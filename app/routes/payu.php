<?php

/*
 * PAYU //////////////////////////////////////////////////////
 */

$app->post('/payment/payu/sending', function () use ($app) {
    
    $tools = new \lib\Tools();
    $result = $tools->prepareDataToShip($app->request()->post(),$_SESSION['user']['id_customer']);

    if($result===true) {
        $user = $tools->customer;
        $order = $tools->order;
        $delivery = $tools->delivery;

        $directory = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $myUrl = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] .$directory;

        $orderName = 'Przesyłka kurierska';

        if($user->prepays()->filter('sum')>=$order->price && $user->onetime==0) {
            $prepay = \Model::factory('Prepay')->create();
            $prepay->id_customer = $userId;
            $prepay->date = date('Y-m-d H:i:s');
            $prepay->id_order = $order->id_order;
            $prepay->amount -= $order->price;
            $prepay->save();

            SendMail($delivery->from_email, array('EMAIL'=>$delivery->from_email), 8);
            //SendMail('marcin.jastrzebski@poludniowo.pl', array('ID'=>$prepay->id_order), 9);
            $courierManager = new \lib\CourierManager($order->id_courier);
            $courier = $courierManager->getCourier();
            if(!$courier->ship_from_db($prepay->id_order)) file_put_contents("debug.txt", "Błąd w trakcie wysyłania danych kurierowi \n\n",FILE_APPEND);
            else file_put_contents("debug.txt", "kurier wysłany \n\n",FILE_APPEND);

            echo json_encode(array('prepay'=>'prepay'));
            exit();
        }


        // initialization of order is done with OrderCreateRequest message sent.

        // important!, dont use urlencode() function in associative array, in connection with sendOpenPayuDocumentAuth() function.
        // urlencoding is done inside OpenPayU SDK, file openpayu.php.

        $item = array(
            'Quantity' => 1,
            'Product' => array (
                'Name' => $orderName,
                'UnitPrice' => array (
                    'Gross' => $order->price*100, 'Net' => 0, 'Tax' => 0, 'TaxRate' => '0', 'CurrencyCode' => 'PLN'
                )
            )
        );

        // shoppingCart structure
        $shoppingCart = array(
            'GrandTotal' => $order->price*100,
            'CurrencyCode' => 'PLN',
            'ShoppingCartItems' => array (
                array ('ShoppingCartItem' => $item),
            )
        );

        // Order structure
        $orderPayU = array (
            'MerchantPosId' => OpenPayU_Configuration::getMerchantPosId(),
            'SessionId' => $order->session,
            //'OrderUrl' => $myUrl . '/order_cancel.php?order=' . rand(), // is url where customer will see in myaccount, and will be able to use to back to shop.
            'OrderCreateDate' => date("c"),

            'OrderDescription' => 'Nr zam:'.$order->id_order,
            'MerchantAuthorizationKey' => OpenPayU_Configuration::getPosAuthKey(),
            'OrderType' => 'VIRTUAL', // options: MATERIAL or VIRTUAL
            'ShoppingCart' => $shoppingCart
        );

        // OrderCreateRequest structure
        $OCReq = array (
            'ReqId' =>  md5(rand()),
            'CustomerIp' => $_SERVER['REMOTE_ADDR'], // note, this should be real ip of customer retrieved from $_SERVER['REMOTE_ADDR']
            'NotifyUrl' => $myUrl . '/payment/payu/notify', // url where payu service will send notification with order processing status changes
            'OrderCancelUrl' => $myUrl . '/payment/payu/cancel',
            'OrderCompleteUrl' => $myUrl . '/payment/payu/succes/'.$order->id_order,
            'OrderId' => $order->id_order,
            'Order' => $orderPayU
        );

        $customer = array(
            'Email' => $user->email,
            'FirstName' => $user->name,
            'LastName' => $user->lname,
            'Phone' => $user->phone,
            'Language' => 'pl_PL',
        );

        if(!empty($customer))
           $OCReq['Customer'] = $customer;


        // send message OrderCreateRequest, $result->response = OrderCreateResponse message
        $result = OpenPayU_Order::create($OCReq);


        if ($result->getSuccess()) {

            $result = OpenPayU_OAuth::accessTokenByClientCredentials();
            echo json_encode(array('token'=>$result->getAccessToken(),'sessionid'=>$order->session));



        } else {
            echo json_encode(array('error'=>$result->getError(),'message'=>$result->getMessage()));
        }
    } else {
        print json_encode($result);
    }
    

    
});

