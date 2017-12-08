## Installation

cp docker-compose.yml.dist docker-compose.yml
docker-compose up -d
dcc php
php app/console assets:install httpdocs

## Building release

bin/build

## Testing

    bin/phpunit -c app

### Behat

See [behat3](http://docs.behat.org/en/latest/) for reference.
To run behat tests:

    bin/reload test
    bin/behat

### Reload

**reload** - reloads your application datasources in the order: drop database(if available), create database, run migrations,
load fixtures, clear and warmup cache. These binaries are located in **app/Resources/bin** and may be adapted
for custom usage.

    bin/reload test

Would reload application for **test** environment. Default is **dev** as usual in symfony2 application.

