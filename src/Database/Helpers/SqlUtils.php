<?php

declare(strict_types=1);

namespace App\Database\Helpers;

use App\Database\Exceptions\DatabaseException;

class SqlUtils
{
    /**
     * Build an SQL WHERE condition using the DynamoDB friendly syntax, example:
     *
     * [
     *  '#user = :userName' => [
     *   ':userName' => 'test',
     *   '#user' => 'user',  // field alias for escaping
     *  ],
     * ]
     *
     * @param array<mixed> &$params
     * @param array<mixed> $where
     * @throws DatabaseException
     **/
    public static function buildAltWhere(array &$params, array $where): string
    {
        if (count($where) <= 0) {
            return '';
        }

        $fieldMap = [];
        $conditions = [];

        foreach ($where as $k => $v) {
            if (!is_array($v)) {
                throw new DatabaseException('malformed condition');
            }

            foreach ($v as $_k => $_v) {
                if ($_k[0] === '#') {
                    // Field alias.
                    $fieldMap[$_k] = sprintf("`%s`", $_v);
                } else {
                    // Regular param.  Just escape the field name.
                    $conditions[] = self::escapeCondition($k);
                    $params[$_k] = $_v;
                }
            }
        }

        $query = ' WHERE ' . implode(' AND ', $conditions);

        foreach ($fieldMap as $src => $dst) {
            $query = str_replace($src, $dst, $query);
        }

        return $query;
    }

    /**
     * @param array<mixed> $keys
     *
     * @return array{string, array<mixed>}
     * @throws DatabaseException
     **/
    public static function buildDelete(string $tableName, array $keys): array
    {
        $params = [];
        $query = sprintf("DELETE FROM `%s`", $tableName);
        $query .= self::buildAltWhere($params, $keys);

        return [$query, $params];
    }

    /**
     * @param array<mixed> $keys
     * @param array<mixed> $props
     * @return array{string, array<mixed>}
     * @throws DatabaseException
     **/
    public static function buildIncrement(string $tableName, array $keys, array $props): array
    {
        $_set = [];
        $_params = [];

        foreach ($props as $k => $v) {
            $_set[] = sprintf("`%s` = COALESCE(`%s`, 0) + :%s", $k, $k, $k);
            $_params[sprintf(':%s', $k)] = $v;
        }

        $_set = implode(", ", $_set);

        $query = sprintf("UPDATE `%s` SET %s", $tableName, $_set);

        if (count($keys) > 0) {
            $query .= self::buildAltWhere($_params, $keys);
        }

        return [$query, $_params];
    }

    /**
     * @param array<mixed> $props
     * @return array{string, array<mixed>}
     **/
    public static function buildInsert(string $tableName, array $props): array
    {
        $_fields = [];
        $_marks = [];
        $_params = [];

        foreach ($props as $k => $v) {
            $f = self::extractFieldName($k);
            $_fields[] = sprintf("`%s`", $f);
            $_params[sprintf(':%s', $f)] = $v;
            $_marks[] = sprintf(':%s', $f);
        }

        $_fields = implode(", ", $_fields);
        $_marks = implode(", ", $_marks);

        $query = sprintf("INSERT INTO `%s` (%s) VALUES (%s)", $tableName, $_fields, $_marks);

        return [$query, $_params];
    }

    /**
     * @param array<mixed> $fields
     * @param array<mixed> $where
     * @return array{string, array<mixed>}
     * @throws DatabaseException
     **/
    public static function buildSelect(string $tableName, array $fields, array $where): array
    {
        $_fields = array_map(function (string $name): string {
            return sprintf("`%s`", $name);
        }, $fields);

        if (count($_fields) === 0) {
            $_fields[] = '*';
        }

        $params = [];
        $query = "SELECT " . implode(", ", $_fields);
        $query .= sprintf(" FROM `%s`", $tableName);
        $query .= self::buildAltWhere($params, $where);

        return [$query, $params];
    }

    /**
     * @param array<mixed> $where
     * @return array{string, array<mixed>}
     * @throws DatabaseException
     **/
    public static function buildSelectCount(string $tableName, array $where): array
    {
        $params = [];
        $query = sprintf("SELECT COUNT(1) AS `count` FROM `%s`", $tableName);
        $query .= self::buildAltWhere($params, $where);

        return [$query, $params];
    }

    /**
     * Build an UPDATE query using a regular k-v props syntax.
     *
     * @param array<mixed> $where
     * @param array<mixed> $props
     * @return array{string, array<mixed>}
     * @throws DatabaseException
     **/
    public static function buildUpdate(string $tableName, array $where, array $props): array
    {
        $_set = [];
        $_params = [];

        foreach ($props as $k => $v) {
            $fn = self::extractFieldName($k);
            $_set[] = sprintf("`%s` = :%s", $k, $fn);
            $_params[sprintf(':%s', $fn)] = $v;
        }

        $_set = implode(", ", $_set);

        $query = sprintf("UPDATE `%s` SET %s", $tableName, $_set);
        $query .= self::buildAltWhere($_params, $where);

        return [$query, $_params];
    }

    private static function escapeCondition(string $condition): string
    {
        $parts = explode(' ', $condition, 3);

        if (!str_starts_with(trim($parts[0]), '#')) {
            $parts[0] = sprintf('`%s`', trim($parts[0]));
        }

        return implode(' ', $parts);
    }

    private static function extractFieldName(string $condition): string
    {
        $parts = explode(' ', $condition, 3);
        return trim($parts[0]);
    }
}
