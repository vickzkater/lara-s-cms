## Version 2.0.1
### Changelog
- Add support auth for Guzzle functions
- Add support Session Driver Database by make some changes in `Illuminate\Session\DatabaseSessionHandler.php`
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

## Version 2.0.0
### Changelog
- Add QR Code Generator
- Add support login with social media (Google/Facebook)
- Add support back-end mode (MODEL or API)
- Add support upload file (PDF/TXT/DOCS/etc)

## Version 1.2.3
### Changelog
- Add PageBuilder
- Using The Helper JS - a lot of JS helper functions that are ready to help in your project
- Rebuild website template using Modern Business from Start Bootstrap
- Add Module Banner

## Version 1.2.2
### Changelog
- Using The Helper PHP - a lot of PHP helper functions that are ready to help in your project
- Add Support GoSMSGateway API - send SMS (Short Message Service)
- Add Support Mailchimp API
- Fix error 404 Custom Page because Module Application Configurations
- Fix error 503 (Maintenance Mode) Page because Module Application Configurations

## Version 1.2.1
### Changelog
- Add Import & Export Excel File (in Product)
- Add Delete Uploaded Image (in Product)
- Add Rich Text Editor/WYSIWYG using TinyMCE (in Product)
- Add Datepicker (in Product)

## Version 1.2.0
### Changelog
- Upgraded to Laravel 7.x
- Add Sortable Data List (in Division)
- Add Sortable Data List with Filter (in Branch - Filter by Division)
- Add Application Configurations