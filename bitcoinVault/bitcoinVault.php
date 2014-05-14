<?php

namespace bitcoinVault;

use \Exception as CoinException;


class bitcoinVault {
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
	protected static $_port = '8332';
	protected static $_username = "tester";
	protected static $_password = "apple";
	protected static $_serveraddress = "192.168.0.13";

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
			$this->bitcoin = new jsonRPCClient('http://' . self::$_username . ':' . self::$_password . '@' . self::$_serveraddress . ':' . self::$_port);
		} catch(Exception $e) {
			throw new CoinException($e);
		}
	}

	/**
	 * used for cleanup
	 */
	public function __destruct() {
		// $this->bitcoin->stop();
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