# Rock Paper Scissors REST API

A Rock Paper Scissors game exposed as a REST API, built with PHP and Symfony.

## Prerequisites

- PHP >= 8.2
- [Composer](https://getcomposer.org/)
- [Symfony CLI](https://symfony.com/download) (optional, for the dev server)

### Install prerequisites (macOS)

```bash
brew install php composer symfony-cli/tap/symfony-cli
```

## Install

```bash
composer install
```

## Run

```bash
# Using Symfony CLI
symfony server:start

# Or using PHP's built-in server
php -S localhost:8000 -t public/
```
