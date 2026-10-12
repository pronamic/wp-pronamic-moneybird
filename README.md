# Pronamic Moneybird

This WordPress plugin provides the link between your WordPress website and your Moneybird administration.

## Moneybird API

This WordPress plugin uses the Moneybird API, documentation for this can be found at https://developer.moneybird.com/.

## WP-CLI

### Link financial mutations to external sales invoices

Link unprocessed financial mutations to open external sales invoices that mention the transaction ID of the financial mutation (`account_servicer_transaction_id`, for example a Mollie `tr_…` ID) in one of their notes. A financial mutation is only linked if its amount equals the unpaid amount of the external sales invoice.

```
wp pronamic-moneybird link-financial-mutations-to-external-sales-invoices-by-transaction-id-notes --period=202601..202604 --financial-account-id=<id> --dry-run
```

| Option | Description |
| --- | --- |
| `--period=<period>` | Moneybird period filter for the financial mutations and the external sales invoices. |
| `--financial-account-id=<id>` | Moneybird financial account ID of the financial mutations. |
| `--prefix=<prefix>` | Only process transaction IDs that start with this prefix, defaults to `tr_`. |
| `--dry-run` | Only show the matches, do not link anything. |
| `--yes` | Skip the confirmation before linking. |

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
