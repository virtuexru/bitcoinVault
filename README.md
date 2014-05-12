# bitcoinVault
## an open source PHP Bitcoin API Client

This PHP Bitcoin API extends the JSON RPC Client to communicate with a [Bitcoind server](https://en.bitcoin.it/wiki/Bitcoind).

### Bitcoind (server) setup
To find out how to setup your own bitcoind daemon server running locally on [virtualbox](https://www.virtualbox.org/wiki/Downloads) & ubuntu, you can check out my guide here: [Guide to compile & install Bitcoind on Ubuntu 12.04 using VirtualBox](http://virtuedev.com/bitcoin/guide-to-compile-install-bitcoind-on-ubuntu-12-04-using-virtualbox/)

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