$app->post('/payment/payu/notify', function () use ($app) {
    try {	
        // Processing notification received from payu service.
        // Variable $notification contains array with OrderNotificationRequest message.
        $result = OpenPayU_Order::consumeMessage($app->request()->post('DOCUMENT'));
        // TODO POSTA WYWAL !

        if ($result->getMessage() == 'OrderNotifyRequest') {
                // Second step, request details of order data.
                // Variable $response contain array with OrderRetrieveResponse message.
                $result = OpenPayU_Order::retrieve($result->getSessionId());
    $response = $result->getResponse();
    // $response = $serial;
    $result = array();

    //flatten the response array to make easier to receive data
    array_walk_recursive($response, function ($value, $key) use (& $result) {
      $result[$key] = $value;
    });
    $arrayResponse = $result;
    if($arrayResponse['OrderStatus'] == 'ORDER_STATUS_COMPLETE' && $arrayResponse['PaymentStatus'] == 'PAYMENT_STATUS_END') {
        $order = Model::factory('Order')->where('session',$arrayResponse['SessionId'])->find_one();
        if($order instanceof Order) {
          if($order->status !='paid') {
            $order->status = 'paid';
            $delivery = $order->delivery()->find_one();
            SendMail($delivery->from_email, array('EMAIL'=>$delivery->from_email), 8);
            SendMail('marcin.jastrzebski@poludniowo.pl', array('ID'=>$order->id_order), 9);
            $order->save();
            $courierManager = new \lib\CourierManager($order->id_courier);
            $courier = $courierManager->getCourier();
            if(!$courier->ship_from_db($order->id_order)) file_put_contents("debug.txt", "Błąd w trakcie wysyłania danych kurierowi \n\n",FILE_APPEND);
            else file_put_contents("debug.txt", "kurier wysłany \n\n",FILE_APPEND);
          }
           
        }
    }

    //   write_to_file("debug.txt", "recursive array: \n " . serialize($arrayResponse) . "\n\n");
        }		
    } catch (Exception $e) {

    }
});

$app->get('/payment/payu/cancel', function () use ($app) {
    
});

$app->get('/payment/payu/succes/:id', function ($id) use ($app) {
  try {
   // throw new Exception("Error while proceeding order in PayU", 101);
     
      //TODO sprawdzic czy dane zamowienie nie ma juz statusu OK
      if(!empty($id)) {
        if(strpos($id,'error')!==false)
            throw new Exception('Wystąpił błąd w trakcie procesowania płatności przez PayU.');
        
        $id = onlyNumber($id);        
        $orderNumber = $id;

        $order = Model::factory('Order')->find_one($orderNumber);    
        
        if($order->status == 'yes') throw new Exception('To zamówienie zostało już zrealizowane');
       
        $req = array(
            'ReqId' => md5(rand()),
            'MerchantPosId' => OpenPayU_Configuration::getMerchantPosId(),
            'SessionId' => md5('nadajto'.$orderNumber)
        );

        $OrderRetrieveRequestUrl = OpenPayU_Configuration::getServiceUrl() . 'co/openpayu/OrderRetrieveRequest';

        $oauthResult = OpenPayu_OAuth::accessTokenByClientCredentials();

        OpenPayU::setOpenPayuEndPoint($OrderRetrieveRequestUrl . '?oauth_token=' . $oauthResult->getAccessToken());
        $xml = OpenPayU::buildOrderRetrieveRequest($req);

        $merchantPosId = OpenPayU_Configuration::getMerchantPosId();
        $signatureKey = OpenPayU_Configuration::getSignatureKey();
        $response = OpenPayU::sendOpenPayuDocumentAuth($xml, $merchantPosId, $signatureKey);

        $status = OpenPayU::verifyOrderRetrieveResponseStatus($response);
 
        $result = new OpenPayU_Result();
        $result->setStatus($status);
        $result->setError($status['StatusCode']);

        if (isset($status['StatusDesc']))
            $result->setMessage($status['StatusDesc']);
//CustomerRecord
        $result->setRequest($req);
        $result->setResponse($response);


            $assoc = OpenPayU::parseOpenPayUDocument($response);
            $result->setResponse($assoc);

            $arrayResponse = array();

            array_walk_recursive($assoc, function ($value, $key) use (& $arrayResponse) {
              $arrayResponse[$key] = $value;
            });

//sprawdzenie statusu zamowienia dla danego sessionId           
            $orderStatus = $assoc['OpenPayU']['OrderDomainResponse']['OrderRetrieveResponse']['OrderStatus'];
            $paymentStatus = $assoc['OpenPayU']['OrderDomainResponse']['OrderRetrieveResponse']['PaymentStatus'];
          
            $result->setSessionId($assoc['OpenPayU']['OrderDomainResponse']['OrderRetrieveResponse']['SessionId']);
            $sessionId = $result->getSessionId();
            
            $email = $assoc['OpenPayU']['OrderDomainResponse']['OrderRetrieveResponse']['CustomerRecord']['Email'];

            $result->setSuccess(($status['StatusCode'] == 'OPENPAYU_SUCCESS' && ($orderStatus == 'ORDER_STATUS_PENDING' || $orderStatus == 'ORDER_STATUS_COMPLETE') ) ? TRUE : FALSE);
  //          $result->setSuccess(($status['StatusCode'] == 'OPENPAYU_SUCCESS' ) ? TRUE : FALSE);
            

       if($result->getSuccess() ) {
          
      
          
          if($orderStatus == 'ORDER_STATUS_PENDING') {
            $message = "Czekamy na potwierdzenie płatnosci z PayU.\n Jak tylko je otrzymamy, wyślemy do Ciebie email z etykietą dla kuriera.";
          } else {
                
//TODO sprawdzic czy wartosci zamowienia sie pokrywaja z tym co jest w bazie              
              $paidAmount = $assoc['OpenPayU']['OrderDomainResponse']['OrderRetrieveResponse']['PaidAmount'];
              if($order->price==$paidAmount/100) {
                  $message = 'Sprawdź swoja skrzynkę email. Jeżeli nie znajdziesz tam etykiety dla kuriera prosimy o kontakt.';
              } else throw new Exception("Kwota zamówienia nie zgadza się z kwotą przesłaną do PayU!");
          }
           $app->render('paymentok.php',array('message'=>$message));
           exit();
 
       } else {
                    
  //        elseif($orderStatus == '')
           throw new Exception("Coś poszło nie tak. Prosimy złożyć zamówienie ponownie. Dziękujemy!", 103);
//wpisac debuga

       }
  } else throw new Exception("ID zamówienia nie jest poprawne!");
} catch (Exception $e) {
      $app->render('paymentfail.php',array('error'=>$e->getMessage()));
  }
});

