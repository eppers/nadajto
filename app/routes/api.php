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
 
    //czy user ma kase na koncie
    try {
        $request = $app->request();
        $body = $request->getBody();
        $input = json_decode($body,true);
        $api = new \lib\Api;

        $stringOrderDetails = $sep = ''; 
        file_put_contents('debug-api.txt',$input);
        foreach( $input as $key => $value ) {
            $stringOrderDetails .= $sep . $key . '=' . $value;
            $sep = '&';
        }
        file_put_contents('debug-api.txt',$stringOrderDetails);

        if(!$api->loginCheck($input['login'], $input['password'], $input['apiKey']))
            throw new Exception('Autoryzacja nie powiodła się.');
        
        $customer = \Model::factory('Customer')->where_raw('(`api` = ? AND `email` = ?)', array($input['apiKey'], $input['login']))->find_one();
        if(!empty($input['COD_check'])) $bankAcc = $customer->bank_acc;
        else $bankAcc = false; 
        
        if($customer instanceof \Customer) {
            $dataSend = array(
                'weight' => $input['pkg_weight'],
                'length' => $input['pkg_depth'],
                'width' => $input['pkg_width'],
                'height' => $input['pkg_height'],
                'pkg_type' => $input['rodzaj'],
                'nad_email' => $customer->email,
                'nad_email2' => $customer->email,
                'nad_company' => $customer->company,
                'nad_imie' => $customer->name,
                'nad_nazwisko' => $customer->lname,
                'nad_addr' => $customer->addr,
                'nad_miasto' => $customer->city,
                'nad_zip' => $customer->zip,
                'nad_telef' => $customer->phone,
                'odb_email' => $input['details']['receiverEmail'],
                'odb_email2' => $input['details']['receiverEmail'],
                'odb_company' => $input['details']['receiverName'],
                'odb_imie' => $input['details']['receiverName'],
                'odb_addr' => $input['details']['receiverStreet'],
                'odb_miasto' => $input['details']['receiverCity'],
                'odb_zip' => $input['details']['receiverZipCode'],
                'odb_telef' => $input['details']['receiverPhoneNumber'],
                'bank' => $bankAcc,
                'form' => $stringOrderDetails
            );
            $tools = new \lib\Tools();
            $result = $tools->prepareDataToShip($dataSend,$customer->id_customer, true);
            if(!$result) print json_encode(array('faultstring'=>$result));
        }
        
    } catch(Exception $e) {
        print json_encode(array('faultstring'=>$e->getMessage()));
    }
   
    //print json_encode(var_export($app->request()->post(),true));
      //  var_dump($app->request()->post());
    //die();
    

    if($result===true) {
        
        $user = $tools->customer;
        $order = $tools->order;
        $delivery = $tools->delivery;

        $directory = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $myUrl = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] .$directory;

        $orderName = 'Przesyłka kurierska';

        if($user->prepays()->filter('sum')>=$order->price && $user->onetime==0) {
            try {
                $prepay = new \lib\Prepay();
                $prepay->addPrepayForOrder($order);
                $prepayId = $prepay->getId();
                
                if(!empty($prepayId)) {
                    
                    SendMail($delivery->from_email, array('EMAIL'=>$delivery->from_email), 8);
                    //SendMail('marcin.jastrzebski@poludniowo.pl', array('ID'=>$prepay->id_order), 9);
                    $courierManager = new \lib\CourierManager($order->id_courier);
                    $courier = $courierManager->getCourier();
                    if(!$courier->ship_from_db($order->id_order)) throw new Exception('Błąd w trakcie wysyłania danych kurierowi dla tego zamówienia. Skontakuj się z nadajto.');
                    else {
                        $result = array('status'=>'success','msg'=>'Kurier zamówiony. Sprawdź skrzynkę email.');
                    }
                } else throw new Exception('Błąd w trakcie generowania zamówienia dla płatności PREPAY');
            } catch (Exception $e) {
                $result = array('faultstring'=> $e->getMessage());
            }
            
            
        } else  $result = array('faultstring'=> 'Brak środków na koncie. Doładuj je na nadajto.pl');
        
        echo json_encode($result);
    }
});        
?>