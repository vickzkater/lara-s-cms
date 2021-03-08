<p align="center"><img src="https://hosting.kiniditech.com/lara-s-cms_logo.png" width="200" alt="LARA-S-CMS"></p>

# LARA-S-CMS

<p align="center">
<a href="https://travis-ci.org/vickzkater/lara-s-cms" target="_blank"><img src="https://travis-ci.org/vickzkater/lara-s-cms.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/vickzkater/lara-s-cms" target="_blank"><img class="latest_stable_version_img" src="https://img.shields.io/packagist/v/vickzkater/lara-s-cms" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/vickzkater/lara-s-cms" target="_blank"><img class="total_img" src="https://img.shields.io/packagist/dt/vickzkater/lara-s-cms" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/vickzkater/lara-s-cms" target="_blank"><img class="license_img" src="https://img.shields.io/packagist/l/vickzkater/lara-s-cms" alt="License"></a>
</p>

***Latest Version: 2.0.7 (Laravel 7.30.4)**

## What is "Lara-S-CMS" ?

A PHP Laravel Skeleton for Content Management System (CMS) or Admin Dashboard (within/without website) using Bootstrap 4 Admin Dashboard Template [Gentelella](https://github.com/ColorlibHQ/gentelella) as Admin Template.

For sample as website, we are using [Modern Business](https://startbootstrap.com/templates/modern-business/) a free Bootstrap 4 website template

Laravel (S) Content Management System
- Skeleton 💀
- Simple 😃
- Sample 🤓
- Standard 💯
- Smart 🧠
- Sophisticated 💡
- SUPER 💪
- Sucks? 💢
- Spinner 🤣

Developed by [@vickzkater](https://github.com/vickzkater/) (Powered by [KINIDI Tech](https://kiniditech.com/)) since September 2019

## Features

- [x] Support Multi Languages
- [x] Admin Login
- [x] My Profile
- [x] Office/Subsidiary Management
- [x] Branch per Office Management
- [x] Rule Management
- [x] Usergroup Management
- [x] User (Admin) Management
- [x] Access/Privilege/User Roles Management
- [x] Simple System Logs
- [x] Restore Deleted Data
- [x] Custom 404 Error Page
- [x] Custom Maintenance Mode
- [x] Product Management (as module sample including upload image feature)
- [x] Support DataTables AJAX
- [x] Support reCAPTCHA v2 (optional for Admin Panel & User Panel)
- [x] Sortable Data List (in Banner Module)
- [x] Sortable Data List with Filter (in Branch Module - Filter by Division)
- [x] Application Configurations
- [x] Import & Export Excel File (in Product Module)
- [x] Delete Uploaded Image (in Product Module)
- [x] Rich Text Editor/WYSIWYG using TinyMCE (in Product Module)
- [x] Datepicker (in Product Module)
- [x] [The Helper PHP - a lot of PHP helper functions that are ready to help in your project](https://github.com/vickzkater/the-helper-php)
- [x] [Support GoSMSGateway API - send SMS (in DevController)](https://github.com/vickzkater/gosms-laravel)
- [x] [Support Mailchimp API (in DevController)](https://github.com/vickzkater/mailchimp-helper-laravel)
- [x] [PageBuilder](https://github.com/vickzkater/kiniditech-pagebuilder) (in Article)
- [x] [The Helper JS - a lot of JS helper functions that are ready to help in your project](https://github.com/vickzkater/the-helper-js)
- [x] Banner Management
- [x] QR Code Generator
- [x] Login with social media (Google/Facebook)
- [x] Support back-end mode (MODEL or API)
- [x] Support upload file (PDF/TXT/DOCS/etc)
- [x] Support Session Driver Database (please check section `Session Driver Database`)
- [x] Security update: if password has been changed, then force user to re-login
- [x] Feature logout from all sessions
- [x] Sample function sending email & email template (support HTML & Plain Text)
- [x] Generate thumbnail (in Article Module for saving thumbnail)
- [x] [Support One Way SMS Gateway API - send SMS (in DevController)](https://github.com/vickzkater/onewaysms-laravel)
- [x] Add support Login with Instagram - to use it read [Instagram's Official Guide](https://developers.facebook.com/docs/instagram-basic-display-api/getting-started)
- [x] Setup Open Graph configurations (based on [ogp.me](https://ogp.me/), [Twitter Dev Docs](https://developer.twitter.com/en/docs/twitter-for-websites/cards/overview/markup), & [FB Dev Docs](https://developers.facebook.com/docs/sharing/webmasters/))
- [x] Guzzle-Client helper functions (please check on Controller.php)

## Admin Panel

<p><img src="https://hosting.kiniditech.com/lara-s-cms_modules_v1.2.2.jpg" alt="LARA-S-CMS Modules" width="700"></p>

## Website

<p><img src="https://hosting.kiniditech.com/lara-s-cms_website_v1.2.3.jpg" alt="LARA-S-CMS Website" width="700"></p>

## Version

 Laravel  | Lara-S-CMS
:---------|:----------
 5.8.x    | 1.0 ; 1.1.0
 6.x      | 1.0.1 ; 1.1.1
 7.x      | 1.2.x ; 2.x

## Requirements

- PHP >= 7.3
- [Laravel 7.x Requirements](https://laravel.com/docs/7.x/installation#server-requirements)

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
- Set `DISPLAY_SESSION` for enable/disable display session in Admin - Footer (Development Purpose)

- Set `APP_MODE` for set application mode (STAGING/LIVE)
- Set `APP_VERSION` for set application version
- Set `APP_BACKEND` for choose application back-end mode (MODEL or API) if use API, please make sure `APP_URL_API` is not empty
- Set `ADMIN_CMS` for enable/disable Admin Panel
- Set `ADMIN_DIR` for set application or admin system directory name (or leave it blank if using the admin dashboard only)

- Set `APP_URL_SITE` for set application URL that used for login with social media
- Set `APP_URL_API` for set API URL, if this project using back-end mode API (`APP_BACKEND`=API)

- Set `API_USER` for set API auth credential (optional)
- Set `API_PASS` for set API auth credential (optional)

- Set `APP_TIMEZONE` for set timezone application, sample: UTC (GMT) or Asia/Jakarta (GMT+7) or Asia/Kuala_Lumpur (GMT+8)
- Set `APP_MAINTENANCE_UNTIL` for set deadline maintenance application using format (Y, m - 1, d)

- Set `MULTILANG_MODULE` for enable/disable multi languages module in application
- Set `DEFAULT_LANGUAGE` for set default language in application

- Set `META_DESCRIPTION` for set meta description
- Set `META_AUTHOR` for set meta author

- Set `APP_FAVICON_TYPE` for set favicon type (ico/png/etc)
- Set `APP_FAVICON` for set application favicon based on file image (input with image's path), sample: the favicon file is in public/images directory path, then set 'images/favicon.ico'

- Set `APP_LOGO` for set application logo based on Font Awesome (input without 'fa-' just the icon name, example: star/laptop/bank)
- Set `APP_LOGO_IMAGE` for set application logo based on file image (input with image's path), sample: the logo image is in "public/images" directory path, then set "images/logo.png"

- Set `POWERED` for display developer name
- Set `POWERED_URL` for display developer URL

- Set `MAIL_MODULE` for enable/disable Mail Module

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

- Set `AUTH_WITH_PROVIDER` for enable/disable login with social media/provider

- Set `GOOGLE_CLIENT_MODULE` for enable/disable GOOGLE API Authentication
- Set `GOOGLE_CLIENT_ID` for set GOOGLE API Authentication
- Set `GOOGLE_CLIENT_SECRET` for set GOOGLE API Authentication
- Set `GOOGLE_CALLBACK_URL` for set GOOGLE API Authentication Callback URL

- Set `FACEBOOK_CLIENT_MODULE` for enable/disable FACEBOOK API Authentication
- Set `FACEBOOK_CLIENT_ID` for set FACEBOOK API Authentication
- Set `FACEBOOK_CLIENT_SECRET` for set FACEBOOK API Authentication
- Set `FACEBOOK_CALLBACK_URL` for set FACEBOOK API Authentication Callback URL

- Set `INSTAGRAM_CLIENT_MODULE` for enable/disable INSTAGRAM API Authentication
- Set `INSTAGRAM_CLIENT_ID` for set INSTAGRAM API Authentication
- Set `INSTAGRAM_CLIENT_SECRET` for set INSTAGRAM API Authentication
- Set `INSTAGRAM_CALLBACK_URL` for set INSTAGRAM API Authentication Callback URL

- Set `FCM_SERVER_KEY` for set Firebase Push Notification
- Set `FCM_SENDER_ID` for set Firebase Push Notification

- Set `SMS_MODULE` for enable/disable SMS Module

- Set `GOSMS_AUTH_USER` for set GoSMSGateway credentials
- Set `GOSMS_AUTH_PASS` for set GoSMSGateway credentials

- Set `ONEWAYSMS_API` for set OnewaySMS Gateway API URL
- Set `ONEWAYSMS_AUTH_USER` for set OnewaySMS Gateway credentials
- Set `ONEWAYSMS_AUTH_PASS` for set OnewaySMS Gateway credentials

- Set `MC_KEY` for set Mailchimp API key
- Set `MC_LIST_ID` for set Mailchimp List ID
- Set `MC_TAG_DEFAULT` for set Mailchimp Tag ID as default

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

<p><img src="https://hosting.kiniditech.com/lara-s-cms_loginpage_v1.2.2.jpg" alt="LARA-S-CMS Login Page" width="500"></p>

### Login details (default)

**Administrator**
```
Username: superuser
Password: sudo123!
```

## Configurations

### Basic Configurations

**Directory Permissions**

After installing Lara-S-CMS, you may need to configure some permissions. Directories within the `storage` and the `bootstrap/cache` directories should be writable by your web server. If you are using the Homestead virtual machine, these permissions should already be set.

And with additionally configure the permission for directory `public/uploads/`. So upload photos feature in Product Module can work well.
```
chmod o+w -R public/uploads/
```

***If after setup all configs, but still display error "500 Internal Server Error"**

Comment first line in `.htaccess` for fix this issue
```
#Header always set Content-Security-Policy: upgrade-insecure-requests

<IfModule mod_rewrite.c>
   RewriteEngine On 
   RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

_*) This issue is reported only occur on some hosting servers, e.g. AWS (Amazon Web Service)_

***For your information**

- `CustomFunction.php` in `app\Libraries\` that automatically called in the load of web because it has been set in `composer.json`
- `Helper.php` in `app\Libraries\` that can be called in Controller/View by line code `use App\Libraries\Helper;` for call some helper functions

## IMPORTANT NOTE!

Please set `APP_DEBUG` to `false` on Production to disable [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar).

## Session Driver Database

When using the `database` session driver, you will need to create a table to contain the session items. Below is an example `Schema` declaration for the table:
```
Schema::create('sessions', function ($table) {
    $table->string('id')->unique();
    $table->foreignId('user_id')->nullable();
    $table->string('ip_address', 45)->nullable();
    $table->text('user_agent')->nullable();
    $table->text('payload');
    $table->integer('last_activity');
});
```

You may use the `session:table` Artisan command to generate this migration:
```
php artisan session:table

php artisan migrate
```

Then you need make some changes in `Illuminate\Session\DatabaseSessionHandler.php`
```
...
protected function addUserInformation(&$payload)
{
    if ($this->container->bound(Guard::class)) {
        $payload['user_id'] = $this->userId();
    }

    // ADDED FOR LARA-S-CMS BY KINIDI TECH - BEGIN
    if(\Session::has('admin')){
        $larascms_user = \Session::get('admin');
        $payload['user_id'] = $larascms_user->id;
    }
    // ADDED FOR LARA-S-CMS BY KINIDI TECH - END

    return $this;
}
...
```

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

Source: [Laravel Documentations](https://laravel.com/docs/7.x/configuration#maintenance-mode)

<p><img src="https://hosting.kiniditech.com/lara-s-cms_maintenance_v1.2.2.jpg" alt="LARA-S-CMS Maintenance Mode"  width="700"></p>

### Maintenance Mode Response Template

The default template for maintenance mode responses is located in `resources/views/errors/503.blade.php` and `public/maintenance/`

## Packages Used (Outside of Laravel)

- [yajra/laravel-datatables-oracle](https://github.com/yajra/laravel-datatables) - used to display a list of data in a table
- [maatwebsite/excel](https://github.com/Maatwebsite/Laravel-Excel) - used to export & import Excel data
- [guzzlehttp/guzzle](https://github.com/guzzle/guzzle) - used to send HTTP requests and trivial to integrate with web services
- [nztim/mailchimp](https://github.com/nztim/mailchimp) - used to Mailchimp API
- [simplesoftwareio/simple-qrcode](https://github.com/SimpleSoftwareIO/simple-qrcode) - used to generate QR code
- [laravel/socialite](https://github.com/laravel/socialite) - used to login with social media
- [intervention/image](https://github.com/Intervention/image) - used to generate thumbnail image
- [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) -  used to development

## Libraries Used

- [The Helper PHP - a lot of PHP helper functions that are ready to help in your project](https://github.com/vickzkater/the-helper-php)
- [The Helper JS - a lot of JS helper functions that are ready to help in your project](https://github.com/vickzkater/the-helper-js)
- [GoSMSGateway API - Laravel Library (GoSms)](https://github.com/vickzkater/gosms-laravel)
- [Mailchimp API Helper - Laravel Library (MailchimpHelper)](https://github.com/vickzkater/mailchimp-helper-laravel)
- [PageBuilder (Build pages using content elements)](https://github.com/vickzkater/kiniditech-pagebuilder)
- [One Way SMS Gateway API - Laravel Library (OnewaySms)](https://github.com/vickzkater/onewaysms-laravel)

## Lara-S-CMS has been featured on

- PHP Weekly - [phpweekly.com](http://www.phpweekly.com/archive/2020-08-13.html)

<p><img src="https://hosting.kiniditech.com/lara-s-cms_on_phpweeklydotcom.png?v=2" alt="LARA-S-CMS on PHP Weekly" width="400"></p>

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
