<?php

namespace bitcoinVault;

use \Exception as CoinException;


class bitcoinVault {
	/**
	 * @var pool: list of connections to construct
	 * TODO: possibility to add more coins to the pool [array('bitcoin' => '8332', 'litecoin' => '9332')]
	 */
	private static $_coinPool = array('bitcoin' => '8332');

	/**
	 * @var activeCoins: key names of all current coins
	 */
	protected $activeCoins;

	/**
	 * @var instance: singleton instance of the class
	 */
	private static $instance;

	/**
	 * @var {coin}: all possible/applicable coins
	 */
	protected $bitcoin;

	/**
	 * @var username: bitcoind username
	 * @var password: bitcoin password
	 * !note: username/pass should be loaded in from disk
	 * @var serveraddress: IP address of bitcoind
	 */
	private static $_username = "tester";
	private static $_password = "apple";
	private static $_serveraddress = "0.0.0.0";

	/**
	 * @var algo: blowfish used for encryption
	 * @var cost: cost parameter
	 */
	private static $algo = '$2y';
	private static $cost = '$10';

	/**
	 * constructs the main object
	 */
	public function __construct() {
		$this->setActiveCoins();

		try {
			foreach (self::$_coinPool as $prefix => $port) {
				// create the actual connection to bitcoind using the coin name
				// usage: $this->bitcoin->(arg)
				$this->{$prefix} = new jsonRPCClient('http://' . self::$_username . ':' . self::$_password . '@' . self::$_serveraddress . ':' . $port);
			}
		} catch(Exception $e) {
			throw new CoinException($e);
		}
	}

	/**
	 * used for cleanup
	 */
	public function __destruct() {
		// $this->bitcoin->disconnect();
    }

	/**
	 * gathers our active coins set in pool
	 */
	protected function setActiveCoins() {
		$this->activeCoins = array_keys(self::$_coinPool);
	}

	/**
	 * provides singleton instance of our class
	 */
	protected function getInstance() {
		if(!self::$instance) self::$instance = new self();

		return self::$instance;
	}

	/**
	 * required salting for the password
	 */
	protected static function unique_salt() {
		return substr(sha1(mt_rand()), 0, 22);
	}

	/**
	 * @param hash: the hash to be checked
	 * @param password: against the cleartext password
	 */
    public static function check_password($hash, $password) {
        $full_salt = substr($hash, 0, 29);
        $new_hash = crypt($password, $full_salt);

        return ($hash == $new_hash);
    }

	/**
	 * @param password: the password to hash
	 * for more information on crypt: http://docs.php.net/manual/en/function.crypt.php
	 */
	public static function hash($password) {
		return crypt($password,
					self::$algo .
					self::$cost .
					'$' . self::unique_salt());
	}
}

?>