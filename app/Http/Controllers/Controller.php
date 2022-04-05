<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	function decryptFilter($srchid) {
		list($crypted_token, $enc_iv) = explode("::", $srchid);;
		$cipher_method = 'aes-128-ctr';
		$enc_key = openssl_digest(php_uname(), 'SHA256', TRUE);
		return json_decode(openssl_decrypt($crypted_token, $cipher_method, $enc_key, 0, hex2bin($enc_iv)), true);		
	}
	
	function encryptFilter($q){
		$token = json_encode($q);

		$cipher_method = 'aes-128-ctr';
		$enc_key = openssl_digest(php_uname(), 'SHA256', TRUE);
		$enc_iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_method));
		$crypted_token = openssl_encrypt($token, $cipher_method, $enc_key, 0, $enc_iv) . "::" . bin2hex($enc_iv);
		unset($token, $cipher_method, $enc_key, $enc_iv);
		return $crypted_token;
	}
}
