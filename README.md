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
2. Install the dependencies using Composer:

```bash
cd wemovies/
composer install
```

3. Set up the .env file in root directory:

```bash
cp .env.example .env
```
4. Launch the Symfony local server:

```bash
symfony server:start
```