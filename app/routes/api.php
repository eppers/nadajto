<?php

$app->get('/api/generuj', function () use ($app) {
  print sha1('nadajto'.time());
});

$app->post('/api/rate', function () use ($app) {
    $courier = new \lib\Tools;
    $respond = array();
     
   try {
    $request = $app->request();
    $body = $request->getBody();
    $input = json_decode($body,true);
    $api = new \lib\Api;
    
    $stringRate = $sep = ''; 
    
    foreach( $input as $key => $value ) {
      $stringRate .= $sep . $key . '=' . $value;
      $sep = '&';
     
    }
    file_put_contents('debug-api.txt',$stringRate);

    if(!$api->loginCheck($input['login'], $input['password'], $input['apiKey']))
      throw new Exception('Autoryzacja nie powiodła się.');
    
    if(!empty($input['pkg_width']) && !empty($input['pkg_height']) && !empty($input['pkg_depth']) && !empty($input['pkg_weight']) ){
      $result = $courier->rate(array('form'=>$stringRate));
    //  file_put_contents('debug-api.txt',var_export($result,true));
      $respond['status'] = 'success';
      $respond['total'] = $result[1]['price_brut'];
      
    } else {
      throw new Exception('Pola waga oraz wymiary muszą być wypełnione.');
    }
  } catch (Exception $e) {
    file_put_contents('debug-api.txt',var_export($e->getMessage(),true),FILE_APPEND);
    $respond['status'] = 'fail';
    $respond['fault'] = $e->getMessage();
  //  $app->response()->status(400);
 //   $app->response()->header('X-Status-Reason', $e->getMessage());
  }
    
    $app->response()->header('Content-Type', 'application/json');
    print json_encode($respond);  
});
?>