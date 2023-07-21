This is an API for a simple private wiki.  Details TBD.

[![test](https://github.com/umonkey/torch-api/actions/workflows/tests.yml/badge.svg)](https://github.com/umonkey/torch-api/actions/workflows/tests.yml)
[![codecov](https://codecov.io/gh/umonkey/torch-api/branch/master/graph/badge.svg?token=RX0QCDYEB4)](https://codecov.io/gh/umonkey/torch-api)


## Features

- Private access only.
- Data stored in SQLite or [Amazon DynamoDB][1].
- Markdown formatting, no special syntax.
- Page id differs from page title.


## TODO

- [ ] DynamoDB: only update entity props that were changed, not all.
- [ ] Database boostraping code, create user etc.
- [ ] DynamoDB: database setup code, permissions, etc.
- [ ] Public read-only access.
- [ ] Database: add support for Google Bigtable.

[1]: https://github.com/umonkey/torch-api/wiki/Using-DynamoDB
