<?php

namespace bitcoinVault;

use \Exception as CoinException;


class bitcoinClient extends bitcoinVault {
	/**
	 * @var coinDict: array of shorthand
	 */
	protected $coinDict = array('bitcoin' => 'btc');

	/**
	 * @param  string $coin
	 * @param  string $destination
	 * @return backupWallet() response jSONrpc
	 */
	public function backupWallet($coin, $destination) {
		return $this->$coin->backupwallet($destination);
	}

	/**
	 * @param  int 	  $price
	 * @param  string $coin
	 * @return int last btc-e price * btc given
	 */
	private function convertCoinToUsd($price, $coin) {
		$convert_url = "https://btc-e.com/api/2/" . $this->coinDict[$coin] . "_usd/ticker";

		// Initiate curl
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $convert_url);

		try {
			// Execute
			$result = curl_exec($ch);
			$prices = json_decode($result, true);

			return $prices['ticker']['last'] * $price;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * @param  string $coin
	 * @param  string $account
	 * @return getaccount() response jSONrpc
	 */
	public function getAccount($coin, $account) {
		return $this->$coin->getaccount($account);
	}

	/**
	 * @param  string $coin
	 * @param  string $account
	 * @return getaccountaddress() response jSONrpc
	 */
	protected function getAccountAddress($coin, $account) {
		try {
			return $this->$coin->getaccountaddress($account);
		} catch (CoinException $e) {
			throw $e;
		}
	}

	/**
	 * @param  string $coin
	 * @param  string $account
	 * @return getaddressesbyaccount() response jSONrpc
	 */
	public function getAddressesByAccount($coin, $account) {
		try {
			return $this->$coin->getaddressesbyaccount($account);
		} catch (CoinException $e) {
			throw $e;
		}
	}

	/**
	 * calls getinfo on {coin}d
	 */
	public function getAllCoinInfo() {
		foreach($this->activeCoins as $coin) {
			echo $coin . ': ' . $this->$coin->getblockcount() . '<br />';
		}
	}

	/**
	 * @param  string $coin
	 * @return getbalance() response jSONrpc
	 */
	public function getBalance($coin) {
		return $this->$coin->getbalance($this->userWallet, 0);
	}

	/**
	 * @param  string $coin
	 * @return getbalance() response jSONrpc
	 */
	public function getBalanceFiat($coin) {
		return $this->convertCoinToUsd($this->$coin->getbalance($this->userWallet, 0), $coin);
	}

	/**
	 * @param  string $coin
	 * @return getdifficulty() response jSONrpc
	 */
	public function getDifficulty($coin) {
		return $this->$coin->getdifficulty();
	}

	/**
	 * @param  string $coin
	 * @return getinfo() response jSONrpc
	 */
	public function getInfo($coin) {
		foreach($this->$coin->getinfo() as $key => $item) {
			echo (empty($key) ? '[null]' : $key) . ': ' . (empty($item) ? '0' : $item) . '<br />';
		}
	}

	/**
	 * testing grabbing getInstance() [protected]
	 * method from Parent [coinVault] class
	 */
	public function getInstance() {
		return Parent::getInstance();
	}

	/**
	 * @param  array  $coins
	 * @param  string $account
	 * @return getnewaddress() response jSONrpc
	 */
	public function bulkGetNewAddress(array $coins, $account) {
		try {
			foreach($coins as $coin) {
				${'new_account_' . $coin} = $this->$coin->getnewaddress($account);
			}

			$sql = "INSERT INTO wallets (member, btc, ltc) VALUES (:account, :address_btc, :address_ltc)";
			$stmt = $this->dbc->prepare($sql);

			if($stmt->execute(array(':account' => $account, ':address_btc' => $new_account_bitcoin, ':address_ltc' => $new_account_litecoin))) {
				return array('bitcoin' => $new_account_bitcoin, 'litecoin' => $new_account_litecoin);
			} else {
				return false;
			}
		} catch(CoinException $e) {
			throw $e;
		}
	}

	/**
	 * @param  string $coin
	 * @param  string $account
	 * @return getreceivedbyaddress() response jSONrpc
	 */
	public function getReceivedByAddress($coin, $account) {
		return $this->$coin->getreceivedbyaddress($account);
	}

	/**
	 * @param  string $coin
	 * calls listaccounts on {coin}d
	 */
	public function listAccounts($coin) {
		foreach($this->$coin->listaccounts() as $account => $amount) {
			echo (empty($account) ? '[null]' : $account) . ': ' . $amount . '<br />';
		}
		/*
		while(list($name, $val) = @each()) {
			echo $name . ': ' . $val . '<br />';
		}
		return true;
		*/
	}

	/**
	 * @param  string $coin
	 * @param  string $account
	 * calls listtransactions on {coin}d
	 */
	public function listTransactions($coin, $account='') {
		return $this->$coin->listtransactions($account);
	}

	/**
	 *
	 */
	public function _moveCoin($coin, $amount=0, $from_account, $to_account) {
		try {
			$this->moveCoin($coin, $amount, $from_account, $to_account);
			return true;
		} catch (CoinException $e) {
			echo $e->getMessage() . "<br />";
    		return false;
		}
	}

	/**
	 *
	 */
	public function _sendCoin($coin, $amount=0, $from_account, $to_account) {
		try {
			$this->sendCoin($coin, $amount, $from_account, $to_account);
			return true;
		} catch (CoinException $e) {
			echo $e->getMessage() . "<br />";
    		return false;
		}
	}

}