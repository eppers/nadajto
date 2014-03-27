<?php

namespace lib;

class Tools {
    
    
  public function rate($post) {
      file_put_contents('debug-api.txt',$post);
    $couriers = \Model::factory('Courier')->find_many();
    $result = array();
    $type = 0;
    $weight = 1;
    $dim['length'] = 1;
    $dim['width'] = 1;
    $dim['height'] = 1;

    $formArray = explode('&', $post['form']);
    file_put_contents('debug-api.txt',var_export($formArray,true));
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
                    
                    $result[$courier->id_courier]['price_net']= number_format($parcel->getPrice()-$parcel->getPrice()*$_SESSION['user']['discount']/100, 2, '.', '');
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
                            $result[$courier->id_courier]['price_net'] = number_format($result[$courier->id_courier]['price_net'] + ($price2-$price2*$_SESSION['user']['discount']/100), 2, '.', '');
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
                                    $result[$courier->id_courier]['price_net'] = number_format($result[$courier->id_courier]['price_net'] + ($price2-$price2*$_SESSION['user']['discount']/100), 2, '.', '');
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
                        $result[$courierId]['price_net'] = number_format($result[$courierId]['price_net'] + ($price-$price*$_SESSION['user']['discount']/100), 2, '.', '');
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
                        $result[$courier->id_courier]['price_net'] = number_format($result[$courier->id_courier]['price_net'] + ($price-$price*$_SESSION['user']['discount']/100), 2, '.', '');
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
}
?>
