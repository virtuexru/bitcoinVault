<?php

spl_autoload_register();

use bitcoinVault\bitcoinClient as bitcoinClient;
$client = new bitcoinClient();

$client->getInfo();

?>