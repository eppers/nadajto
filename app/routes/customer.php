<?php

$authUser=function () use($app) {
    if(!isset($_SESSION['user']['id_customer']) )
    $app->redirect('/logowanie');    
};

$app->get('/user', $authUser, function () use ($app) {
    $app->redirect('/user/');
});

$app->get('/user/', $authUser, function () use ($app) {
    $app->redirect('/user/zamowienia');
});

$app->get('/user/faktury', $authUser, function () use ($customer) {
    
    $invoices = Model::factory('Invoice')->filter('getCustomerInvoices',$_SESSION['user']['id_customer'])->find_many();
    if (count($invoices)>0){
      
      $invsArray = array();
      $inv = array();
    
      foreach($invoices as $invoice) {
        if($invoice instanceof Invoice) {
          $inv['id'] = $invoice->id_invoice;
          $inv['date'] = $invoice->date;
          $inv['name'] = $invoice->name;
          
          $invsArray[] = $inv;
        }    
      }    
    }    

    $customer->render('faktury.php',array('invoices'=>$invsArray));
    
});

$app->get('/user/faktury/szczegoly/:id', $authUser, function ($id) use ($customer) {
  $orders = Model::factory('Order')->where('id_invoice',$id)->where('id_customer',$_SESSION['user']['id_customer'])->find_many();
    if (count($orders)>0){
      
      $ordsArray = array();
      $ord = array();
          
      foreach($orders as $order) {
        if($order instanceof Order) {
        
          $courier = Model::factory('Courier')->find_one($order->id_courier);
        
          $ord['id'] = $order->id_order;
          $ord['tracking'] = $order->tracking;
          $ord['date'] = $order->date;
          $ord['amount'] = $order->price;
          $ord['courier'] = $courier->name;
          $ord['delivery'] = $order->delivery_type;
          $ord['payment'] = $order->payment;
          
          $ordsArray[] = $ord;
        }    
      }    
    }    
    $customer->render('zamowienia.php',array('orders'=>$ordsArray));
   
});

$app->get('/user/faktury/pobierz/:id', $authUser, function ($id) use ($customer) {
  $invoice = Model::factory('Invoice')->find_one($id);
  $order = $invoice->orders()->find_one();

  if($order instanceof \Order && $order->id_customer==$_SESSION['user']['id_customer']) {
  $filePath = $GLOBALS['REALPATH'];        
          
  $fileName = $invoice->filename.'.pdf';
  if(substr($filePath,-1)!="/") $filePath .= "/";
  $pathOnHd = $filePath .'invoices/'. $fileName;

    if ($download = fopen ($pathOnHd, "r")) {
        $size = filesize($pathOnHd);
        $fileInfo = pathinfo($pathOnHd);
        $ext = strtolower($fileInfo["extension"]);

        switch ($ext) {
            case "pdf":
                    header("Content-type: application/pdf"); 
                    header("Content-Disposition: attachment; filename=\"{$fileInfo["basename"]}\"");
                break;
            default;
                header("Content-type: application/octet-stream");
                header("Content-Disposition: filename=\"{$fileInfo["basename"]}\"");
        }

        header("Content-length: $size");

        while(!feof($download)) {
            $buffer = fread($download, 2048);
            echo $buffer;
        }
        fclose ($download);
    }
    exit;
  }

   
});

$app->get('/faktury/generuj', $authUser, function () use ($customer) {
    
   
$users = \Model::factory('Customer')->where('onetime',0)->find_many();
foreach($users as $user) {
    if($user instanceof \Customer) {
        \lib\InvoicePDF::generateLastMonth($user);
    }
}


});

/*
 * Orders ......................................................................
 */

