# Introduction

An example project based on Laravel 10.* for interview testing.

# Installation

```shell
cp .evn.example .env

docker compose up -d

docker compose exec fpm bash -c 'composer install'
docker compose exec fpm bash -c 'php artisan key:generate'
docker compose exec fpm bash -c 'php artisan migrate --seed'

# Building frontend assets
docker run -it --rm -v ${PWD}:/app node:20.18.0 sh -c 'cd /app && npm install && npm run build' 
```

# Usage

The project comes with preloaded data populated through Seeders. You can log in using the account below to verify the functionality directly:

* Email: echo@example.com Password: echo
* Email: george@example.com Password: george

# Screenshot

![User Authentication](/images/interview-01.png)

![User Dashboard](/images/interview-02.png)

![SQL Executor](/images/interview-03.png)

![User Management](/images/interview-04.png)