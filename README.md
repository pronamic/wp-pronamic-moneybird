# Pronamic Moneybird

This WordPress plugin provides the link between your WordPress website and your Moneybird administration.

## Moneybird API

This WordPress plugin uses the Moneybird API, documentation for this can be found at https://developer.moneybird.com/.

## Deploy

The plugin is deployed with [Deployer](https://deployer.org/). The hosts are not part of this repository, they are imported from a separate file via the `DEPLOYER_IMPORT` environment variable:

```
DEPLOYER_IMPORT=~/deployer.php vendor/bin/dep deploy
```

Or via the Composer script, which uses `~/deployer.php` unless `DEPLOYER_IMPORT` is set:

```
composer deploy
```

## Links

- https://www.pronamic.nl/
- https://www.moneybird.com/
- https://developer.moneybird.com/
- https://www.moneybird.nl/huisstijl/
- https://github.com/picqer/moneybird-php-client