$app->get('/user/zamowienia', $authUser, function () use ($customer) {
    $user = Model::factory('Customer')->find_one($_SESSION['user']['id_customer']);
    $orders = $user->orders()->order_by_desc('date')->find_many();
    
      
    foreach($orders as $order) {
        if($order instanceof Order) {
          $ord = array();
          $courier = Model::factory('Courier')->find_one($order->id_courier);
          $delivery = $order->delivery()->find_one();
          $additionals = $order->additionals()->find_many();

          $ord['id'] = $order->id_order;
          $ord['tracking'] = $order->tracking;
          $ord['date'] = $order->date;
          $ord['amount'] = $order->price;
          $ord['courier'] = $courier->name;
          $ord['delivery'] = $order->delivery_type;
          $ord['payment'] = $order->payment;
          foreach($additionals as $add) {
              if($add instanceof OrderAdditional) {
                  $addType = Model::factory('Additional')->find_one($add->id_add);
                  switch ($addType->type) {
                      case 'COD' : $ord['COD'] = (!empty($add->price))?$add->price : 0; break;
                      case 'ROD' : $ord['ROD'] = true; break;
                      case 'Insurance' : $ord['Insurance'] = (!empty($add->price))?$add->price : 0; break;
                      default: ;
                  }
              }
          }
        
          $ord['date_repay'] = date('Y-m-d', strtotime($delivery->date.' +4 Weekday'));    
          if($delivery instanceof Delivery) {
              if($delivery->status!='D' && $delivery->status!='MV' && $delivery->status!='ER' ) {
                   $upsDelivery = new UPS\Delivery($order->tracking);
                   $delivery->status = $upsDelivery->getStatus();
                   $ord['code'] = $delivery->status;

                   $delivery->save();
              }
          }
          $ord['status'] = \lib\Delivery::printStatus($delivery->status);
          $ordsArray[] = $ord;
        }       
    }    
         
    $customer->render('zamowienia.php',array('orders'=>$ordsArray));
   
});

  $app->get('/user/zamowienia/dostawa/:id', $authUser, function ($id) use ($customer) {
  
  $order = Model::factory('Order')->find_one($id);
  $delivery = Model::factory('Delivery')->where('id_order',$id)->find_one();
  $del = array();              
    
  if($delivery instanceof Delivery && $order->id_customer==$_SESSION['user']['id_customer']) {
    
    $del['invoice'] = $order->id_invoice;    
        
    $del['id'] = $delivery->id_del;
    $del['date'] = $delivery->date;
    
    $del['from_cmp'] = $delivery->from_company;
    $del['from_nip'] = $delivery->from_nip;
    $del['from_name'] = $delivery->from_name;
    $del['from_lname'] = $delivery->from_lname;
    $del['from_addr'] = $delivery->from_street.' '.$delivery->from_no;
    if(!empty($delivery->from_no2))$del['from_addr'] .= '/'.$delivery->from_no2;   
    $del['from_city'] = $delivery->city;
    $del['from_zip'] = $delivery->from_zip;
    $del['from_country'] = $delivery->from_country;
    $del['from_email'] = $delivery->from_email;
    $del['from_phone'] = $delivery->from_phone;
    
    $del['to_cmp'] = $delivery->to_company;
    $del['to_nip'] = $delivery->to_nip;
    $del['to_name'] = $delivery->to_name;
    $del['to_lname'] = $delivery->to_lname;
    $del['to_addr'] = $delivery->to_street.' '.$delivery->to_no;
    if(!empty($delivery->to_no2))$del['to_addr'] .= '/'.$delivery->to_no2;   
    $del['to_city'] = $delivery->city;
    $del['to_zip'] = $delivery->to_zip;
    $del['to_country'] = $delivery->to_country;
    $del['to_email'] = $delivery->to_email;
    $del['to_phone'] = $delivery->to_phone;
    
  }    
       
  $customer->render('dostawa.php',array('delivery'=>$del));    
});

/*
 * Config .....................................................
 */

$app->get('/user/konfiguruj/', $authUser, function () use ($customer) {
  $user = Model::factory('Customer')->find_one($_SESSION['user']['id_customer']);
  $prepay = $user->prepays()->filter('sum');
  $payULink = OpenPayu_Configuration::getSummaryUrl();  
  
        if($user instanceof Customer) {

            $addr = explode(' ', $user->addr);//zrobic adres na zasadzie backslashy
           
            $client['id'] = $user->id_customer;
            $client['company'] = $user->company;
            $client['nip'] = $user->nip;
            $client['name'] = $user->name;
            $client['lname'] = $user->lname;
            $client['street'] = $addr[0];
            $client['no1'] = $addr[1];
            if(!empty($addr[2])) $client['no2'] = $addr[2];
            $client['city'] = $user->city;
            $client['zip1'] = substr($user->zip,0,2);
            $client['zip2'] = substr($user->zip,2);
            $client['email'] = $user->email;
            $client['phone'] = $user->phone;
            $client['prepay'] = $prepay;

        }       
 
         
    $customer->render('config.php',array('user'=>$client,'payULink'=>$payULink));
   
});

$app->post('/user/konfiguruj/',$authUser, function () use ($app) {
    $id = $_SESSION['user']['id_customer'];
    
    $customer = Model::factory('Customer')->find_one($id);

    
        if($customer instanceof Customer) {

            $pass=$app->request()->post('pass');
            $customer->company = $app->request()->post('company');
            $customer->nip = $app->request()->post('nip') ;
            $customer->addr = $app->request()->post('addr_street').' '.$app->request()->post('addr_no').' '.$app->request()->post('addr_no2') ;
            $customer->city = $app->request()->post('city') ;
            $customer->zip = $app->request()->post('zip1').$app->request()->post('zip2') ;
            $customer->phone = $app->request()->post('phone');
            if(!empty($pass))$customer->pass = md5($pass);
            
            $customer->save();
        }       
    
        $app->redirect('/user/konfiguruj');
    
});