$app->get('/payment/payu/ok', function () use ($app) {
    $app->render('paymentok.php',array('title'=>'title'));
});



/*
 * Prepay - skarbonka (obsługa wpłaty w routes/customer)
 */


$app->post('/payment/payu/prepay/notify', function () use ($app) {
    try {	
        // Processing notification received from payu service.
        // Variable $notification contains array with OrderNotificationRequest message.
        $result = OpenPayU_Order::consumeMessage($app->request()->post('DOCUMENT'));
        // TODO POSTA WYWAL !
        if ($result->getMessage() == 'OrderNotifyRequest') {
                // Second step, request details of order data.
                // Variable $response contain array with OrderRetrieveResponse message.
                $result = OpenPayU_Order::retrieve($result->getSessionId());
    $response = $result->getResponse();
    // $response = $serial;
    $result = array();

    //flatten the response array to make easier to receive data
    array_walk_recursive($response, function ($value, $key) use (& $result) {
      $result[$key] = $value;
    });
    $arrayResponse = $result;
    if($arrayResponse['OrderStatus'] == 'ORDER_STATUS_COMPLETE' && $arrayResponse['PaymentStatus'] == 'PAYMENT_STATUS_END') {
        $order = Model::factory('Prepay')->where('session',$arrayResponse['SessionId'])->find_one();
        if($order instanceof Prepay) {
          if($order->status !='paid') {
            $order->status = 'paid';
            $order->save();
            $customer = $order->customer()->find_one();
            $return = SendMail($customer->email, array('AMOUNT'=>$order->amount), 11);
            
            $return = SendMail('nadajtoolzalogistic@gmail.com', array('ID'=>$order->id_prepay), 10);
            
            
          }
           
        }
    }

        }		
    } catch (Exception $e) {
        file_put_contents('debug.txt',$e->getMessage(),FILE_APPEND);
        
    }
});

$app->get('/payment/payu/prepay/cancel', function () use ($app) {
    
});

