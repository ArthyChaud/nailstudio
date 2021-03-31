# Nailstudio

Website for the NailStudio shop

## Prerequisites

### Tools

+ PHP 7.4 or above
+ Symfony 4 or above
+ Composer 2.0 or above
+ MySQL or MariaDB
+ a web server

### PHP extensions

+ ext
+ openssl

## Set up 

### Dependencies

```shell
composer update
```

### SQL database

Enter your database url in the `.env` or `.env.local` file like this :

```dotenv
DATABASE_URL="mysql://[USER]:@[IP]:[PORT]/[DATABASE NAME]"
```

## Schemas

To update schemas, you can run `Schema.bat` or `Schema.sh` depends on your system.

## Migrations

To make migrations, you can run `Migrations.bat` or `Migrations.sh` depends on your system.

## Authors

+ Maxime NOEL
+ Thomas OBRECHT
+ Arthur BEAUDOIN
