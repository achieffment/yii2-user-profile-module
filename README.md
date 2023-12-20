User profile module for Yii 2
=====

Perks
---

* Uses [yii2-user-management-module](https://github.com/achieffment/yii2-user-management-module) (webvimark [module-user-management](https://github.com/webvimark/user-management) fork)
* User profile (includes avatar, sex, firstname, lastname, patronymic, date of birth, phone number, about, job title and social links (vk, ok, telegram, whatsapp, viber, youtube, twitter, facebook) fields)
* Avatar encoding
* Profile data encoding
* Forms for create, update, view users and registration 


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require chieff/yii2-user-profile-module
```

or add

```
"chieff/yii2-user-profile-module": "dev-master"
```

to the require section of your `composer.json` file.

Forked
---
It is a fork of webvimark [module-user-management](https://github.com/webvimark/user-management), so be sure that you are not using that module in require sections of composer. This may have an impact because of this fork uses same namespaces. 

Configuration
---

1) In your config/web.php

```php
'modules' => [
    'user-profile' => [
        'class' => 'chieff\modules\UserProfile\UserProfileModule',
    ]
],
```

- If you want avatar be encoded, use
```php
'avatarEncode' => true
```
- If you want data be encoded, use
```php
'dataEncode' => true
```
- If you want avatar or data be encoded, also use
```php
'passphrase' => 'your passphare'
```

2) Also add routes for user-management module in your config/web.php
```php
'modules' => [
    'user-management' => [
        'userCreatePath' => '/user-profile/profile/create',
        'userUpdatePath' => '/user-profile/profile/update',
        'userViewPath'   => '/user-profile/profile/view',
    ]
],
```

2) Run migrations from the console, delete user table first if you use yii2 advanced
```
./yii migrate --migrationPath=vendor/chieff/yii2-user-profile-module/migrations/
```

If you want delete tables later and didn't migrate another tables, use:
```
./yii migrate/down --migrationPath=vendor/chieff/yii2-user-profile-module/migrations/
```

Icons
---

To see icons:
```php
class AppAsset extends AssetBundle
{
	// ...
	public $depends = [
		// ...
		'rmrevin\yii\fontawesome\AssetBundle'
	];
}
```

Datepicker
---

For date and datetime fields used [kartik-v/yii2-widget-datepicker](https://github.com/kartik-v/yii2-widget-datepicker) and [kartik-v/yii2-widget-datetimepicker](kartik-v/yii2-widget-datetimepicker) , it need bootstrap directive of version. Put in config/params.php code below for version that you are using:
```
<?php

return [
    'bsVersion' => '4',
    'adminEmail' => 'admin@example.com',
    // ...
];
```