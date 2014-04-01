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
    file_put_contents('debug-api.txt',$input);
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


$app->post('/api/ship', function () use ($app) {
   // $tools = new \lib\Tools();
    print json_encode(var_export($app->request()->post(),true));
      //  var_dump($app->request()->post());
    //die();
//    $result = $tools->prepareDataToShip($app->request()->post(),$_SESSION['user']['id_customer']);

//    if($result===true) {
//        $user = $tools->customer;
//        $order = $tools->order;
//        $delivery = $tools->delivery;
//
//        $directory = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
//        $myUrl = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] .$directory;
//
//        $orderName = 'Przesyłka kurierska';
//
//        if($user->prepays()->filter('sum')>=$order->price && $user->onetime==0) {
//            try {
//                $prepay = new \lib\Prepay();
//                $prepay->addPrepayForOrder($order);
//                $prepayId = $prepay->getId();
//                
//                if(!empty($prepayId)) {
//                    SendMail($delivery->from_email, array('EMAIL'=>$delivery->from_email), 8);
//                    SendMail('marcin.jastrzebski@poludniowo.pl', array('ID'=>$prepay->id_order), 9);
//                    $courierManager = new \lib\CourierManager($order->id_courier);
//                    $courier = $courierManager->getCourier();
//                    if(!$courier->ship_from_db($order->id_order)) throw new Exception('Błąd w trakcie wysyłania danych kurierowi dla tego zamówienia. Skontakuj się z nadajto.');
//                    else {
//                        $result = array('prepay'=>'prepay');
//                    }
//                } else throw new Exception('Błąd w trakcie generowania zamówienia dla płatności PREPAY');
//            } catch (Exception $e) {
//                $result = array('error' => $e->getMessage());
//            }
//            
//            echo json_encode($result);
//            exit();
//        }
//    }
});        
?>