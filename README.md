# bitcoinVault
## an open source PHP Bitcoin API Client

This PHP Bitcoin API extends the JSON RPC Client to communicate with a [Bitcoind server](https://en.bitcoin.it/wiki/Bitcoind).

### Usage
Use the index.php file as a starting point or just copy and paste from here:

```php
<?php
spl_autoload_register();

// init
$client = new bitcoinVault\bitcoinClient();

// then for example, pull your bitcoind server stats:
$client->getInfo('bitcoin');
?>
```

### Requirements
+ PHP5
+ cURL support