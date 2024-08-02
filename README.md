![Packagist License (custom server)](https://img.shields.io/packagist/l/vcian/pulse-active-sessions)
![Packagist Downloads (custom server)](https://img.shields.io/packagist/dt/vcian/pulse-active-sessions)


# Active Sessions card for Laravel Pulse

This card will show total number of sessions in application.

## Installation

Require the package with Composer:

```shell
composer require vcian/pulse-active-sessions
```

Next, you should publish the Pulse configuration and migration files using the vendor:publish Artisan command:

```
php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider"
```

```
php artisan migrate
```

## Register the recorder

Right now, the Composer dependencies will only be checked once per day. To run the checks you must add the `PulseActiveSessionRecorder` to the `pulse.php` file.

```diff
return [
    // ...
    
    'recorders' => [
+        \Vcian\Pulse\PulseActiveSessions\Recorders\PulseActiveSessionRecorder::class => [],
    ]
]
```

You also need to be running [the `pulse:check` command](https://laravel.com/docs/10.x/pulse#dashboard-cards).

## Add to your dashboard

To add the card to the Pulse dashboard, you must first [publish the vendor view](https://laravel.com/docs/10.x/pulse#dashboard-customization).

Then, you can modify the `dashboard.blade.php` file:

```diff
<x-pulse>
+    <livewire:pulse_active_session cols='4' rows='2' />

    <livewire:pulse.servers cols="full" />

    <livewire:pulse.usage cols="4" rows="2" />

    <livewire:pulse.queues cols="4" />

    <livewire:pulse.cache cols="4" />

    <livewire:pulse.slow-queries cols="8" />

    <livewire:pulse.exceptions cols="6" />

    <livewire:pulse.slow-requests cols="6" />

    <livewire:pulse.slow-jobs cols="6" />

    <livewire:pulse.slow-outgoing-requests cols="6" />

</x-pulse>
```

Then, you can add setting in the `pulse.php` config file:

```diff
+    'active_session_threshold' => 100,
```

<img src="/art/card-1.png" width="100%" alt="Active Sessions Card">

<img src="/art/card-2.png" width="100%" alt="Active Sessions Card">

<img src="/art/card-3.png" width="100%" alt="Active Sessions Card">

<img src="/art/card-4.png" width="100%" alt="Active Sessions Card">

To make pulse recorders will automatically capture entries based on framework events dispatched by Laravel, You must run the below command.
```
php artisan pulse:check
```

That's it!

## Supported session drivers

- database
- file
- redis
- memcached

## Not Supported

- Passport : This will not support when multiple providers used as authentication.It only while using single provider.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

       We believe in 
            👇
          ACT NOW
      PERFECT IT LATER
    CORRECT IT ON THE WAY.

## Security

If you discover any security-related issues, please email ruchit.patel@viitor.cloud instead of using the issue tracker.

## Credits

- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.



