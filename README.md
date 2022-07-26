
[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/support-ukraine.svg?t=1" />](https://supportukrainenow.org)

# Respins.io base functions
    "repositories": [
        {
            "type": "path",
            "url": "packages/respins/base-functions"
        }
    ],
        "respins/base-functions": "*",

    Add to App\Http\Kernel.php API middleware:
    ```
        'respins_aggregation' => [
            \Respins\BaseFunctions\Middleware\RespinsIPCheck::class,
        ],
    ```
## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/base-functions.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/base-functions)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require respins/base-functions
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="base-functions-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="base-functions-config"
```
