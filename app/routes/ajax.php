<?php

session_start();
$app->post('/form/price/update', function () use ($app) {
    //underline determinates if price will be update for one courier or more (courierName_typeOfAdditionalOption)
    //wyszukanie ceny danej uslugi i pomniejszenie sumy o wartosc tej uslugi
    
    $courier = new \lib\Tools;
    $result = $courier->rate($app->request()->post(), $_SESSION['user']['discount']);   

    //TODO przygotowac wersje updatujaca wiecej kurierow
     echo json_encode($result);
}); 

$app->post('/api/ship/void',  function () use ($app) {
      $orderId = $app->request()->post('id');

      try {
          if($_SESSION['admin']!=1) {

            $customer = Model::factory('Customer')->find_one($_SESSION['user']['id_customer']);
            $order = $customer->orders()->find_one(intval($orderId));
            if(!$order instanceof Order) { 
                throw new Exception('Nie masz uprawnienień do usuwania tego zamówienia'); 
            }
          } else {
            $order = Model::factory('Order')->find_one(intval($orderId)); 
            if(!$order instanceof Order) { 
                throw new Exception('Nie ma takiego zamówienia'); 
            }
          }

          $delivery = $order->delivery()->find_one();
          if(!$delivery instanceof Delivery || $delivery->status!='M') {
              throw new Exception('To zamówienie nie może zostać anulowane.'); 
          } else {
              $ups = new UPS\Tools();
              if(!$ups->delete_shipment($order->tracking)) {
                throw new Exception('To zamówienie nie może zostać anulowane2.'); 
              }
          }
      } catch(Exception $e) {
          $resp['error'] = $e->getMessage();
          print json_encode($resp);
          exit();
      }
      print json_encode('ok');
         
});

$app->get('/echocookie',  function () use ($app) {
      $lulok = $app->getEncryptedCookie('courier_prices'); // <<< returns NULL
      var_dump(json_decode($lulok)); 
      $aara=json_decode($lulok);
      print $aara->ups->price;
         
    });
$app->get('/echocookie2',  function () use ($app) {
      $lulok = $app->getEncryptedCookie('test_cookies2'); // <<< returns NULL
      print $lulok;

    });

?>