/* 
 * Skarbonka
 */
$app->post('/prepay/add', function () use ($app) {

$directory = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$myUrl = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] .$directory;

$prepay = \Model::factory('Prepay')->create();


$prepay->id_customer = $_SESSION['user']['id_customer'];
$prepay->date = date('Y-m-d H:i:s');
$prepay->amount = floatval($app->request()->post('amount'));
$prepay->save(); 
$orderName = 'NadajTo - doładowanie';
$orderNumber = $prepay->id(); 
$session = md5('prepay'.$orderNumber);
$prepay->session = $session;
$prepay->save();

if(!$name = onlyLetter($app->request()->post('name'))) {$error['input'][] = 'name'; $error['msg'][] = 'Niepoprawne imię';};
if(!$lname = onlyLetter($app->request()->post('lname'))) {$error['input'][] = 'lname'; $error['msg'][] = 'Niepoprawne nazwisko';};
if(!$email = filter_var($app->request()->post('email'),FILTER_VALIDATE_EMAIL)) {$error['input'][] = 'email'; $error['msg'][] = 'Niepoprawny email';};
if(!$phone = clearPhone($app->request()->post('phone'))) {$error['input'][] = 'phone'; $error['msg'][] = 'Niepoprawne telefon';};

if(count($error['input'])>0) {
    print json_encode($error);
    exit();
}

$item = array(
        'Quantity' => 1,
        'Product' => array (
            'Name' => $orderName,
            'UnitPrice' => array (
                'Gross' => $prepay->amount*100, 'Net' => 0, 'Tax' => 0, 'TaxRate' => '0', 'CurrencyCode' => 'PLN'
            )
        )
    );

    // shoppingCart structure
    $shoppingCart = array(
        'GrandTotal' => $prepay->amount*100,
        'CurrencyCode' => 'PLN',
        'ShoppingCartItems' => array (
            array ('ShoppingCartItem' => $item),
        )
    );

    // Order structure
    $order = array (
        'MerchantPosId' => OpenPayU_Configuration::getMerchantPosId(),
        'SessionId' => $session,
        //'OrderUrl' => $myUrl . '/order_cancel.php?order=' . rand(), // is url where customer will see in myaccount, and will be able to use to back to shop.
        'OrderCreateDate' => date("c"),

        'OrderDescription' => 'Nr zam:'.$orderNumber,
        'MerchantAuthorizationKey' => OpenPayU_Configuration::getPosAuthKey(),
        'OrderType' => 'VIRTUAL', // options: MATERIAL or VIRTUAL
        'ShoppingCart' => $shoppingCart
    );

    // OrderCreateRequest structure
    $OCReq = array (
        'ReqId' =>  md5(rand()),
        'CustomerIp' => $_SERVER['REMOTE_ADDR'], // note, this should be real ip of customer retrieved from $_SERVER['REMOTE_ADDR']
        'NotifyUrl' => $myUrl . '/payment/payu/prepay/notify', // url where payu service will send notification with order processing status changes
        'OrderCancelUrl' => $myUrl . '/payment/payu/prepay/cancel',
        'OrderCompleteUrl' => $myUrl . '/payment/payu/prepay/succes/'.$orderNumber,
        'OrderId' => 'nadajto-prepay'.$orderNumber,
        'Order' => $order
    );

    $customer = array(
        'Email' => $email,
        'FirstName' => $name,
        'LastName' => $lname,
        'Phone' => $phone,
        'Language' => 'pl_PL',
    );

    if(!empty($customer))
       $OCReq['Customer'] = $customer;


    // send message OrderCreateRequest, $result->response = OrderCreateResponse message
    $result = OpenPayU_Order::create($OCReq);


    if ($result->getSuccess()) {

        $result = OpenPayU_OAuth::accessTokenByClientCredentials();
        echo json_encode(array('token'=>$result->getAccessToken(),'sessionid'=>$session));



    } else {
        echo json_encode(array('error'=>$result->getError(),'message'=>$result->getMessage()));
    }
    
});
 $app->get('/skarbonka/payin', function () use ($customer) {
    $user = Model::factory('Customer')->find_one(1);
    $amount = '10';
    $cyfra = '100';
    var_dump( $user->prepays()->filter('sum'));



});
 $app->get('/czas', function () use ($app) {
     $dane = 3;
     $i = 0;
     $date = '2013-12-05';
     while(true)
     try {
         print str_replace("-","",$date);
         if($i<3) throw new Exception ('błąd');
         print 'poszlo';
         print $i;
         break;
     }
     catch( Exception $e) {
         if($i++==$dane) throw $e;
         else $date = date('Y-m-d', strtotime($date.' +1 Weekday'));
     }
 });
?>