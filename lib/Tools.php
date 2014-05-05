<?php

namespace lib;

class Tools {
    
  private $order;
  private $customer;
  private $delivery;
    
    
  public function rate($post,$discount=0) {
      file_put_contents('debug-api.txt',$post);
    $couriers = \Model::factory('Courier')->find_many();
    $result = array();
    $type = 0;
    $weight = 1;
    $dim['length'] = 1;
    $dim['width'] = 1;
    $dim['height'] = 1;

    //TODO uzyc parse_str
    $formArray = explode('&', $post['form']);
    file_put_contents('debug-api.txt',var_export($formArray,true));
    
    //discount dla API
    $login=explode("=", $formArray[0]);
    if($login[0]==='login') {
        $customer = \Model::factory('Customer')->where('email',$login[1])->find_one();
        if($customer instanceof \Customer)
            $discount = $customer->discount;
    }

    //Parcel data
    if($key = array_find('pkg_weight',$formArray)) {
        $val = explode('=',$formArray[$key]);
        if(!empty($val[1]))
            $weight = $val[1];
    }
    
    foreach($dim as $key=>&$row) {
        if($keyTmp = array_find('pkg_'.$key,$formArray)) {
            $val = explode('=',$formArray[$keyTmp]);
            if(!empty($val[1]))
                $row = (int)$val[1];
        }
        
    }
   
    foreach($formArray as &$row) {
        $row = preg_replace("/[^a-zA-Z0-9\=_\-]/", "", $row);
        $arrayTemp = explode('=', $row);
        if($arrayTemp[0]=='rodzaj') {
            $type = $arrayTemp[1];
            foreach($couriers as $courier) {
                try {
                $cour = new \lib\CourierManager($courier->id_courier);
                $parcel = $cour->getParcel($dim['length'], $dim['width'], $dim['height'], $weight, $type);
                } catch(\Exception $e) {
                    return array('error'=>$e->getMessage());
                    exit();
                }
                if($parcel) {
                    
                    $result[$courier->id_courier]['price_net']= number_format($parcel->getPrice()-$parcel->getPrice()*$discount/100, 2, '.', '');
                    $result[$courier->id_courier]['price_brut'] = number_format($result[$courier->id_courier]['price_net']+$result[$courier->id_courier]['price_net']*$GLOBALS['CONFIG']['vat']/100, 2, '.', '');
                    $result[$courier->id_courier]['notstand'] = $parcel->getNotstand();
                } else {
                    continue;
                }
            }
           
           
        } elseif($arrayTemp[0]=='Notstand') {
            foreach($couriers as $courier) {
                if($result[$courier->id_courier]['notstand']==0) {
                    try {
                        $additional = new \lib\CourierAdditional($arrayTemp[0], $courier->name);
                           
                        $price2 = $additional->getPrice();

                        if(is_numeric($price2)) {
                            $result[$courier->id_courier]['price_net'] = number_format($result[$courier->id_courier]['price_net'] + ($price2-$price2*$discount/100), 2, '.', '');
                            $result[$courier->id_courier]['price_brut'] = number_format($result[$courier->id_courier]['price_net']+$result[$courier->id_courier]['price_net']*$GLOBALS['CONFIG']['vat']/100, 2, '.', '');
                        }
                    } catch(\Exception $e) {
                        continue;
                    }
                }
            }
        } else {

     
        $arrayAdd=explode('_', $arrayTemp[0]);
        if (count($arrayAdd)>1) { //if a name contains string pkg or courier or string check (additional_check require additional_input)
            if($arrayAdd[0]=='pkg') {
                continue;
                
            } elseif($arrayAdd[1]=='check') {
                if($key = array_find($arrayAdd[0].'_input',$formArray)) {
                    $val = explode('=',$formArray[$key]);
                    if(!empty($val[1])) {
                        foreach($couriers as $courier) {
                            try {
                                $additional = new \lib\CourierAdditional($arrayAdd[0], $courier->name);
                                $price2 = $additional->getPrice($val[1]);
                                
                                if(is_numeric($price2)) {
                                    $result[$courier->id_courier]['price_net'] = number_format($result[$courier->id_courier]['price_net'] + ($price2-$price2*$discount/100), 2, '.', '');
                                    $result[$courier->id_courier]['price_brut'] = number_format($result[$courier->id_courier]['price_net']+$result[$courier->id_courier]['price_net']*$GLOBALS['CONFIG']['vat']/100, 2, '.', '');
                                }
                            } catch(\Exception $e) {
                                continue;
                            }
                        }
                    }
                } 
            } else {
                //update price for one courier : ups_Insurance=360   
                //TODO if Insurance - pick the higher one
                $val = $arrayTemp[1];
                try {
                    $additional = new \lib\CourierAdditional($val[0], $arrayAdd[0]);
                    $price = $additional->getPrice($val[1]);
                    if(is_numeric($price)) {
                        $courierId = $additional->getCourier();
                        $result[$courierId]['price_net'] = number_format($result[$courierId]['price_net'] + ($price-$price*$discount/100), 2, '.', '');
                        $result[$courierId]['price_brut'] = number_format($result[$courierId]['price_net']+$result[$courierId]['price_net']*$GLOBALS['CONFIG']['vat']/100, 2, '.', '');
                        
                    }
                } catch(\Exception $e) {
                    
                }
            }
        } else {
            //update price for one courier : ups_Insurance=360   
            //TODO if Insurance - pick the higher one
            $val = $arrayTemp[1];
            foreach($couriers as $courier) {
                try {
                    $additional = new \lib\CourierAdditional($arrayTemp[0], $courier->name);
                    $price = $additional->getPrice($val[1]);
                    if(is_numeric($price)) {
                        $courierId = $additional->getCourier();
                        $result[$courier->id_courier]['price_net'] = number_format($result[$courier->id_courier]['price_net'] + ($price-$price*$discount/100), 2, '.', '');
                        $result[$courier->id_courier]['price_brut'] = number_format($result[$courier->id_courier]['price_net']+$result[$courier->id_courier]['price_net']*$GLOBALS['CONFIG']['vat']/100, 2, '.', '');

                    }

                } catch(\Exception $e) {
                    ;
                }
            }
        } 
        
      }
    }

      return $result;
    }
    
