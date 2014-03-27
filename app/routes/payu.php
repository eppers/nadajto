<?php

/*
 * PAYU //////////////////////////////////////////////////////
 */

$app->post('/payment/payu/sending', function () use ($app) {
    
    //TODO spradzenie czy wymairy sie zgadzaja dla danego kuriera oraz czy mozna wyslac np palete UPSem
    $delivery = Model::factory('Delivery')->create();
    $parcel = Model::factory('Parcel')->create();
    
    $typeTmp = 0;
    $weight = 1;
    $dim['length'] = 1;
    $dim['width'] = 1;
    $dim['height'] = 1;
    
    if(!$parcel->weight = onlyNumber($app->request()->post('weight'))) {$error['input'][] = 'pkg_weight'; $error['msg'][] = 'Niepoprawna waga paczki';};
    if(!$parcel->length = onlyNumber($app->request()->post('length'))) {$error['input'][] = 'pkg_length'; $error['msg'][] = 'Niepoprawna długość paczki';};
    if(!$parcel->width = onlyNumber($app->request()->post('width'))) {$error['input'][] = 'pkg_width'; $error['msg'][] = 'Niepoprawna szerokość paczki';};
    if(!$parcel->height = onlyNumber($app->request()->post('height'))) {$error['input'][] = 'pkg_height'; $error['msg'][] = 'Niepoprawna wysokość paczki';};
    
    if($app->request()->post('pkg_type')>3) {$error['input'][] = 'pkg_type'; $error['msg'][] = 'Zły typ wysyłki';};
    if(!$parcel->type = onlyNumber($app->request()->post('pkg_type'))) {$error['input'][] = 'pkg_type'; $error['msg'][] = 'Zły typ wysyłki';};
    
    if(!$nad_email = filter_var($app->request()->post('nad_email'),FILTER_VALIDATE_EMAIL)){$error['input'][] = 'nad_email'; $error['msg'][] = 'Niepoprawny email';};
    $nad_email2 = $app->request()->post('nad_email2');
    $nad_company = $app->request()->post('nad_company');
    $nad_company = clearName($nad_company);

    $odb_company = $app->request()->post('odb_company');
    $odb_company = clearName($odb_company);    
    $nad_nip = preg_replace('/[^\s0-9\-]/u', "", trim($app->request()->post('nad_nip')));
    $odb_nip = preg_replace('/[^\s0-9\-]/u', "", trim($app->request()->post('odb_nip')));
    if($app->request()->post('bank')!=='false') {
        if(!$bank = onlyNumber($app->request()->post('bank'))) {$error['input'][] = 'account-no'; $error['msg'][] = 'Niepoprawny numer konta';};
    }

    if(strcmp($nad_email,$nad_email2)!=0){$error['input'][] = 'nad_email'; $error['msg'][] = 'Niepoprawny email';}
    else $delivery->from_email=$nad_email;
    if(!$from_name = clearName($app->request()->post('nad_imie'))) {$error['input'][] = 'nad_imie'; $error['msg'][] = 'Niepoprawne imię';};
    if(!$from_lname = clearName($app->request()->post('nad_nazwisko'))) {$from_lname='';};
    if(!$from_addr = clearName($app->request()->post('nad_addr'))) {$error['input'][] = 'nad_ulica'; $error['msg'][] = 'Niepoprawna ulica';};
    if(!$from_addr_house = clearName($app->request()->post('nad_nrdomu'))) {$error['input'][] = 'nad_nrdomu'; $error['msg'][] = 'Niepoprawny numer';};
    
    if(!$to_name = clearName($app->request()->post('odb_imie'))) {$error['input'][] = 'odb_imie'; $error['msg'][] = 'Niepoprawne imię';};
    if(!$to_lname = clearName($app->request()->post('odb_nazwisko'))) {$to_lname='';};
    if(!$to_addr = clearName($app->request()->post('odb_addr'))) {$error['input'][] = 'odb_ulica'; $error['msg'][] = 'Niepoprawna ulica';};
    if(!$to_addr_house = clearName($app->request()->post('odb_nrdomu'))) {$error['input'][] = 'odb_nrdomu'; $error['msg'][] = 'Niepoprawny numer';};
    
    $courier = Model::factory('Courier')->find_one($app->request()->post('courierid'));
    $result = array();
    
    if($courier instanceof Courier) {
        $formArray = explode('&', $app->request()->post('form'));
        
        //Parcel data
        if(!empty($parcel->weight)) {
                $weight = $parcel->weight;
        }

        foreach($dim as $key=>&$row) {
                $val = $parcel->$key;
                if(!empty($val))
                    $row = (int)$val;
        }


        foreach($formArray as &$row) {
            $row = preg_replace("/[^a-zA-Z0-9\=_\-]/", "", $row);
            $arrayTemp = explode('=', $row);
            if($arrayTemp[0]=='rodzaj') {
                $typeTmp = $arrayTemp[1];
                    try {
                        $cour = new \lib\CourierManager($courier->id_courier);
                        $parcelTmp = $cour->getParcel($dim['length'], $dim['width'], $dim['height'], $weight, $typeTmp);
                    } catch(Exception $e) {
                        {$error['input'][] = 'size'; $error['msg'][] = 'Błąd rozmiaru paczki';};
                    }
                    if($parcelTmp) {
                        $result['price_net'] = number_format($parcelTmp->getPrice()-$parcelTmp->getPrice()*$_SESSION['user']['discount']/100, 2, '.', '');
                        $result['price_brut'] = number_format($result['price_net']+$result['price_net']*$GLOBALS['CONFIG']['vat']/100, 2, '.', '');
                        $result['notstand'] = $parcelTmp->getNotstand();
                        
                    } else {$error['input'][] = 'rodzaj'; $error['msg'][] = 'Ten kurier nie obsługuje tego typu wysyłki';};              
            } elseif($arrayTemp[0]=='Notstand') {
                if($result['notstand']==0) {
                     try {
                        $additional = new \lib\CourierAdditional($arrayTemp[0], $courier->name);
                        $price2 = $additional->getPrice();

                        if(is_numeric($price2)) {
                            $result['price_net'] = number_format($result['price_net'] + $price2-$price2*$_SESSION['user']['discount']/100, 2, '.', '');
                            $result['price_brut'] = number_format($result['price_net']+$result['price_net']*$GLOBALS['CONFIG']['vat']/100, 2, '.', '');
                            $result['notstand'] = 1;

                            $tempArr['id_add'] = $additional->getIdAdditional();
                            $tempArr['price'] = $price2;

                            $additionalsArr[]=$tempArr;
                        }
                    } catch(Exception $e) {
                        continue;
                    }
                }
            } else {


                $arrayAdd=explode('_', $arrayTemp[0]);
                if (count($arrayAdd)>1) { //if a name contains string courier or string check (additional_check require additional_input)
                    if($arrayAdd[0]=='pkg') {
                    continue;

                    } elseif($arrayAdd[1]=='check') {

                        if($key = array_find($arrayAdd[0].'_input',$formArray)) {
                            $val = explode('=',$formArray[$key]);
                            if(!empty($val[1])) {
                                try {
                                    $additional = new \lib\CourierAdditional($arrayAdd[0], $courier->name);
                                    $price2 = $additional->getPrice($val[1]);
                                    $COD = $additional->getCOD();
                                    if(is_numeric($price2)) {
                                        $result['price_net'] = number_format($result['price_net'] + $price2-$price2*$_SESSION['user']['discount']/100, 2, '.', '');
                                        $result['price_brut'] = number_format($result['price_net']+$result['price_net']*$GLOBALS['CONFIG']['vat']/100, 2, '.', '');

                                        $tempArr['id_add'] = $additional->getIdAdditional();
                                        $tempArr['price'] = $val[1];

                                        $additionalsArr[]=$tempArr;
                                    }
                                } catch(Exception $e) {
                                    continue;
                                }
                            }
                        } 
                    } else {
                        //update price for one courier : ups_Insurance=360   
                        //TODO if Insurance - pick the higher one
                        $val = explode('=',$arrayAdd[1]);
                        if(strtolower($arrayAdd[0])===strtolower($courier->name)) {
                            try {
                                $additional = new \lib\CourierAdditional($val[0], $arrayAdd[0]);
                                $price = $additional->getPrice($val[1]);
                                if(is_numeric($price)) {
                                    $courierId = $additional->getCourier();
                                    $result['price_net'] = number_format($result['price_net'] + $price, 2, '.', '');
                                    $result['price_brut'] = number_format($result['price_net']+$result['price_net']*$GLOBALS['CONFIG']['vat']/100, 2, '.', '');
                                    $tempArr['id_add'] = $additional->getIdAdditional();
                                    $tempArr['price'] = (!empty($val[1]))? $val[1]:0;

                                    $additionalsArr[]=$tempArr;

                                }
                            } catch(Exception $e) {

                            }
                        }
                    }
                } else {
                    $val = $arrayTemp[1];
                        try {
                            $additional = new \lib\CourierAdditional($arrayTemp[0], $courier->name);
                            $price = $additional->getPrice($val);
                            if(is_numeric($price)) {
                                $courierId = $additional->getCourier();
                                $result['price_net'] = number_format($result['price_net'] + ($price-$price*$_SESSION['user']['discount']/100), 2, '.', '');
                                $result['price_brut'] = number_format($result['price_net']+$result['price_net']*$GLOBALS['CONFIG']['vat']/100, 2, '.', '');
                                $tempArr['id_add'] = $additional->getIdAdditional();
                                $tempArr['price'] = (!empty($val))? $val:0;

                                $additionalsArr[]=$tempArr;
                            }

                        } catch(Exception $e) {
                            ;
                        }

                }
            }

        }

        if( $result['notstand'] == 1 ) {
            $notStandIncluded = 0;
            try {
                $additional = new \lib\CourierAdditional('Notstand', $courier->name);
                foreach ($additionalsArr as $row) {
                    if($row['id_add'] == $additional->getIdAdditional()){
                        $notStandIncluded = 1;
                    };
                }
                if($notStandIncluded==0) {
                    $tempArr['id_add'] = $additional->getIdAdditional();
                    $tempArr['price'] = $additional->getPrice();

                    $additionalsArr[]=$tempArr;
                }
            } catch(Exception $e) {
                ;
            }
        }
    
    } else {$error['input'][] = 'rodzaj'; $error['msg'][] = 'Ten kurier nie obsługuje tego typu wysyłki';};
    
    
    
    if(!empty($nad_company)) {
        $delivery->from_company = $nad_company;
    } else {
        $delivery->from_company = trim($from_name.' '.$from_lname);
    }
    $delivery->from_name = $from_name;
    $delivery->from_lname = $from_lname;
    $delivery->from_street = $from_addr;
    $delivery->from_no = $from_addr_house;
    
    if(!$delivery->from_city = onlyLetter($app->request()->post('nad_miasto'))) {$error['input'][] = 'nad_miasto'; $error['msg'][] = 'Niepoprawne miasto';};
    if(!$delivery->from_zip = onlyNumber($app->request()->post('nad_zip'))) {$error['input'][] = 'nad_zip'; $error['msg'][] = 'Niepoprawny kod';};
    $delivery->from_country = 'PL';
    if(!$delivery->from_phone = clearPhone($app->request()->post('nad_telef'))) {$error['input'][] = 'nad_telef'; $error['msg'][] = 'Niepoprawny numer telefonu';};

    if($delivery->from_zip) {

        $zip = clearZip($app->request()->post('nad_zip'));
        $cityFrom = onlyLetter($app->request()->post('nad_miasto'));
        $cities = \Model::factory('City')->where('pna',$zip)->find_many();
        $okCity = 0;
        if(!empty($cityFrom)) {
            foreach($cities as $city) {
                if(strpos(strtolower($city->city),strtolower($cityFrom))!==false) {
                    $okCity = 1;
                }
            }
        }
        if($okCity==0) {
            {$error['input'][] = 'nad_miasto'; $error['msg'][] = 'Kod nie pasuje do miasta';};
        }
    }
        
//    if(!empty($odb_company)) {
//        $delivery->to_company = $odb_company;
//    } else {
//        $delivery->to_company = $to_name.' '.$to_lname;
//    }
    
    if($app->request()->post('odb_priv')==1) $delivery->to_company = $to_name.' '.$to_lname;
    else $delivery->to_company = $to_name;

    $delivery->to_name = $to_name;
    $delivery->to_lname = $to_lname;
    $delivery->to_street = $to_addr;
    $delivery->to_no = $to_addr_house;
    if(!$delivery->to_city = onlyLetter($app->request()->post('odb_miasto'))) {$error['input'][] = 'odb_miasto'; $error['msg'][] = 'Niepoprawne miasto';};//if(!$ups->to_address->city = clearName($app->request()->post('odb_miasto'))) {$error['input'][] = 'odb_miasto'; $error['msg'][] = 'Niepoprawne miasto';};
    if(!$delivery->to_zip = onlyNumber($app->request()->post('odb_zip'))) {$error['input'][] = 'odb_zip'; $error['msg'][] = 'Niepoprawny kod';};
    $delivery->to_country = 'PL';
    if(!$delivery->to_phone = clearPhone($app->request()->post('odb_telef'))) {$error['input'][] = 'odb_telef'; $error['msg'][] = 'Niepoprawny numer telefonu';};
    
    if($delivery->to_zip) {
        $zip = clearZip($app->request()->post('odb_zip'));
        $cityTo = onlyLetter($app->request()->post('odb_miasto'));
        $cities = \Model::factory('City')->where('pna',$zip)->find_many();
        $okCity = 0;
        if(!empty($cityTo)) {        
            foreach($cities as $city) {

                //print json_encode($city->city.' '.$cityFrom);
                if(strpos(strtolower($city->city),strtolower($cityTo))!==false) {
                    $okCity = 1;
                }
            }
        }
        if($okCity==0) {
            {$error['input'][] = 'odb_miasto'; $error['msg'][] = 'Kod nie pasuje do miasta';};
        }
    }
    
    $date = $app->request()->post('data_nad');
    if(empty($date)) {
        if(date('N') >= 6)
            $date = date('Y-m-d', strtotime(' +1 Weekday')); 
        else {
            $hour = date(H);
            if($hour>=12) $date = date('Y-m-d', strtotime(' +1 Weekday')); 
            else $date = date('Y-m-d');
        }
        $delivery->date = $date;
    } else {
        if (strtotime($date) === false || strtotime($date." 12:00:00")<strtotime('now')) {
            $error['input'][] = $date;
        } else {
            if (date('N', strtotime($date)) >= 6)
                $delivery->date = date('Y-m-d', $date." +1 Weekday");
            else
                $delivery->date = date('Y-m-d', $date);
        }
    }
    //TODO w zaleznosci od rodzaju przesylki wyswietla typ
   
  
    
    if(count($error['input'])>0) {
        print json_encode($error);
        exit();
    }
// TODO dane z bazy jezeli niejednorazowy    
    $order = Model::factory('Order')->create();
    $userId = $_SESSION['user']['id_customer'];
    $order->delivery_type = $typeTmp;    
    try {
    
        if($courier instanceof Courier) {

            if(!empty($userId)) {
                $user = Model::factory('Customer')->find_one($userId);

                if(!$user instanceof Customer) {
                    throw new Exception('Błąd z ID usera. Skontaktuj się z administracją.');
                }
            } else {
                //TODO jezeli nie ma takiego klienta to dodac go do bazy
                $user = Model::factory('Customer')->create();
                $user->company = $delivery->from_company;
                $user->nip = $nad_nip;
                $user->name = $delivery->from_name;
                $user->lname = $delivery->from_name;
                $user->addr =  $delivery->from_street.' '.$delivery->from_no;
                $user->city = $delivery->from_city;
                $user->zip = $delivery->from_zip;
                $user->country = $delivery->from_country;
                $user->phone = $delivery->from_phone;
                $user->email = $delivery->from_email;
                $user->onetime = 1;
                
                $user->save();
                $userId = $user->id();
                
            }
            
            //server podaje zla strefe czasowa
            date_default_timezone_set('Europe/Warsaw');
            $order->date = date('Y-m-d H:i:s');

           
            $amount = number_format($result['price_brut'], 2, '.', '');
        //TODO zapis sessji do zamowienia ?
            if($COD) $order->payment = 2;
            $order->price = $amount;
            $order->price_netto = number_format($result['price_net'], 2, '.', '');
            $order->id_customer = $userId;
            $order->id_courier = $courier->id_courier;
            if(!empty($bank)) $order->bank_account = $bank;
        // TODO validacja pol    

            if(!$order->save()) throw new Exception('Zamówienie nie zostało dodane do bazy. Spróbuj złożyć zamówienie ponownie.');
            if(count($additionalsArr)>0) {
                foreach($additionalsArr as $orderAdd) {
                    $orderAdditionals = \Model::factory('OrderAdditional')->create();
                    $orderAdditionals->id_add = $orderAdd['id_add'];
                    $orderAdditionals->id_order = $order->id();
                    $orderAdditionals->price = $orderAdd['price'];
                    $orderAdditionals->save();
                }
            }
            $delivery->id_order = $order->id();
            $delivery->save();
            
            $parcel->id_delivery = $delivery->id();
            $parcel->save();
            
            

        } else {
            throw new Exception('Nie ma takiego kuriera');
        }
    } catch(Exception $e) {
        echo json_encode(array('error'=>$e->getCode(),'message'=>$e->getMessage()));
        exit();
    }
    // openpayu service configuration
    // some preprocessing
    $directory = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $myUrl = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] .$directory;

    $orderName = 'Przesyłka kurierska';
    $orderNumber = $order->id();
    $session = md5('nadajto'.$orderNumber);
    $order->session = $session;
    $order->save(); 
    
    
    if($user->prepays()->filter('sum')>=$amount && $user->onetime==0) {
        $prepay = \Model::factory('Prepay')->create();
        $prepay->id_customer = $userId;
        $prepay->date = date('Y-m-d H:i:s');
        $prepay->id_order = $orderNumber;
        $prepay->amount -= $amount;
        $prepay->save();
        
        SendMail($delivery->from_email, array('EMAIL'=>$delivery->from_email), 8);
        SendMail('marcin.jastrzebski@poludniowo.pl', array('ID'=>$prepay->id_order), 9);
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
                'Gross' => $amount*100, 'Net' => 0, 'Tax' => 0, 'TaxRate' => '0', 'CurrencyCode' => 'PLN'
            )
        )
    );

    // shoppingCart structure
    $shoppingCart = array(
        'GrandTotal' => $amount*100,
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
        'NotifyUrl' => $myUrl . '/payment/payu/notify', // url where payu service will send notification with order processing status changes
        'OrderCancelUrl' => $myUrl . '/payment/payu/cancel',
        'OrderCompleteUrl' => $myUrl . '/payment/payu/succes/'.$orderNumber,
        'OrderId' => $orderNumber,
        'Order' => $order
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
        echo json_encode(array('token'=>$result->getAccessToken(),'sessionid'=>$session));



    } else {
        echo json_encode(array('error'=>$result->getError(),'message'=>$result->getMessage()));
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
