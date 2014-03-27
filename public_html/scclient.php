<?php
 ini_set('display_errors', 'On');
 error_reporting(E_ALL);
 date_default_timezone_set('Europe/Warsaw');

#die(sha1('test'));

 try {
	$client = new SoapClient(NULL, array(
		'location'		=> 'http://'.$_SERVER['HTTP_HOST'].'/nadajto/api',
		'uri'			=> 'http://'.$_SERVER['HTTP_HOST'].'/nadajto/api',
		'trace'			=> 1,
		'exceptions'	=> 0));
 } catch (SOAPFault $err) {
	die($err->faultcode.': '.$err->faultstring);
 }

 $uobj = new UserObject();
 $uobj->login = 'admin';
 $uobj->password = 'test';
 $uobj->key = 'kdj438rh43r4g';
 $result = $client->__soapCall("Login", array($uobj));
 if(is_soap_fault($result))
 {
	trigger_error("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring})", E_USER_ERROR);
 }
 var_dump($result);
 $session = $result['SessionID'];
 echo '<hr>';

 $result = $client->__soapCall("GetAvailableCouriers", array($session));
 if(is_soap_fault($result))
 {
	trigger_error("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring})", E_USER_ERROR);
 }
 var_dump($result);
 echo '<hr>';






## classes definitnions ##
 class UserObject {
	public $login;
	public $password;
	public $key;
 }


?>