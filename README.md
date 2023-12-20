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
"chieff/yii2-user-profile-module": "^1"
```

to the require section of your `composer.json` file.

Configuration
---

1) In your config/web.php

```php
'modules' => [
    'user-profile' => [
        'class' => 'chieff\modules\UserProfile\UserProfileModule',
        'avatarEncode' => false,
        'passphrase' => 'qwe',
        'dataEncode' => false,
    ]
],
```

To learn about events check:

* http://www.yiiframework.com/doc-2.0/guide-concept-events.html
* http://www.yiiframework.com/doc-2.0/guide-concept-configurations.html#configuration-format

Layout handler example in *AuthHelper::layoutHandler()*

To see full list of options check *UserManagementModule* file

**If you use yii2 advanced and want to use one auth in general**:

- Comment user sections and sessions in components like code below in /backend/config/main.php and /frontend/config/main.php
```php
'components' => [
    'user' => [
        'identityClass' => 'common\models\User',
        'enableAutoLogin' => true,
        'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
    ],
    'session' => [
        'name' => 'advanced-backend',
    ],
]
```
- Add code from the beggining of this step in /common/config/main.php
- Add code below in /common/config/main.php
```php
'components' => [
    'session' => [
        'name' => 'advanced-both',
    ],
]
```

2) In your config/console.php (this is needed for migrations and working with console, skip this step if you use yii2 advanced)

```php
'modules' => [
    'user-management' => [
        'class' => 'webvimark\modules\UserManagement\UserManagementModule',
        'controllerNamespace' => 'vendor\webvimark\modules\UserManagement\controllers', // To prevent yii help from crashing
    ],
],
```

3) Run migrations from the console, delete user table first if you use yii2 advanced
```
./yii migrate --migrationPath=vendor/webvimark/module-user-management/migrations/
```

If you want delete tables later and didn't migrate another tables, use:
```
./yii migrate/down 11 --migrationPath=vendor/webvimark/module-user-management/migrations/
```

4) In you base controller

```php
public function behaviors() {
    return [
        'ghost-access' => [
            'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
        ],
    ];
}
```

Where you can go
-----

```php
use webvimark\modules\UserManagement\components\GhostMenu;
use webvimark\modules\UserManagement\UserManagementModule;
echo GhostMenu::widget([
    'encodeLabels'=>false,
    'activateParents'=>true,
    'items' => [
        [
            'label' => 'Backend routes',
            'items' => UserManagementModule::menuItems()
        ],
        [
            'label' => 'Frontend routes',
            'items' => [
                ['label' => 'Login', 'url' => ['/user-management/auth/login']],
                ['label' => 'Logout', 'url' => ['/user-management/auth/logout']],
                ['label' => 'Registration', 'url' => ['/user-management/auth/registration']],
                ['label' => 'Change own password', 'url' => ['/user-management/auth/change-own-password']],
                ['label' => 'Password recovery', 'url' => ['/user-management/auth/password-recovery']],
                ['label' => 'E-mail confirmation', 'url' => ['/user-management/auth/confirm-email']],
            ],
        ],
    ],
]);
```

Also you can get links for nav:
```php
use webvimark\modules\UserManagement\components\GhostMenuArray;
$menuItems = GhostMenuArray::buildDefault();
$menuItems = array_merge($menuItems, [
    [
        'label' => 'Блог',
        'url' => ['/topic/index'],
        'items' => [
            [
                'label' => 'Категории',
                'url' => ['/topic-category/index'],
            ],
            [
                'label' => 'Страницы',
                'url' => ['/topic/index']
            ]
        ]
    ]
]);
```
and get links for different places buildDefaultBackend or buildDefaultFrontend.

First steps
---

From the menu above at first you'll se only 2 element: "Login" and "Logout" because you have no permission to visit other urls and to render menu we using **GhostMenu::widget()**. It's render only element that active user can visit.

Also same functionality has **GhostNav::widget()** and **GhostHtml:a()**

1) Login as superadmin/superadmin

2) Go to "Permissions" and play there

3) Go to "Roles" and play there

4) Go to "User" and play there

5) Go to "Routes" and play there

6) Relax


Usage
---

You controllers may have two properties that will make whole controller or selected action accessible to everyone

```php
public $freeAccess = true;
```

Or

```php
public $freeAccessActions = ['first-action', 'another-action'];
```

Here are list of the useful helpers. For detailed explanation look in the corresponding functions.

```php
User::hasRole($roles, $superAdminAllowed = true)
User::hasPermission($permission, $superAdminAllowed = true)
User::canRoute($route, $superAdminAllowed = true)

User::assignRole($userId, $roleName)
User::revokeRole($userId, $roleName)

User::getCurrentUser($fromSingleton = true)
```

Role, Permission and Route all have following methods

```php
Role::create($name, $description = null, $groupCode = null, $ruleName = null, $data = null)
Role::addChildren($parentName, $childrenNames, $throwException = false)
Role::removeChildren($parentName, $childrenNames)
```

Events
------

Events can be handled via config file like following

```php
'modules' => [
    'user-management' => [
        'class' => 'webvimark\modules\UserManagement\UserManagementModule',
        'on afterRegistration' => function(UserAuthEvent $event) {
            // Here you can do your own stuff like assign roles, send emails and so on
        },
    ],
],
```

List of supported events can be found in *UserAuthEvent* class

FAQ
---

**Question**: I want users to register and login with they e-mails! Mmmmm... And they should confirm it too!

**Answer**: See configuration properties *$useEmailAsLogin* and *$emailConfirmationRequired*

**Question**: I want to have profile for user with avatar, birthday and stuff. What should I do ?

**Answer**: Profiles are to project-specific, so you'll have to implement them yourself (but you can find example here - https://github.com/webvimark/user-management/wiki/Profile-and-custom-registration). Here is how to do it without modifying this module

1) Create table and model for profile, that have user_id (connect with "user" table)

2) Check AuthController::actionRegistration() how it works (*you can skip this part*)

3) Define your layout for registration. Check example in *AuthHelper::layoutHandler()*. Now use theming to change registraion.php file

4) Define your own UserManagementModule::$registrationFormClass. In this class you can do whatever you want like validating custom forms and saving profiles

5) Create your controller where user can view profiles

Icons
-----

To see icons:
```
composer require "rmrevin/yii2-fontawesome:~3.5"
```
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
----

For date fields used kartik-v/yii2-widget-datepicker, it need bootstrap directive of version. Put in config/params.php code below for version that you are using:
```
<?php
return [
    'bsVersion' => '4',
    'adminEmail' => 'admin@example.com',
    // ...
];
```