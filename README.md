## About

This app only for serve REST API Using Laravel 10, this app is build like instagram model flow.

# Features

- Auth
 - Register
 - Login
- User
 - GET Profile
 - Update Profile
 - Followers / follows Detail
 - Search User
 - Follow / Unfollow
- Post / Story
 - Create story
  - Multiple Images, Single Caption
 - Delete story
 - Like / Unlike
 - Comments
  - 2 level indent, I use this concept to limit the indentation of hierarchy level from comments


# Postman documentation

Postman details API
```
https://documenter.getpostman.com/view/4929641/2s9Ye8faUq
```

## Instalation

clone this repo
```
git clone https://github.com/adepane/sosmed.git "yourdirname"
```

```
cd yourdirname
```

install all vendor
```
composer install
```

After you done with it, now please create new key
```
php artisan key:generate
```

Before access the installer, purge the all configuration
```
php artisan optimize:clear
```

To run it in the browser, you can use valet / herd like `sosmed.test`, or if you don't have valet installed, you run development serve like so
```
php artisan serve
```

## Preparing DB
change the .env database to your's in section below
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sosmed
DB_USERNAME=root
DB_PASSWORD=
```

Install Passport
```
php artisan passport:install
```

Migrate all Database
```
php artisan migrate
```

## Change Passport Client ID Key in .env

change the .env for PASSPORT CLIENT in section below:
```
PASSPORT_CLIENT_ID=""
PASSPORT_SECRET=""
```
