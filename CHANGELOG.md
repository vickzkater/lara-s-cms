Changelog
=========

## Version 2.0.7
- Update the packages (Laravel Framework 7.30.3 > 7.30.4)
- Optimize multi languages
- Add [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar)
- Remove package fzaninotto/faker (security issue)
- Update package phpunit to version 9 (warning issue)
- Fix issue for 404 error page when using the admin dashboard only

## Version 2.0.6
- Update config global mail for setup reply-to
- Update session redirect uri for web (separate from admin) using session "redirect_uri_web"
- Fix issue inaccurate stored time value due to incorrect data type selection in migrations (modify type "timestamp" to "datetime")
- Update minor for admin template
- Renaming menu user => admin, usergroup => admin group, and division => office
- Add Open Graph configurations (based on [ogp.me](https://ogp.me/), [Twitter Dev Docs](https://developer.twitter.com/en/docs/twitter-for-websites/cards/overview/markup), & [FB Dev Docs](https://developers.facebook.com/docs/sharing/webmasters/))

## Version 2.0.5
- Add function generate thumbnail image
- Fix issue in Article List
- Change the superadmin account credentials that do not conflict with the username 'superadmin'
- Add some language phrases for the language pack
- Add support One Way SMS Gateway API - send SMS (Short Message Service)
- Add support Login with Instagram

## Version 2.0.3.2
- Rollback `composer.lock` (using from v2.0.2) for support PHP >= 7.2
- Update the packages (Laravel Framework 7.18.0 > 7.28.3)
- Add solution for issue "500 Internal Server Error" (please check `README`)

## Version 2.0.3.1
- Update `README` (require PHP >= 7.3)

## Version 2.0.3
- Update validation redirection URL after login in Admin (if AJAX DataTables, set URL to Admin Home page as default)
- Add Dev Function to test sending email
- Add email template & function sending email as sample
- Update migrations & seeders for add rules automatically
- Update the packages (Laravel Framework 7.18.0 > 7.28.2)

## Version 2.0.2
- Update minor in MailchimpHelper
- Fix issue update config

## Version 2.0.1
- Add support auth for Guzzle functions
- Add support Session Driver Database
- Add security update: if the password has been changed, then force the user to re-login
- Add feature logout from all sessions

## Version 2.0.0
- Add QR Code Generator
- Add support login with social media (Google/Facebook)
- Add support back-end mode (MODEL or API)
- Add support upload file (PDF/TXT/DOCS/etc)

## Version 1.2.3
- Add PageBuilder
- Using The Helper JS - a lot of JS helper functions that are ready to help in your project
- Rebuild website template using Modern Business from Start Bootstrap
- Add Module Banner

## Version 1.2.2
- Using The Helper PHP - a lot of PHP helper functions that are ready to help in your project
- Add Support GoSMSGateway API - send SMS (Short Message Service)
- Add Support Mailchimp API
- Fix error 404 Custom Page because Module Application Configurations
- Fix error 503 (Maintenance Mode) Page because Module Application Configurations

## Version 1.2.1
- Add Import & Export Excel File (in Product)
- Add Delete Uploaded Image (in Product)
- Add Rich Text Editor/WYSIWYG using TinyMCE (in Product)
- Add Datepicker (in Product)

## Version 1.2.0
- Upgraded to Laravel 7.x
- Add Sortable Data List (in Division)
- Add Sortable Data List with Filter (in Branch - Filter by Division)
- Add Application Configurations