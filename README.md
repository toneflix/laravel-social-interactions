# Laravel Social Interactions

[![Test & Lint](https://github.com/toneflix/laravel-social-interactions/actions/workflows/php.yml/badge.svg?branch=main)](https://github.com/toneflix/laravel-social-interactions/actions/workflows/php.yml)
[![Latest Stable Version](http://poser.pugx.org/toneflix-code/social-interactions/v)](https://packagist.org/packages/toneflix-code/social-interactions) [![Total Downloads](http://poser.pugx.org/toneflix-code/social-interactions/downloads)](https://packagist.org/packages/toneflix-code/social-interactions) [![Latest Unstable Version](http://poser.pugx.org/toneflix-code/social-interactions/v/unstable)](https://packagist.org/packages/toneflix-code/social-interactions) [![License](http://poser.pugx.org/toneflix-code/social-interactions/license)](https://packagist.org/packages/toneflix-code/social-interactions) [![PHP Version Require](http://poser.pugx.org/toneflix-code/social-interactions/require/php)](https://packagist.org/packages/toneflix-code/social-interactions)
[![codecov](https://codecov.io/gh/toneflix/laravel-social-interactions/graph/badge.svg?token=SHm1zYOgLH)](https://codecov.io/gh/toneflix/laravel-social-interactions)

<!-- ![GitHub Actions](https://github.com/toneflix/laravel-social-interactions/actions/workflows/main.yml/badge.svg) -->

Laravel Social Interactions adds to your project the ability to create social interactions like **saves, votes, likes, dislikes, reactions, Etc.** with your models.

## Use Cases

1. Voting System (Upvoting and Downvoting)
2. Bookmarking System
3. Liking and Reaction System
3. Anything that requires a third party user to approve or reject.

## Installation

1. Install the package via composer:

    ```bash
    composer require toneflix-code/social-interactions
    ```

2. Publish resources (migrations and config files):

    ```shell
    php artisan vendor:publish --tag=social-interactions
    ```

3. Run the migrations with the following command:

    ```shell
    php artisan migrate
    ```

4. Done!

## Package Discovery

Laravel automatically discovers and publishes service providers but optionally after you have installed Laravel Fileable, open your Laravel config file config/app.php and add the following lines.

In the $providers array add the service providers for this package.

```php
ToneflixCode\SocialInteractions\SocialInteractionsServiceProvider::class
```

Add the facade of this package to the $aliases array.

```php
'SocialInteraction' => ToneflixCode\SocialInteractions\Facades\SocialInteraction::class
```

## Usage

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email code@toneflix.com.ng instead of using the issue tracker.

## Credits

-   [Toneflix Code](https://github.com/toneflix)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
