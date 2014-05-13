<?php

namespace bitcoinVault;

use \Exception as CoinException;


class bitcoinVault {
	/**
	 * @var instance: singleton instance of the class
	 */
	private static $instance;

	/**
	 * @var the one, the only bitcoin
	 */
	protected $bitcoin;

	/**
	 * @var port: bitcoind port
	 * @var username: bitcoind username
	 * @var password: bitcoin password
	 * !note: username/pass should be loaded in from disk
	 * @var serveraddress: IP address of bitcoind
	 */
	private static $_port = '8332';
	private static $_username = "tester";
	private static $_password = "apple";
	private static $_serveraddress = "192.168.0.13";

	/**
	 * @var algo: blowfish used for encryption
	 * @var cost: cost parameter
	 */
	private static $_algo = '$2y';
	private static $_cost = '$10';

	/**
	 * constructs the main object
	 */
	public function __construct() {
		try {
			// create the actual connection to bitcoind using the coin name
			// usage: $this->bitcoin->(arg)
			$this->bitcoin = new jsonRPCClient('http://' . self::$_username . ':' . self::$_password . '@' . self::$_serveraddress . ':' . self::$_port);
		} catch(Exception $e) {
			throw new CoinException($e);
		}
	}

	/**
	 * used for cleanup
	 */
	public function __destruct() {
		$this->bitcoin->stop();
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
		return crypt($password, self::$_algo . self::$_cost . '$' . self::unique_salt());
	}
}

?>