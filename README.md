<p align="center"><img src="https://hosting.kiniditech.com/lara-s-cms_logo.png" width="200" alt="LARA-S-CMS"></p>

# LARA-S-CMS

<p align="center">
<a href="https://travis-ci.org/vickzkater/lara-s-cms" target="_blank"><img src="https://travis-ci.org/vickzkater/lara-s-cms.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/vickzkater/lara-s-cms" target="_blank"><img class="latest_stable_version_img" src="https://poser.pugx.org/vickzkater/lara-s-cms/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/vickzkater/lara-s-cms" target="_blank"><img class="total_img" src="https://poser.pugx.org/vickzkater/lara-s-cms/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/vickzkater/lara-s-cms" target="_blank"><img class="license_img" src="https://poser.pugx.org/vickzkater/lara-s-cms/license" alt="License"></a>
</p>

A PHP Laravel Skeleton for Content Management System (CMS) or Admin Dashboard (within/without website) using Bootstrap 4 Admin Dashboard Template [Gentelella](https://github.com/ColorlibHQ/gentelella) as Admin Template.

For sample as website, we are using [Business Casual](https://startbootstrap.com/themes/business-casual/) a free Bootstrap 4 website template

Developed by [@vickzkater](https://github.com/vickzkater/) (Powered by [KINIDI Tech](https://kiniditech.com/)) on September 2019


## DEMO URL

**COMING SOON**

### Login details
```
Username: superadmin
Password: admin123
```

## Version

 Laravel  | Lara-S-CMS
:---------|:----------
 5.8.x    | 1.0.x
 6.x.x    | 1.1.1

## Requirements

- PHP >= 7.2
- MySQL 5.0.12-dev - 20150407
- [Laravel 5.8.36](https://github.com/laravel/laravel)

## Installing Lara-S-CMS

Lara-S-CMS utilizes [Composer](http://getcomposer.org/) to manage its dependencies. So, before using Lara-S-CMS, make sure you have Composer installed on your machine.

### Composer Create-Project

You may also install Lara-S-CMS by issuing the Composer `create-project` command in your terminal:
```
composer create-project vickzkater/lara-s-cms --prefer-dist website
```

### Setup

After creating the project move to the project root folder eg: `cd website` and run the command to set up database and configuration files (if key is not generated while installing).

```
php artisan key:generate
```

**Application Key**

The next thing you should do after installing Lara-S-CMS is set your application key to a random string. If you installed Lara-S-CMS via Composer or the Lara-S-CMS installer, this key has already been set for you by the `key:generate` command. Typically, this string should be 32 characters long. The key can be set in the `.env` environment file. If you have not renamed the .env.example file to .env, you should do that now. **If the application key is not set, your user sessions and other encrypted data will not be secure!**

Next, setup environment configuration in `.env` file

- Set `APP_NAME` for application name
- Set `APP_BACKEND` for choose application back-end mode (MODEL or API)
- Set `DISPLAY_SESSION` for display session data (debugging) in footer admin web page (development purpose only)
- Set `APP_URL_SITE` for set website URL (public website), if any
- Set `APP_URL_MAIN` for set main website URL, if this project is a microsite
- Set `APP_URL_API` for set API URL, if this project using back-end mode: API
- Set `APP_TIMEZONE` for set timezone application, sample: UTC or Asia/Jakarta
- Set `APP_VERSION` for set application version
- Set `APP_MAINTENANCE_UNTIL` for set deadline maintenance application using format (Y, m - 1, d)
- Set `ADMIN_DIR` for set application or admin system directory name (or leave it blank if using the admin dashboard only)
- Set `DEFAULT_LANGUAGE` for set default language in application
- Set `APP_FAVICON_TYPE` for set favicon type (ico/png/etc)
- Set `APP_FAVICON` for set application favicon based on file image (input with image's path), sample: the favicon file is in public/images directory path, then set 'images/favicon.ico'
- Set `APP_LOGO` for set application logo based on Font Awesome (input without 'fa-' just the icon name, example: star/laptop/bank)
- Set `APP_LOGO_IMAGE` for set application logo based on file image (input with image's path), sample: the logo image is in public/images directory path, then set 'images/logo.png'
- Set `HELP` for set description of application
- Set `POWERED` for display developer name
- Set `POWERED_URL` for display developer URL
- Set `META_KEYWORDS` for set meta keywords
- Set `META_TITLE` for set meta title
- Set `META_DESCRIPTION` for set meta description
- Set `META_AUTHOR` for set meta author

- Set `MAIL_FROM_NAME` for set sender email's name
- Set `MAIL_FROM_ADDRESS` for set sender email's address
- Set `MAIL_REPLYTO_NAME` for set reply-to email's name
- Set `MAIL_REPLYTO_ADDRESS` for set reply-to email's address
- Set `MAIL_CONTACT_NAME` for set contact email's name (used for receive email from "contact us" page)
- Set `MAIL_CONTACT_ADDRESS` for set contact email's address (used for receive email from "contact us" page)

- Set `RECAPTCHA_SITE_KEY` for set GOOGLE reCAPTCHA
- Set `RECAPTCHA_SECRET_KEY` for set GOOGLE reCAPTCHA
- Set `RECAPTCHA_SITE_KEY_ADMIN` for set GOOGLE reCAPTCHA in Admin Dashboard
- Set `RECAPTCHA_SECRET_KEY_ADMIN` for set GOOGLE reCAPTCHA in Admin Dashboard

- Set `GOOGLE_CLIENT_MODULE` for enable/disable GOOGLE API Authentication
- Set `GOOGLE_CLIENT_ID` for set GOOGLE API Authentication
- Set `GOOGLE_CLIENT_SECRET` for set GOOGLE API Authentication
- Set `GOOGLE_CALLBACK_URL` for set GOOGLE API Authentication Callback URL

- Set `FACEBOOK_CLIENT_MODULE` for enable/disable FACEBOOK API Authentication
- Set `FACEBOOK_CLIENT_ID` for set FACEBOOK API Authentication
- Set `FACEBOOK_CLIENT_SECRET` for set FACEBOOK API Authentication
- Set `FACEBOOK_CALLBACK_URL` for set FACEBOOK API Authentication Callback URL

- Set `TWITTER_CLIENT_MODULE` for enable/disable TWITTER API Authentication
- Set `TWITTER_CLIENT_ID` for set TWITTER API Authentication
- Set `TWITTER_CLIENT_SECRET` for set TWITTER API Authentication
- Set `TWITTER_CALLBACK_URL` for set TWITTER API Authentication Callback URL

- Set `LINKEDIN_CLIENT_MODULE` for enable/disable LINKEDIN API Authentication
- Set `LINKEDIN_CLIENT_ID` for set LINKEDIN API Authentication
- Set `LINKEDIN_CLIENT_SECRET` for set LINKEDIN API Authentication
- Set `LINKEDIN_CALLBACK_URL` for set LINKEDIN API Authentication Callback URL

- Set `FCM_SERVER_KEY` for set Firebase Push Notification
- Set `FCM_SENDER_ID` for set Firebase Push Notification

### Database Setup

**You must run the database migration for running this application.**

Make sure `DB_DATABASE` is set correctly in `.env` file then run migrations to create the structure database and some system data
```
php artisan migrate
```

After migration finish run the command `php artisan serve` or browse the link to view the admin login page (application URL with addition `/ADMIN_DIR` - based on `.env`).

```
http://path-to-project-folder/public/manager
```

<p align="center"><img src="https://hosting.kiniditech.com/lara-s-cms_loginpage_v1.1.0.jpg" alt="LARA-S-CMS Login Page"></p>

### Login details (default)

**Administrator**
```
Username: superadmin
Password: admin123
```

## Features

- [x] Support Multi Languages
- [x] Admin Login
- [x] My Profile
- [x] Division Management
- [x] Branch per Division Management
- [x] Rule Management
- [x] Usergroup Management
- [x] User Management
- [x] Access/Privilege Management
- [x] System Logs
- [x] Restore Deleted Data
- [x] Custom 404 Error Page
- [x] Custom Maintenance Mode
- [x] Product Management

<p align="center"><img src="https://hosting.kiniditech.com/lara-s-cms_modules_v1.1.0.jpg" alt="LARA-S-CMS Modules"></p>

## Configurations

### Basic Configurations

**Directory Permissions**

After installing Lara-S-CMS, you may need to configure some permissions. Directories within the `storage` and the `bootstrap/cache` directories should be writable by your web server. If you are using the Homestead virtual machine, these permissions should already be set.

And with additionally configure the permission for directory `public/uploads/`. So upload photos feature in Product Module can work well.
```
chmod o+w -R public/uploads/
```

**Somethings that maybe you must know**

- `CustomFunction.php` in `app\Libraries\` that automatically called in the load of web because it has been set in `composer.json`
- `Helper.php` in `app\Libraries\` that can be called in Controller/View by line code `use App\Libraries\Helper;` for call some helper functions


## Maintenance Mode

When your application is in maintenance mode, a custom view will be displayed for all requests into your application. This makes it easy to "disable" your application while it is updating or when you are performing maintenance. A maintenance mode check is included in the default middleware stack for your application. If the application is in maintenance mode, an HttpException will be thrown with a status code of 503.

To enable maintenance mode, simply execute the `down` Artisan command:
```
php artisan down
```
To disable maintenance mode, use the `up` command:
```
php artisan up
```

Even while in maintenance mode, specific IP addresses or networks may be allowed to access the application using the command
```
php artisan down --allow=127.0.0.1 --allow=192.168.0.0/16
```

Source: [Laravel Documentations](https://laravel.com/docs/6.x/configuration#maintenance-mode)

<p align="center"><img src="https://hosting.kiniditech.com/lara-s-cms_maintenance.jpg" alt="LARA-S-CMS"></p>

### Maintenance Mode Response Template

The default template for maintenance mode responses is located in `resources/views/errors/503.blade.php` and `public/maintenance/`

## Packages Used (Outside of Laravel)
- [yajra/laravel-datatables-oracle](https://github.com/yajra/laravel-datatables) - used to display a list of data in a table

## Documentation

Coming soon

## Contributing

Thank you for considering contributing to the Lara-S-CMS.

## Bugs, Improvements & Security Vulnerabilities

If you discover a bug or security vulnerability within Lara-S-CMS, please send an email to Vicky Budiman at [vicky@kiniditech.com](mailto:vicky@kiniditech.com). All requests will be addressed promptly.

## Issues

If you come across any issue/bug please [report them here](https://github.com/vickzkater/lara-s-cms/issues).

## License

Lara-S-CMS is open-sourced software built by KINIDI Tech and contributors and licensed under the [MIT license](http://opensource.org/licenses/MIT).

## Credits

- Vicky Budiman (https://github.com/vickzkater)
- Laravel (https://github.com/laravel/laravel)
- ColorlibHQ (https://github.com/ColorlibHQ/gentelella)
- Start Bootstrap (https://startbootstrap.com/)

<p align="center">Brought to you by</p>
<p align="center"><img src="https://hosting.kiniditech.com/kiniditech_logo.png" width="200" alt="KINDI Tech"></p>
<p align="center">KINIDI Tech</p>
