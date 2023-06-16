<?php

declare(strict_types=1);

namespace App\Database\Helpers;

use App\Database\Exceptions\DatabaseException;

class DynamoDbUtils
{
    /**
     * @param array<string,string|int> $keys
     * @return mixed[]
     * @throws DatabaseException
     */
    public static function buildGetQuery(string $tableName, array $keys): array
    {
        return [
            'TableName' => $tableName,
            'Key' => self::wrap($keys),
        ];
    }

    /**
     * @param array<string,string|int> $keys
     * @param array<string,mixed> $props
     * @return mixed[]
     * @throws DatabaseException
     */
    public static function buildPutQuery(string $tableName, array $keys, array $props): array
    {
        $query = [
            'TableName' => $tableName,
            'Item' => self::wrap([...$keys, ...$props]),
            'ConditionExpression' => null,
            'ExpressionAttributeNames' => [],
        ];

        $query['ConditionExpression'] = match (count($keys)) {
            1 => 'attribute_not_exists(#hash)',
            2 => 'attribute_not_exists(#hash) AND attribute_not_exists(#range)',
            default => throw new DatabaseException('wrong number of keys'),
        };

        foreach ($keys as $name => $value) {
            $type = $query['ExpressionAttributeNames'] === [] ? '#hash' : '#range';
            $query['ExpressionAttributeNames'][$type] = $name;
        }

        return $query;
    }

    /**
     * @param array<string,string|int> $keys
     * @param array<string,mixed> $props
     * @return mixed[]
     * @throws DatabaseException
     */
    public static function buildUpdateQuery(string $tableName, array $keys, array $props): array
    {
        $query = self::buildBaseUpdateQuery($tableName, $keys);

        $setParts = $removeParts = [];

        foreach (self::wrap($props) as $k => $v) {
            $query['ExpressionAttributeNames']['#' . $k] = $k;

            if (self::isNull($v)) {
                $removeParts[] = "#" . $k;
            } else {
                $setParts[] = sprintf("#%s = :%s", $k, $k);
                $query['ExpressionAttributeValues'][':' . $k] = $v;
            }
        }

        $exp = '';

        if (count($setParts) > 0) {
            $exp .= ' SET ' . implode(', ', $setParts);
        }

        if (count($removeParts) > 0) {
            $exp .= ' REMOVE ' . implode(', ', $removeParts);
        }

        $query['UpdateExpression'] = ltrim($exp);

        return $query;
    }

    /**
     * @return mixed[]
     */
    public static function buildScanQuery(string $tableName, ?string $cursor): array
    {
        $query = [
            'TableName' => $tableName,
        ];

        if ($cursor !== null) {
            $query['ExclusiveStartKey'] = $cursor;
        }

        return $query;
    }

    /**
     * https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_AttributeValue.html
     *
     * @param array<mixed> $item
     *
     * @return array<mixed>
     **/
    public static function unwrap(array $item): array
    {
        $item = array_map(function ($value) {
            if (isset($value['S'])) {
                return (string)$value['S'];
            }

            if (isset($value['N'])) {
                if (false === strpos($value['N'], '.')) {
                    return (int)$value['N'];
                }

                return (float)$value['N'];
            }

            if (isset($value['SS'])) {
                return $value['SS'];
            }

            if (isset($value['BOOL'])) {
                return $value['BOOL'];
            }

            if (isset($value['M'])) {
                return self::unwrap($value['M']);
            }

            if (isset($value['B'])) {
                return $value['B'];
            }

            if (isset($value['NS'])) { // number set
                return array_map(function (string $item) {
                    if (false === strpos($item, '.')) {
                        return (int)$item;
                    }

                    return (float)$item;
                }, $value['NS']);
            }

            if (isset($value['L'])) { // list
                return array_map(function (array $item) {
                    return self::unwrap([$item])[0];
                }, $value['L']);
            }

            if (isset($value['NULL'])) {
                return null;
            }

            $keys = array_keys($value);
            throw new DatabaseException(sprintf("Unknown value type: %s", $keys[0]));
        }, $item);

        return $item;
    }

    /**
     * @param mixed[] $props
     * @return mixed[]
     * @throws DatabaseException
     **/
    public static function wrap(array $props): array
    {
        $result = [];

        foreach ($props as $k => $v) {
            if (is_int($v) || is_float($v)) {
                $result[$k]['N'] = strval($v);
            } elseif (is_bool($v)) {
                $result[$k]['BOOL'] = $v;
            } elseif (is_string($v)) {
                $result[$k]['S'] = $v;
            } elseif (is_array($v)) {
                if (count($v) > 0) {
                    $values = array_values($v);

                    if (!self::isNumericArray($v)) {
                        $result[$k]['M'] = self::wrap($v);
                    } elseif (!is_string($values[0])) {
                        $result[$k]['NS'] = array_values(array_unique(array_map('strval', $v)));
                    } else {
                        $result[$k]['SS'] = array_values(array_unique($v));
                    }
                } else {
                    $result[$k]['M'] = self::wrap($v);
                }
            } elseif (is_null($v)) {
                $result[$k]['NULL'] = true;
            } else {
                throw new DatabaseException('wrong key type');
            }
        }

        return $result;
    }

    /**
     * @param mixed[] $value
     **/
    private static function isNumericArray(array $value): bool
    {
        foreach ($value as $k => $v) {
            if (!is_int($k)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param mixed[] $keys
     * @return mixed[]
     * @throws DatabaseException
     **/
    private static function buildBaseUpdateQuery(string $tableName, array $keys, bool $ifExists = true): array
    {
        $query = [
            'TableName' => $tableName,
        ];

        $keyNames = array_keys($keys);
        $keyValues = array_values($keys);

        if (count($keys) === 2) {
            if ($ifExists) {
                $query['ConditionExpression'] = 'attribute_exists(#hashkey) AND attribute_exists(#rangekey)';
                $query['ExpressionAttributeNames'] = [
                    '#hashkey' => $keyNames[0],
                    '#rangekey' => $keyNames[1],
                ];
            }

            $query['Key'] = self::wrap([
                $keyNames[0] => $keyValues[0],
                $keyNames[1] => $keyValues[1],
            ]);

            return $query;
        } elseif (count($keys) === 1) {
            if ($ifExists) {
                $query['ConditionExpression'] = 'attribute_exists(#hashkey)';
                $query['ExpressionAttributeNames'] = [
                    '#hashkey' => $keyNames[0],
                ];
            }

            $query['Key'] = self::wrap([
                $keyNames[0] => $keyValues[0],
            ]);

            return $query;
        }

        throw new DatabaseException('wrong number of keys');
    }

    /**
     * @param mixed[] $prop
     **/
    private static function isNull(array $prop): bool
    {
        return isset($prop['NULL']);
    }
}
