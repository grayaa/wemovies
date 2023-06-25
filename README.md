# Symfony Movie App

This is a simple Symfony 5.4 application that uses the Movie Database API to display movies information.

## API Documentation

You can find the documentation for the Movie Database API at the following link:
[Movie Database API Documentation](https://developer.themoviedb.org/reference/intro/getting-started)

## Prerequisites

Before running the Symfony app, make sure you have the following:

- PHP 7.4 or higher
- Composer
- [Symfony CLI](https://symfony.com/download)
## Installation

1. Clone the repository:

```bash
git clone https://github.com/grayaa/wemovies.git
```
2. Install the dependencies using Composer: => use the `composer` binary installed
   on your computer to run these commands [Download Composer](https://getcomposer.org/):

```bash
cd wemovies/
composer install
```

3. Set up the .env file in root directory:

```bash
cp .env.example .env
```
4. Launch the Symfony local server, use the `symfony` binary installed
   on your computer to run this command [Symfony CLI](https://symfony.com/download):

```bash
symfony server:start
```
=== Alternatively you can use default php server ===
```bash
php -S localhost:8000 -t public/
```
Then access the application in your browser at the given URL (http://localhost:8000 by default).