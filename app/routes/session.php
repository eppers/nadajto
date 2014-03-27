<?php
/*
 * TODO: 
 * 1. rozmiar paczki dla ups validacje zrobic - nie moze przekroczyc konkretnych wymiarow
 * 
 * 3. obsluge bledu w razie gdyby nie udalo sie zamowic kuriera / mail wysylac po udanym shipingu oraz pickupie
 * 4. tabele przesylek rozbic na rodzaje przesylek i kody w zaleznosci od kuriera ? http://www.ups.com/worldshiphelp/WS11/ENU/AppHelp/Codes/Package_Type_Codes.htm
 * 
 */

/*
 * Wyświetlenie strony głównej
 */
$app->get('/', function () use ($app, $couriersArr) {
    
    $session = array();
    if(isset($_SESSION['user']['id'])) $session = array('id'=>$_SESSION['user']['id']);

    foreach($couriersArr as $courier) {
        $couriersArr[strtolower($courier['name'])]['price_brutto'] = number_format($courier['price']+$courier['price']*$GLOBALS['CONFIG']['vat']/100, 2, '.', '');
    }

    $payULink = OpenPayu_Configuration::getSummaryUrl();
    $app->render('home.php',array('title'=>'title','session'=>$session,'payULink'=>$payULink,'couriers'=>$couriersArr,'page'=>'start'));
});

/*
 * Aktywacja
 */
$app->get('/aktywacja', function () use ($app) {

    $app->render('aktywacja.php',array('title'=>'title'));
});

/*
 * Cennik
 */
$app->get('/cennik', function () use ($app) {

    $app->render('cennik.php',array('title'=>'title'));
});


$app->post('/cennik', function () use ($app) {

    $config = Model::factory('Config')->find_array();


    if(count($config)<0) die('Błąd: Brak danych konfiguracyjnych');

    if($config) {
        $GLOBALS['CONFIG'] = array();
        foreach($config as $tmp) {
                $GLOBALS['CONFIG'][$tmp['variable']] = $tmp['value'];
        }
    }  else {
        die('Błąd: Brak danych konfiguracyjnych');
    }

    $imi = htmlspecialchars(strip_tags($app->request()->post('imienaz')));
    $ema = htmlspecialchars(strip_tags(str_replace("\n", '', $app->request()->post('email'))));
    $tel = htmlspecialchars(strip_tags($app->request()->post('telefon')));
    $odb = explode(',', $GLOBALS['CONFIG']['msg_dest']);
    $res = array();
    foreach($odb as $admmail)
    {
    $res[] = SendMail($admmail, array('NAME'=>$imi, 'EMAIL'=>$ema, 'PHONE'=>$tel), 6, $ema);
    }

    $retVal = 1;
    foreach($res as $result)
        if($result !== false)
        {
                $retVal = 0;
                break;
        }
        
    $app->render('cennik.php',array('RETMSG'=>$retVal));

});
        
        

/*
 * Dla kogo
 */
$app->get('/dla_kogo', function () use ($app) {

    $app->render('dlakogo.php',array('title'=>'title'));
});

/*
 * Godziny
 */
$app->get('/godziny_odbioru', function () use ($app) {

    $app->render('godziny.php',array('title'=>'title'));
});

/*
 * Jak przygotować
 */
$app->get('/jak_przygotowac', function () use ($app) {

    $app->render('jakprzygotowac.php',array('title'=>'title'));
});

/*
 * Elementy niestandardowe
 */
$app->get('/elementy_niestandardowe', function () use ($app) {

    $app->render('niestandardowe.php',array('title'=>'title'));
});

/*
 * Przedmioty niedozwolone w wysyłce
 */
$app->get('/przedmioty_niedozwolone', function () use ($app) {

    $app->render('niedozwolone.php',array('title'=>'title'));
});

/*
 * Reklamacje
 */
$app->get('/reklamacje', function () use ($app) {

    $app->render('reklamacje.php',array('title'=>'title'));
});

/*
 * Kontakt
 */
$app->get('/kontakt', function () use ($app) {

    $app->render('kontakt.php',array('title'=>'title'));
});

$app->post('/kontakt', function () use ($app) {

    $config = Model::factory('Config')->find_array();


    if(count($config)<0) die('Błąd: Brak danych konfiguracyjnych');

    if($config) {
        $GLOBALS['CONFIG'] = array();
        foreach($config as $tmp) {
                $GLOBALS['CONFIG'][$tmp['variable']] = $tmp['value'];
        }
    }  else {
        die('Błąd: Brak danych konfiguracyjnych');
    }

    $imi = htmlspecialchars(strip_tags($app->request()->post('imienaz')));
    $ema = htmlspecialchars(strip_tags(str_replace("\n", '', $app->request()->post('email'))));
    $tre = str_replace("\n", '<br>', htmlspecialchars(strip_tags($app->request()->post('tresc'))));
    $odb = explode(',', $GLOBALS['CONFIG']['msg_dest']);
    $res = array();
    foreach($odb as $admmail)
    {
    $res[] = SendMail($admmail, array('NAME'=>$imi, 'EMAIL'=>$ema, 'MESSAGE'=>$tre), 4, $ema);
    }

    $retVal = 1;
    foreach($res as $result)
        if($result !== false)
        {
                $retVal = 0;
                break;
        }
        
    $app->render('kontakt.php',array('RETMSG'=>$retVal));

});

/*
 * logowanie
 */
$app->get('/logowanie', function () use ($app) {

    $app->render('logowanie.php',array('title'=>'title'));
});


