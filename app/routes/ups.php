<?php

/*
 * UPS ///////////////////////////////////////////////////////
 */

$app->post('/api/ups/ship', function () use ($app) {
    
    //TODO dane pojda z bazy
    //validacja nr tel
    //validacja adresu
    //nazwy
    //miasta
    $error = array();
    
    $ups = new UPS\Tools;

    //create soap request
    
    $ups->ordernum = '1';
    $ups->EPLlabel = 'PNG';

    if(!$nad_email = filter_var($app->request()->post('nad_email'),FILTER_VALIDATE_EMAIL)){$error['input'][] = 'nad_email'; $error['msg'][] = 'Niepoprawny email';};
    $nad_email2 = $app->request()->post('nad_email2');
    $nad_company = $app->request()->post('nad_company');
    $nad_company = clearName($nad_company);
    if(!$odb_email = filter_var($app->request()->post('odb_email'),FILTER_VALIDATE_EMAIL)){$error['input'][] = 'odb_email'; $error['msg'][] = 'Niepoprawny email';};
    $odb_email2 = $app->request()->post('odb_email2');
    $odb_company = $app->request()->post('odb_company');
    $odb_company = clearName($odb_company);    

    if(strcmp($nad_email,$nad_email2)!=0){$error['input'][] = 'nad_email'; $error['msg'][] = 'Niepoprawny email';}
    else $ups->from_address->email=$nad_email;
    if(!$from_name = clearName($app->request()->post('nad_imie'))) {$error['input'][] = 'nad_imie'; $error['msg'][] = 'Niepoprawne imię';};
    if(!$from_lname = clearName($app->request()->post('nad_nazwisko'))) {$error['input'][] = 'nad_nazwisko'; $error['msg'][] = 'Niepoprawne nazwisko';};
    if(!$from_addr = clearName($app->request()->post('nad_addr'))) {$error['input'][] = 'nad_ulica'; $error['msg'][] = 'Niepoprawna ulica';};
    if(!$from_addr_house = clearName($app->request()->post('nad_nrdomu'))) {$error['input'][] = 'nad_nrdomu'; $error['msg'][] = 'Niepoprawny numer';};
    
    if(strcmp($odb_email,$odb_email2)!=0){$error['input'][] = 'odb_email'; $error['msg'][] = 'Niepoprawny email';};
    if(!$to_name = clearName($app->request()->post('odb_imie'))) {$error['input'][] = 'odb_imie'; $error['msg'][] = 'Niepoprawne imię';};
    if(!$to_lname = clearName($app->request()->post('odb_nazwisko'))) {$error['input'][] = 'odb_nazwisko'; $error['msg'][] = 'Niepoprawne imię';};
    if(!$to_addr = clearName($app->request()->post('odb_addr'))) {$error['input'][] = 'odb_ulica'; $error['msg'][] = 'Niepoprawna ulica';};
    if(!$to_addr_house = clearName($app->request()->post('odb_nrdomu'))) {$error['input'][] = 'odb_nrdomu'; $error['msg'][] = 'Niepoprawny numer';};
    
    if(!empty($nad_company)) {
        $ups->from_address->company = $nad_company;
    } else {
        $ups->from_address->company = $from_name.' '.$from_lname;
    }
    $ups->from_address->name = $from_name.' '.$from_lname;
    $ups->from_address->addr = $from_addr.' '.$from_addr_house;
    if(!$ups->from_address->city = onlyLetter($app->request()->post('nad_miasto'))) {$error['input'][] = 'nad_miasto'; $error['msg'][] = 'Niepoprawne miasto';};
    if(!$ups->from_address->zip = onlyNumber($app->request()->post('nad_zip'))) {$error['input'][] = 'nad_zip'; $error['msg'][] = 'Niepoprawny kod';};
    $ups->from_address->country = 'PL';
    if(!$ups->from_address->phone = clearPhone($app->request()->post('nad_telef'))) {$error['input'][] = 'nad_telef'; $error['msg'][] = 'Niepoprawny numer telefonu';};

    
    if(!empty($odb_company)) {
        $ups->to_address->company = $odb_company;
    } else {
        $ups->to_address->company = $to_name.' '.$to_lname;
    }
    $ups->to_address->name = $to_name.' '.$to_lname;
    $ups->to_address->addr = $to_addr.' '.$to_addr_house;
    if(!$ups->to_address->city = onlyLetter($app->request()->post('odb_miasto'))) {$error['input'][] = 'odb_miasto'; $error['msg'][] = 'Niepoprawne miasto';};//if(!$ups->to_address->city = clearName($app->request()->post('odb_miasto'))) {$error['input'][] = 'odb_miasto'; $error['msg'][] = 'Niepoprawne miasto';};
    if(!$ups->to_address->zip = onlyNumber($app->request()->post('odb_zip'))) {$error['input'][] = 'odb_zip'; $error['msg'][] = 'Niepoprawny kod';};
    $ups->to_address->country = 'PL';
    if(!$ups->to_address->phone = clearPhone($app->request()->post('odb_telef'))) {$error['input'][] = 'odb_telef'; $error['msg'][] = 'Niepoprawny numer telefonu';};
      
    $ups->pkgType = '01';
    if(!$ups->pkgweight = onlyNumber($app->request()->post('waga'))) {$error['input'][] = 'waga'; $error['msg'][] = 'Niepoprawna waga paczki';};
    if(!$ups->pkgdimensions->length = onlyNumber($app->request()->post('dlu'))) {$error['input'][] = 'dlu'; $error['msg'][] = 'Niepoprawna długość paczki';};
    if(!$ups->pkgdimensions->width = onlyNumber($app->request()->post('szer'))) {$error['input'][] = 'szer'; $error['msg'][] = 'Niepoprawna szerokość paczki';};
    if(!$ups->pkgdimensions->height = onlyNumber($app->request()->post('wys'))) {$error['input'][] = 'wys'; $error['msg'][] = 'Niepoprawna wysokość paczki';};

    print 'data nad '.$app->request()->post('data_nad');
    $date = str_replace("-","",$app->request()->post('data_nad'));
    print 'data '.$date;  
    $ups->pickup->date = $date; //'20100104'
    
    if(count($error['input'])<=0) {
       $label = $ups->ship('st');
       if($label !== false) {
           if($ups->pickup()) {
               $res = SendMail($nad_email, array('NAME'=>$imi, 'EMAIL'=>$ema, 'PHONE'=>$tel), 7, false, array('etykieta'=>$GLOBALS['REALPATH'].'/public_html/labels/'.$label));
               print json_encode($res);
           }
       }
    } else  
        print json_encode($error);
});
?>
