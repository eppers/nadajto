<?php


namespace lib;

class CourierAdditional {
    
    private $add; //obj Additional
    private $COD = false;
    private $ROD = false;
    
    function __construct($additionalType,$courierName) {
        $courier = \Model::factory('Courier')->where('name',$courierName)->find_one();
        if(!$courier instanceof \Courier) throw new \Exception('Kurier o nazwie '.$courierName.' nie został znaleziony');
        $additional = $courier->additionals()->where('type',$additionalType)->find_one();
        if($additional instanceof \Additional) {
            $this->add = $additional;
        }
        else throw new \Exception('Dodatkowa opcja o nazwie '.$additionalType.' nie została znaleziona');
    }
    
    function getPrice($val=0) {

        if($this->add->type=='COD') {
            //every thousand +4pln
            if(!empty($val)) {
                $total = floor($val/1000);
                $modulo = $val%1000;
                $price = $this->add->price*$total;
                if($modulo>0) $price += $this->add->price;
                $this->COD = true;
            } else {
                throw new \Exception('Wartość pobrania nie została zdefiniowana');
            }
        } else {
            $insurance = $this->add->insurances()->where_lte('amount_from',$val)->where_gte('amount_to',$val)->find_one();
            if($insurance instanceof \Insurance) {
                $price = $insurance->price+($val*$insurance->extra_costs/100);
            } else $price = $this->add->price;
        }
        
        return number_format($price, 2, '.', '');
    }
    
    function getCourier() {
        return $this->add->id_courier;
    }
    
    function getIdAdditional() {
        return $this->add->id_add;
    }
    
    function getCOD() {
        return $this->COD;
    }
    
    function getROD() {
        return $this->ROD;
    }    
    
    
}

?>
