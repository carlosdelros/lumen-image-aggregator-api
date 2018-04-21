# Lumen Image Aggregator Api


## Installation
1. Include .env file with providers api keys.
2. Run
```php
composer install
```
3. Start server:
```php
php -S localhost:8000 -t public
```

## Usage

- GET /images?q={QUERY}&page={PAGE}&limit={LIMIT}
    - QUERY: Search term used to get images from the different providers.
    - PAGE: Desired page from the provider's paginated result.
    - LIMIT: Number of images return by each provider.
- GET /providers
    - Returns all providers supported by the API