$app->get('/payment/payu/prepay/succes/:id', function ($id) use ($app) {
  try {
   // throw new Exception("Error while proceeding order in PayU", 101);
     
      //TODO sprawdzic czy dane zamowienie nie ma juz statusu OK
      if(!empty($id)) {
        if(strpos($id,'error')!==false)
            throw new Exception('Wystąpił błąd w trakcie procesowania płatności przez PayU.');
        
        $id = onlyNumber($id);        
        $orderNumber = $id;

        $order = Model::factory('Prepay')->find_one($orderNumber);    
        
        if($order->status == 'yes') throw new Exception('Ta płatność została już zrealizowana.');
       
        $req = array(
            'ReqId' => md5(rand()),
            'MerchantPosId' => OpenPayU_Configuration::getMerchantPosId(),
            'SessionId' => md5('prepay'.$orderNumber)
        );

        $OrderRetrieveRequestUrl = OpenPayU_Configuration::getServiceUrl() . 'co/openpayu/OrderRetrieveRequest';

        $oauthResult = OpenPayu_OAuth::accessTokenByClientCredentials();

        OpenPayU::setOpenPayuEndPoint($OrderRetrieveRequestUrl . '?oauth_token=' . $oauthResult->getAccessToken());
        $xml = OpenPayU::buildOrderRetrieveRequest($req);

        $merchantPosId = OpenPayU_Configuration::getMerchantPosId();
        $signatureKey = OpenPayU_Configuration::getSignatureKey();
        $response = OpenPayU::sendOpenPayuDocumentAuth($xml, $merchantPosId, $signatureKey);

        $status = OpenPayU::verifyOrderRetrieveResponseStatus($response);
 
        $result = new OpenPayU_Result();
        $result->setStatus($status);
        $result->setError($status['StatusCode']);

        if (isset($status['StatusDesc']))
            $result->setMessage($status['StatusDesc']);
//CustomerRecord
        $result->setRequest($req);
        $result->setResponse($response);


            $assoc = OpenPayU::parseOpenPayUDocument($response);
            $result->setResponse($assoc);

            $arrayResponse = array();

            array_walk_recursive($assoc, function ($value, $key) use (& $arrayResponse) {
              $arrayResponse[$key] = $value;
            });

//sprawdzenie statusu zamowienia dla danego sessionId           
            $orderStatus = $assoc['OpenPayU']['OrderDomainResponse']['OrderRetrieveResponse']['OrderStatus'];
            $paymentStatus = $assoc['OpenPayU']['OrderDomainResponse']['OrderRetrieveResponse']['PaymentStatus'];
          
            $result->setSessionId($assoc['OpenPayU']['OrderDomainResponse']['OrderRetrieveResponse']['SessionId']);
            $sessionId = $result->getSessionId();
            
            $email = $assoc['OpenPayU']['OrderDomainResponse']['OrderRetrieveResponse']['CustomerRecord']['Email'];

            $result->setSuccess(($status['StatusCode'] == 'OPENPAYU_SUCCESS' && ($orderStatus == 'ORDER_STATUS_PENDING' || $orderStatus == 'ORDER_STATUS_COMPLETE') ) ? TRUE : FALSE);
  //          $result->setSuccess(($status['StatusCode'] == 'OPENPAYU_SUCCESS' ) ? TRUE : FALSE);
       if($result->getSuccess() ) {
          
      
          
          if($orderStatus == 'ORDER_STATUS_PENDING') {
            $message = "Czekamy na potwierdzenie płatnosci z PayU.\n Jak tylko je otrzymamy, Twoja skarbonka zostanie zasilona.";
          } else {
                
//TODO sprawdzic czy wartosci zamowienia sie pokrywaja z tym co jest w bazie              
              $paidAmount = $assoc['OpenPayU']['OrderDomainResponse']['OrderRetrieveResponse']['PaidAmount'];
              if($order->amount==$paidAmount/100) {
                  $message = 'Twoja skarbonka powinna zostać zasilona. Jeżeli tak się nie stało prosimy o kontakt';
              } else throw new Exception("Kwota definiowana nie zgadza się z kwotą przesłaną do PayU!");
          }
           $app->render('paymentok.php',array('message'=>$message));
           exit();
 
       } else {
                    
  //        elseif($orderStatus == '')
           throw new Exception("Coś poszło nie tak. Prosimy złożyć zamówienie ponownie. Dziękujemy!", 103);
//wpisac debuga

       }
  } else throw new Exception("ID zamówienia nie jest poprawne!");
} catch (Exception $e) {
      $app->render('paymentfail.php',array('error'=>$e->getMessage()));
  }
});

$app->get('/payment/payu/prepay/ok', function () use ($app) {
    $app->render('paymentok.php',array('title'=>'title'));
});







$app->get('/payment/ups/test', function () use ($app) {
            $courierManager = new \lib\CourierManager(1);
            $courier = $courierManager->getCourier();
            if(!$courier->ship_from_db(228)) file_put_contents("debug.txt", "Błąd w trakcie wysyłania danych kurierowi \n\n",FILE_APPEND);
            else file_put_contents("debug.txt", "kurier wysłany \n\n",FILE_APPEND);
    
});
$app->get('/payment/ups/test2', function () use ($app) {
    try {
            $addCOD = \Model::factory('OrderAdditional')->where('id_order',128)->filter('getInsurance')->find_one();
            if(count($addCOD)>0) {
                    $paymentMethod = '02';
                } else $paymentMethod = '01';
                var_dump($addCOD);
    }catch(Exception $e) {print $e->getMessage();}  
    
});

$app->get('/payment/ups/price', function () use ($app) {
    try {
            $price = new \UPS\Parcel(70, 70, 70, 15, 1);
            print $price->getPrice();
    }catch(Exception $e) {print $e->getMessage();}  
    
});


?>
