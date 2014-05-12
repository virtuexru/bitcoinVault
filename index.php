<?php

spl_autoload_register();

$client = new bitcoinVault\bitcoinClient();

$client->getInfo();

?>