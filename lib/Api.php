<?php

/*
 * Api for opensolution
 */

namespace lib;

class Api {
  
  public function loginCheck($login, $pass, $api) {

    $pass = base64_decode( $pass );

    $apiKeyPattern = '#^[a-f0-9]{40}$#i';
    if(!preg_match($apiKeyPattern, $api)) {
      return false;
    }
    if(!filter_var($login, FILTER_VALIDATE_EMAIL))
      return false;
    
    $user = \Model::factory('Customer')->where_raw('(`api` = ? AND `email` = ?)', array($api, $login))->find_one();
     
    if($user instanceof \Customer) {
      if(strcmp(hash( 'sha256', $user->pass,true ),$pass)==0) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
  //sprawdzam czy ma kase
  //dodaje do bazy wysylke
  //wywoluje ship from DB
  //wyswietla date odbioru paczki userowi
}

?>