    /*
     * Funkcja z danych przeslanych tworzy parcel, order oraz delivery w bazie przygotowując
     * do wykorzystania metody shipFromDb
     * 
     * @param array $data, int $userId, bool $dataFromApi
     * @return mixed
     */
    public function prepareDataToShip(array $data, $userId = NULL, $dataFromApi = false) {

        $discount = 0;
        if(isset($userId)) {
            $user = \Model::factory('Customer')->find_one($userId);

            if(!$user instanceof \Customer) {
                throw new Exception('Błąd z ID usera. Skontaktuj się z administracją.');
            } else {
                $discount = $user->discount;
            }
        };
        
        
        
        $delivery = \Model::factory('Delivery')->create();
        $parcel = \Model::factory('Parcel')->create();

        $typeTmp = 0;
        $weight = 1;
        $dim['length'] = 1;
        $dim['width'] = 1;
        $dim['height'] = 1;

        if(!$parcel->weight = onlyNumber($data['weight'])) {$error['input'][] = 'pkg_weight'; $error['msg'][] = 'Niepoprawna waga paczki';};
        if(!$parcel->length = onlyNumber($data['length'])) {$error['input'][] = 'pkg_length'; $error['msg'][] = 'Niepoprawna długość paczki';};
        if(!$parcel->width = onlyNumber($data['width'])) {$error['input'][] = 'pkg_width'; $error['msg'][] = 'Niepoprawna szerokość paczki';};
        if(!$parcel->height = onlyNumber($data['height'])) {$error['input'][] = 'pkg_height'; $error['msg'][] = 'Niepoprawna wysokość paczki';};

        if($data['pkg_type']>3) {$error['input'][] = 'pkg_type'; $error['msg'][] = 'Zły typ wysyłki';};
        if(!$parcel->type = onlyNumber($data['pkg_type'])) {$error['input'][] = 'pkg_type'; $error['msg'][] = 'Zły typ wysyłki';};

        if(!$nad_email = filter_var($data['nad_email'],FILTER_VALIDATE_EMAIL)){$error['input'][] = 'nad_email'; $error['msg'][] = 'Niepoprawny email';};
        $nad_email2 = $data['nad_email2'];
        $nad_company = $data['nad_company'];
        $nad_company = clearName($nad_company);

        $odb_company = $data['odb_company'];
        $odb_company = clearName($odb_company);    
        $nad_nip = preg_replace('/[^\s0-9\-]/u', "", trim($data['nad_nip']));
        $odb_nip = preg_replace('/[^\s0-9\-]/u', "", trim($data['odb_nip']));
        $dataBank = $data['bank'] === 'false'? false: $data['bank'];
        if($dataBank!==false) {
            if(!$bank = onlyNumber($data['bank'])) {$error['input'][] = 'account-no'; $error['msg'][] = 'Niepoprawny numer konta';};
        }

        if(strcmp($nad_email,$nad_email2)!=0){$error['input'][] = 'nad_email'; $error['msg'][] = 'Niepoprawny email';}
        else $delivery->from_email=$nad_email;
        if(!$from_name = clearName($data['nad_imie'])) {$error['input'][] = 'nad_imie'; $error['msg'][] = 'Niepoprawne imię';};
        if(!$from_lname = clearName($data['nad_nazwisko'])) {$from_lname='';};
        if(!$from_addr = clearName($data['nad_addr'])) {$error['input'][] = 'nad_ulica'; $error['msg'][] = 'Niepoprawna ulica';};
        if((!$from_addr_house = clearName($data['nad_nrdomu'])) && $dataFromApi==false) {$error['input'][] = 'nad_nrdomu'; $error['msg'][] = 'Niepoprawny numer domu';};

        if(!$to_name = clearName($data['odb_imie'])) {$error['input'][] = 'odb_imie'; $error['msg'][] = 'Niepoprawne imię';};
        if(!$to_lname = clearName($data['odb_nazwisko'])) {$to_lname='';};
        if(!$to_addr = clearName($data['odb_addr'])) {$error['input'][] = 'odb_ulica'; $error['msg'][] = 'Niepoprawna ulica';};
        if((!$to_addr_house = clearName($data['odb_nrdomu'])) && $dataFromApi==false) {$error['input'][] = 'odb_nrdomu'; $error['msg'][] = 'Niepoprawny numer domu';};

        $courier = \Model::factory('Courier')->find_one($data['courierid']);
        $result = array();

        if($courier instanceof \Courier) {
            $formArray = explode('&', $data['form']);

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
                            $result['price_net'] = number_format($parcelTmp->getPrice()-$parcelTmp->getPrice()*$discount/100, 2, '.', '');
                            $result['price_brut'] = number_format($result['price_net']+$result['price_net']*$GLOBALS['CONFIG']['vat']/100, 2, '.', '');
                            $result['notstand'] = $parcelTmp->getNotstand();

                        } else {$error['input'][] = 'rodzaj'; $error['msg'][] = 'Ten kurier nie obsługuje tego typu wysyłki';};              
                } elseif($arrayTemp[0]=='Notstand') {
                    if($result['notstand']==0) {
                         try {
                            $additional = new \lib\CourierAdditional($arrayTemp[0], $courier->name);
                            $price2 = $additional->getPrice();

                            if(is_numeric($price2)) {
                                $result['price_net'] = number_format($result['price_net'] + $price2-$price2*$discount/100, 2, '.', '');
                                $result['price_brut'] = number_format($result['price_net']+$result['price_net']*$GLOBALS['CONFIG']['vat']/100, 2, '.', '');
                                $result['notstand'] = 1;

                                $tempArr['id_add'] = $additional->getIdAdditional();
                                $tempArr['price'] = $price2;

                                $additionalsArr[]=$tempArr;
                            }
                        } catch(\Exception $e) {
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
                                            $result['price_net'] = number_format($result['price_net'] + $price2-$price2*$discount/100, 2, '.', '');
                                            $result['price_brut'] = number_format($result['price_net']+$result['price_net']*$GLOBALS['CONFIG']['vat']/100, 2, '.', '');

                                            $tempArr['id_add'] = $additional->getIdAdditional();
                                            $tempArr['price'] = $val[1];

                                            $additionalsArr[]=$tempArr;
                                        }
                                    } catch(\Exception $e) {
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
                                } catch(\Exception $e) {
                                    continue;
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
                                    $result['price_net'] = number_format($result['price_net'] + ($price-$price*$discount/100), 2, '.', '');
                                    $result['price_brut'] = number_format($result['price_net']+$result['price_net']*$GLOBALS['CONFIG']['vat']/100, 2, '.', '');
                                    $tempArr['id_add'] = $additional->getIdAdditional();
                                    $tempArr['price'] = (!empty($val))? $val:0;

                                    $additionalsArr[]=$tempArr;
                                }

                            } catch(\Exception $e) {
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

        if(!$delivery->from_city = onlyLetter($data['nad_miasto'])) {$error['input'][] = 'nad_miasto'; $error['msg'][] = 'Niepoprawne miasto';};
        if(!$delivery->from_zip = onlyNumber($data['nad_zip'])) {$error['input'][] = 'nad_zip'; $error['msg'][] = 'Niepoprawny kod';};
        $delivery->from_country = 'PL';
        if(!$delivery->from_phone = clearPhone($data['nad_telef'])) {$error['input'][] = 'nad_telef'; $error['msg'][] = 'Niepoprawny numer telefonu';};

        if($delivery->from_zip) {

            $zip = setProperZipType(clearZip($data['nad_zip']));
            $cityFrom = onlyLetter($data['nad_miasto']);
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

        if($data['odb_priv']==1) $delivery->to_company = $to_name.' '.$to_lname;
        else $delivery->to_company = $to_name;

        $delivery->to_name = $to_name;
        $delivery->to_lname = $to_lname;
        $delivery->to_street = $to_addr;
        $delivery->to_no = $to_addr_house;
        if(!$delivery->to_city = onlyLetter($data['odb_miasto'])) {$error['input'][] = 'odb_miasto'; $error['msg'][] = 'Niepoprawne miasto';};//if(!$ups->to_address->city = clearName($data['odb_miasto'])) {$error['input'][] = 'odb_miasto'; $error['msg'][] = 'Niepoprawne miasto';};
        if(!$delivery->to_zip = onlyNumber($data['odb_zip'])) {$error['input'][] = 'odb_zip'; $error['msg'][] = 'Niepoprawny kod';};
        $delivery->to_country = 'PL';
        if(!$delivery->to_phone = clearPhone($data['odb_telef'])) {$error['input'][] = 'odb_telef'; $error['msg'][] = 'Niepoprawny numer telefonu';};

        if($delivery->to_zip) {
            $zip = clearZip($data['odb_zip']);
            $cityTo = onlyLetter($data['odb_miasto']);
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

        $date = $data['data_nad'];
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
                    $delivery->date = date('Y-m-d', strtotime($date." +1 Weekday"));
                else
                    $delivery->date = date('Y-m-d', strtotime($date));
            }
            
        }
        
        
        //TODO w zaleznosci od rodzaju przesylki wyswietla typ



        if(count($error['input'])>0) {
            return $error;
        }
    // TODO dane z bazy jezeli niejednorazowy    
        $order = \Model::factory('Order')->create();
        $order->delivery_type = $typeTmp;    
        try {

            if($courier instanceof \Courier) {

                if(!isset($user)) {
                    //TODO jezeli nie ma takiego klienta to dodac go do bazy
                    $user = \Model::factory('Customer')->create();
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
                
                $this->customer = $user;
                //server podaje zla strefe czasowa
                date_default_timezone_set('Europe/Warsaw');
                $order->date = date('Y-m-d H:i:s');


                $amount = number_format($result['price_brut'], 2, '.', '');

                if($COD) $order->payment = 2;
                $order->price = $amount;
                $order->price_netto = number_format($result['price_net'], 2, '.', '');
                $order->id_customer = $userId;
                $order->id_courier = $courier->id_courier;
                if(!empty($bank)) $order->bank_account = $bank;

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
                $this->delivery = $delivery;
                
                $parcel->id_delivery = $delivery->id();
                $parcel->save();



            } else {
                throw new \Exception('Nie ma takiego kuriera');
            }
        } catch(\Exception $e) {
            return array('error'=>$e->getCode(),'message'=>$e->getMessage());
        }

        $orderNumber = $order->id();
        $session = md5('nadajto'.$orderNumber);
        $order->session = $session;
        $order->save(); 
        
        $this->order = $order;
        
        return true;
    }
    
    public function __get($key) {
      if(isset($this->$key)) {
        return $this->$key;
      }

      throw new \Exception(sprintf('%s::%s cannot be accessed.', __CLASS__, $key));
  }
}
?>
