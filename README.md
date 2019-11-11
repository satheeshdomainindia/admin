# Laravel Admin



This package is just create admin side (multi auth), which is totaly isolated from your normal auth ( which we create using php artisan make:auth )

On top of that, you can use multiple authentication types, simultaneously, so you can be logged
in as a user and an admin, without conflicts!


## Version Guidance

| Laravel version | Branch | Install                                               |
| --------------- | ------ | ----------------------------------------------------- |
| 5.6 and 5.7     | 5.7    | composer require laravelzone/admin:5.7.x-dev          |
| 6.0 and 6.*     | Master | composer require laravelzone/admin:dev-master         |

## Installation


Install via composer.

```bash
composer require laravelzone/admin
```
```bash
php artisan install:admin
```

Now you can login your admin side by going to https://localhost:8000/admin with creadential

## Register new Admin

1. Using artisan command

```
php artisan admin:seed --role=super
```

2. Using Interface
To register new use you need to go to https://localhost:8000/admin/register.

Keep in mind that only a Super Admin can create new Admin.



## Change Prefix

You can change the prefix in your config file you have just published.
With prefix we mean what you want to call your admin side, we call it admin you can call it whatever you want.
Suppose you have changed prefix to 'master' now everywhere instead of 'admin' word, that changed to 'master'

```php
 /*
    |--------------------------------------------------------------------------
    | Prefix
    |--------------------------------------------------------------------------
    |
    | Use prefix to before the routes of admin package.
    | This way you can keep your admin page secure.
    | Default : admin
    */
    'prefix' => 'admin', // can change it to, lets say 'prefix' => 'master'
```

## Create Roles

To create a new role you have two options:

1. Using artisan command

```bash
php artisan admin:role rolename
```

2. Using Interface
   Just go to https://localhost:8000/admin/role.

Now you can click on 'Add Role' button to create new role.

**Edit or Delete Role can also be done with same interface**



## Access Level

**With Middleware**

1. You can use 'role' middleware to allow various admin for accessing certain section according to their role.

```php
Route::get('admin/check',function(){
    return "This route can only be accessed by admin with role of Editor"
})->middleware('role:editor');
```

Here it does't matter if you give role as uppercase or lowercase or mixed, this package take care of all these.

2. If you want a section to be accessed by only super user then use role:super middleware
   A super admin can access all lower role sections.

```php
Route::get('admin/check',function(){
    return "This route can only be accessed by super admin"
})->middleware('role:super');
```

**With Blade Syntax**

You can simply use blade syntax for showing or hiding any section for admin with perticular role.
For example, If you want to show a button for admin with role of editor then write.

```php
@admin('editor')
    <button>Only For Editor</button>
@endadmin
```

If you want to add multiple role, you can do like this

```php
@admin('editor,publisher,any_role')
    <button> This is visible to admin with all these role</button>
@endadmin
```

## Another Auth

**Apart from Admin section, you can make a another auth**

```php
php artisan admin:make {guard}
```

After you run this command you will get steps in which files has been added/changed.
![For Make](https://user-images.githubusercontent.com/41295276/44602450-4a4e2580-a7fd-11e8-858b-cac65c496908.png)

**You can rollback this auth also if you want.**

```php
php artisan admin:rollback {guard}
```

This command will show you steps to rollback and file that has changed/removed.
![For Rollback](https://user-images.githubusercontent.com/41295276/44602466-5508ba80-a7fd-11e8-9737-3711baecbbdb.png)

## License

This package inherits the licensing of its parent framework, Laravel, and as such is open-sourced
software licensed under the [MIT license](http://opensource.org/licenses/MIT)
