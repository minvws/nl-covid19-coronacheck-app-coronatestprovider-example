# COVID-19 CoronaCheck App - Corona Test Provider / Demo Code

## Introduction
This repository contains a very simple CoronaTestProvider that can be used with the CoronaCheck application.

## Security Notice (!!)
This app has 0 security features built it. It has not been designed to run in a production environment.


## Installation Instructions
This application requires a webserver and php-8.0 to run.

It has been tested on linux (ubuntu 20.04) running Apache2, PHP8.0, and MariaDB.

Install packages
```shell
apt install software-properties-common
add-apt-repository ppa:ondrej/php
apt update
apt install apache2 php8.0 libapache2-mod-php8.0 php8.0-mysql 
```

Create database and load data
```sql
CREATE DATABASE CoronaTestProvider;
CREATE TABLE `TestResults` (
   `uuid` varchar(36) COLLATE utf8_bin DEFAULT NULL,
   `testTypeId` varchar(12) COLLATE utf8_bin NOT NULL,
   `verificationCode` varchar(255) COLLATE utf8_bin DEFAULT NULL,
   `sampleDate` datetime DEFAULT NULL,
   `result` int(11) NOT NULL,
   `birthDate` date NOT NULL,
   `token` varchar(255) COLLATE utf8_bin NOT NULL,
   `fetchedCount` int(11) NOT NULL DEFAULT 0,
   `status` varchar(255) COLLATE utf8_bin NOT NULL,
   UNIQUE KEY `token` (`token`),
   UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `TestType` (
    `uuid` varchar(255) COLLATE utf8_bin NOT NULL,
    `name` varchar(255) COLLATE utf8_bin NOT NULL,
    UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
    
INSERT INTO `TestType` VALUES ('pcr','PCR Test - Traditional');
```

Create App configuration
```shell
cp .env.example .env
```

Remember to place CMS Signing certificates into the correct folder mentioned in `.env`.

### Usage

The app contains a `get_test_result` api located at `/ctp/get_test_result` and a web ui to create new test results at `/test_result/create`

The `get_test_result` api is written to conform with the specifications mentioned [here](https://github.com/minvws/nl-covid19-coronacheck-app-coordination/blob/main/docs/providing-test-results.md).

## Development & Contribution process

The development team works on the repository in a private fork (for reasons of compliance with existing processes) and shares its work as often as possible.

If you plan to make non-trivial changes, we recommend to open an issue beforehand where we can discuss your planned changes.
This increases the chance that we might be able to use your contribution (or it avoids doing work if there are reasons why we wouldn't be able to use it).

Note that all commits should be signed using a gpg key.

