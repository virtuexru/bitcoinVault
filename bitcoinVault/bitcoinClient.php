<?php

namespace bitcoinVault;

use \Exception as CoinException;


class bitcoinClient extends bitcoinVault {
	/**
	 * @param  string $coin
	 * @param  string $destination
	 * @return backupWallet() response jSONrpc
	 */
	public function backupWallet($destination) {
		return $this->bitcoin->backupwallet($destination);
	}

	/**
	 * @param  string $account
	 * @return getnewaddress() response jSONrpc
	 */
	public function bulkGetNewAddress($account) {
		if(!isset($account)) throw new CoinException("Account must be passed.", 1);

		try {
			return $this->bitcoin->getnewaddress($account);
		} catch(CoinException $e) {
			throw $e;
		}
	}

	/**
	 * @param  int 	  $price
	 * @param  string $coin
	 * @return int last btc-e price * btc given
	 */
	public function convertCoinToUsd($price) {
		$convert_url = "https://btc-e.com/api/2/btc_usd/ticker";

		// init curl
		$ch = curl_init();

		// curl options
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $convert_url);

		try {
			$result = curl_exec($ch);
			$prices = json_decode($result, true);

			return $prices['ticker']['last'] * $price;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * @param  string $account
	 * @return getaccount() response jSONrpc
	 */
	public function getAccount($account) {
		if(!isset($account)) throw new CoinException("Account must be passed.", 1);

		return $this->bitcoin->getaccount($account);
	}

	/**
	 * @param  string $coin
	 * @param  string $account
	 * @return getaccountaddress() response jSONrpc
	 */
	protected function getAccountAddress($account) {
		if(!isset($account)) throw new CoinException("Account must be passed.", 1);

		try {
			return $this->bitcoin->getaccountaddress($account);
		} catch (CoinException $e) {
			throw $e;
		}
	}

	/**
	 * @param  string $account
	 * @return getaddressesbyaccount() response jSONrpc
	 */
	public function getAddressesByAccount($account) {
		if(!isset($account)) throw new CoinException("Account must be passed.", 1);

		try {
			return $this->bitcoin->getaddressesbyaccount($account);
		} catch (CoinException $e) {
			throw $e;
		}
	}

	/**
	 * @return getbalance() response jSONrpc
	 */
	public function getBalance() {
		return $this->bitcoin->getbalance($this->userWallet, 0);
	}

	/**
	 * @return getbalance() response jSONrpc
	 */
	public function getBalanceFiat() {
		return $this->convertCoinToUsd($this->bitcoin->getbalance($this->userWallet, 0), 'bitcoin');
	}

	/**
	 * @return getdifficulty() response jSONrpc
	 */
	public function getDifficulty() {
		return $this->bitcoin->getdifficulty();
	}

	/**
	 * @return getinfo() response jSONrpc
	 */
	public function getInfo() {
		foreach($this->bitcoin->getinfo() as $key => $item) {
			echo (empty($key) ? '[null]' : $key) . ': ' . (empty($item) ? '0' : $item) . '<br />';
		}
	}

	/**
	 * @param  string $account
	 * @return getreceivedbyaddress() response jSONrpc
	 */
	public function getReceivedByAddress($account) {
		if(!isset($account)) throw new CoinException("Account must be passed.", 1);

		return $this->bitcoin->getreceivedbyaddress($account);
	}

	/**
	 * calls listaccounts on {coin}d
	 */
	public function listAccounts() {
		foreach($this->bitcoin->listaccounts() as $account => $amount) {
			echo (empty($account) ? '[null]' : $account) . ': ' . $amount . '<br />';
		}
	}

	/**
	 * @param  string $account
	 * calls listtransactions on {coin}d
	 */
	public function listTransactions($account='') {
		if(!isset($account)) throw new CoinException("Account must be passed.", 1);

		return $this->bitcoin->listtransactions($account);
	}
}