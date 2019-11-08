<p align="center"><img src="https://github.com/vickzkater/lara-s-cms/raw/master/logo.png" width="200" alt="LARA-S-CMS"></p>

# LARA-S-CMS

[![Build Status](https://travis-ci.org/vickzkater/lara-s-cms.svg?branch=master)](https://travis-ci.org/vickzkater/lara-s-cms)

A PHP Laravel Skeleton for CMS/Admin Dashboard (with website) using Bootstrap 4 Admin Dashboard Template [Gentelella](https://github.com/ColorlibHQ/gentelella)

Developed by [KINIDI Tech](https://kiniditech.com/) ([@vickzkater](https://github.com/vickzkater/)) on September 2019


## DEMO URL

**[DEMO](https://lara-s-cms.kiniditech.com/)**

- Login as Super Admin: `superadmin - admin123`
- Login as User: `user - user123`

## ADMIN URL

Please check `ADMIN_DIR` in `.env` file

## Environment

- [x] PHP 7.2.10
- [x] MySQL 5.0.12-dev - 20150407
- [x] Laravel 5.8.35
- [x] Gentelella 1.4.0

## How to Implement

1. Install the dependencies first
```
composer install
```

2. Copy `.env.example` into `.env` then make some configs in `.env` file (please check `Config .env` Section)
```
cp .env.example .env (for UNIX/Linux/MacOSX)

copy .env.example .env (for Windows)
```

3. Generate application key using command 
```
php artisan key:generate
```

4. Make sure `DB_DATABASE` is set correctly in `.env` file then run migrations to create the structure database and some system data
```
php artisan migrate
```

5. If migration is failed, please run command below first and remove any table(s) in database before execute migrate again
```
composer dump-autoload
```

6. After migration finish, you can open the admin login page by browse to the application URL with addition `/ADMIN_DIR` (based on `.env`)

7. Default admin user is `superadmin` with password `admin123`

## Config .env

- Set `APP_NAME` for application name
- Set `APP_URL` for website URL (front website)
- Set `APP_TIMEZONE` for set timezone application
- Set `APP_VERSION` for set application version
- Set `APP_MAINTENANCE_UNTIL` for set deadline maintenance application using format (Y, m - 1, d)
- Set `MAIL_FROM_NAME` for set sender email's name
- Set `MAIL_FROM_ACCOUNT` for set sender email's account
- Set `MAIL_CONTACT_NAME` for set contact email's name (used for receive email from "contact us" page)
- Set `MAIL_CONTACT_ACCOUNT` for set contact email's account (used for receive email from "contact us" page)
- Set `DISPLAY_SESSION` for display session data in footer web page (development purpose only)
- Set `APP_FAVICON_TYPE` for set application favicon based on file image (input with image's path)
- Set `APP_FAVICON` for set application favicon based on file image (input with image's path)
- Set `APP_LOGO` for application logo based on Font Awesome (input without 'fa-' just the icon name, example: star/laptop/bank)
- Set `APP_LOGO_IMAGE` for application logo based on file image (input with image's path)
- Set `ADMIN_DIR` for application or admin directory name
- Set `POWERED` for display developer name
- Set `POWERED_URL` for display developer URL
- Set `HELP` for display description of application
- Set `DEFAULT_LANGUAGE` for set default language in application
- Set `API_URL` for set API URL (if use it)
- Set `CAPTCHA_SITE_KEY` for set Google reCAPTCHA
- Set `CAPTCHA_SECRET_KEY` for set Google reCAPTCHA
- Set `FACEBOOK_APP_ID` for set Facebook API - Login
- Set `FACEBOOK_APP_SECRET` for set Facebook API - Login
- Set `FACEBOOK_REDIRECT` for set Facebook API Redirect - Login
- Set `GOOGLE_APP_ID` for set Google API - Login
- Set `GOOGLE_APP_SECRET` for set Google API - Login
- Set `GOOGLE_REDIRECT` for set Google API Redirect - Login
- Set `FCM_SERVER_KEY` for set Firebase Push Notification
- Set `FCM_SENDER_ID` for set Firebase Push Notification

## CMS Modules

- [x] Login
- [x] Profile
- [x] User Manager
- [x] Usergroup Manager
- [x] Division
- [x] Branch per Division
- [x] Banner
- [x] Brand
- [x] Product (Purchase Details, QC Task List, Upload Photos, Publish Details, Booking)
- [x] Customer
- [x] Restore Deleted Data
- [x] System Log
- [x] Rule
- [x] Set Access per Module by Usergroup
- [x] Incoming Unit - if product is posted without Sell Price, it is Incoming Unit
- [x] Multi Language
- [x] Custom 404 Error Page
- [x] Set Access modules (User Manager, Usergroup manager, Branch) per Division
- [x] Maintenance Mode


## List of Directories that (Maybe) Need Special Permissions

- /public/uploads/

## Somethings that maybe you must know

- `Helper.php` in `app\Libraries\` that can be called in Controller/View by line code `use App\Libraries\Helper;`
- `CustomFunction.php` in `app\Libraries\` that automatically called in the load of web because it has been set in `composer.json`

## Maintenance Mode

**Enable Maintenance Mode**
```
php artisan down
```
**Disable Maintenance Mode (go to live again)**
```
php artisan up
```

Even while in maintenance mode, specific IP addresses or networks may be allowed to access the application using the command
```
php artisan down --allow=127.0.0.1 --allow=192.168.0.0/16
```

Source: [Laravel Documentations](https://laravel.com/docs/6.x/configuration#maintenance-mode)

There is custom page for maintenance mode in `resources/views/errors/503.blade.php`
