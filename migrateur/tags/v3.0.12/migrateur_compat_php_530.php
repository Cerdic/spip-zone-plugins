<?php


/**
 * Avant PHP 5.4 la constante `OPENSSL_RAW_DATA` n'est pas définie.
 *
 * Or, elle sert pour la lib/php-encryption
 *
 * @link http://php.net/manual/fr/function.openssl-encrypt.php
**/
if (!defined('OPENSSL_RAW_DATA')) {
	define('OPENSSL_RAW_DATA', 1);
}

if (!function_exists('hex2bin')) {
	function hex2bin( $str ) {
		return pack( "H*", $str);
	}
}

if (!function_exists('bin2hex')) {
	function bin2hex( $str ) {
		return unpack( "H*", $str);
	}
}