$app->post('/logowanie', function () use ($app) {

    $isAdmin = 0;
    if(!$email = filter_var($app->request()->post('login'),FILTER_VALIDATE_EMAIL)){$error['input'][] = 'login'; $error['msg'][] = 'Niepoprawny email';};
    $pass = $app->request()->post('pass');
    if(empty($pass)){$error['input'][] = 'passw'; $error['msg'][] = 'Wpisz hasło';};
    
    if(count($error['input'])<=0) {
        
      $user = Model::factory('Customer')->where('email',$email)->where('pass', md5($pass))->where('onetime',0)->find_array();
     
      if(count($user) > 0) {
           //var_dump($user);
          $_SESSION['user']= call_user_func_array('array_merge', $user);
          $addr = explode(' ', $_SESSION['user']['addr']);
          $_SESSION['user']['street']=$addr[0];
          $_SESSION['user']['no1']=$addr[1];
          $_SESSION['user']['no2']=$addr[2];
          $_SESSION['user']['zip1'] = substr($_SESSION['user']['zip'], 0, 2);
          $_SESSION['user']['zip2'] = substr($_SESSION['user']['zip'], 2);
          $_SESSION['user']['login'] = $_SESSION['user']['name'].' '.$_SESSION['user']['lname'];
      } else {
        
        $user = Model::factory('User')->where('email',$email)->where('pass', md5($pass))->find_one();
        if($user instanceof User) {
          $isAdmin = 1;
          $_SESSION['admin']=1;
          $_SESSION['user']['id_customer']=$user->id_user;
          $_SESSION['user']['login']=$user->login;
        } else 
        $error['input'][]='login'; $error['msg'][]='Zły login lub/i hasło';
    
      }
    }
    if(count($error['input'])<=0) 
        if( $isAdmin==1) print json_encode('admin'); 
        else print json_encode(true); 
    else  print json_encode($error);
    

});

/*
 * Pomoc
 */
$app->get('/pomoc', function () use ($app) {

    $app->render('pomoc.php',array('title'=>'title'));
});

/*
 * Przesyłki
 */
$app->get('/przesylki', function () use ($app) {

    $app->render('przesylki.php',array('title'=>'title'));
});

/*
 * Regulamin
 */
$app->get('/regulamin', function () use ($app) {

    $app->render('regulamin.php',array('title'=>'title'));
});

/*
 * Rejestracja
 */
$app->get('/rejestracja', function () use ($app) {

    $app->render('rejestracja.php',array('title'=>'title'));
});
//TODO email z linkiem aktywacyjnym
$app->post('/rejestracja', function () use ($app) {
    
    if(!$name = clearName($app->request()->post('imie'))) {$error['input'][] = 'imie'; $error['msg'][] = 'Niepoprawne imię';};
    if(!$lname = clearName($app->request()->post('nazwisko'))) {$error['input'][] = 'nazwisko'; $error['msg'][] = 'Niepoprawne nazwisko';};
    if(!$phone = clearPhone($app->request()->post('telefon'))) {$error['input'][] = 'telefon'; $error['msg'][] = 'Niepoprawny numer telefonu';};
    if(!$email = filter_var($app->request()->post('email'),FILTER_VALIDATE_EMAIL)){$error['input'][] = 'email'; $error['msg'][] = 'Niepoprawny email';};
    
    $pass = $app->request()->post('passw');
    if(empty($pass)) {$error['input'][] = 'passw'; $error['msg'][] = 'Niepoprawne hasło';}
    $pass2 = $app->request()->post('passw2');
    if(strcmp($pass,$pass2)!=0){$error['input'][] = 'passw'; $error['msg'][] = 'Niepoprawne hasło';};
    
    $customer = Model::factory('Customer')->where('email', $email)->where('onetime',0)->find_one();
    if($customer instanceof Customer){$error['input'][] = 'email'; $error['msg'][] = 'Użytkownik o takim emailu już istnieje';};
    
    if(count($error['input'])<=0) {
        // jezeli jest juz taki zarejestrowany w bazie z emailem to error dla emaila
      $user = Model::factory('Customer')->create();
      $user->name = $name;
      $user->lname = $lname;
      $user->email = $email;
      $user->phone = $phone;
      $user->pass = md5($pass);
      $user->onetime = 0;
      $user->save();
      SendMail($email, array('NAME'=>$name, 'LNAME'=>$lname), 2);
      $_SESSION['user']['name'] = $name;
      $_SESSION['user']['lname'] = $lname;
      $_SESSION['user']['email'] = $email;
      $_SESSION['user']['id_customer'] = $user->id();
      $_SESSION['user']['login'] = $_SESSION['user']['name'].' '.$_SESSION['user']['lname'];
      print json_encode(true);
    } else  
        print json_encode($error);
    
});



/*
 * Wyloguj
 */
$app->get('/wyloguj', function () use ($app) {
    
    session_destroy();
    
    $app->redirect('/');
});

$app->get('/mail/track', function () use ($app) {
     //SendMail('eppers.m@gmail.com', array('EMAIL'=>'eppers.m@gmail.com'), 8);
    //SendMail('eppers.m@gmail.com', array('PICKUP'=>'2013-11-17','TRACKLINK'=>'link trackingu','TRACKING'=>'track123','NAME'=>'wooki', 'EMAIL'=>'eppers.m@gmail.com', 'PHONE'=>'123'), 7, false, array('etykieta'=>$GLOBALS['REALPATH'].'/public_html/labels/'.$label));
});








?>
