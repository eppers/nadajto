<?php

namespace UPS;
/*
 * Class checking prices for parcels
 * and correction of parcel's dimensions
 */
class Parcel {
	private $dimensions = array();
        private $weight;
        private $price;
        private $type;
        private $price_net = 0;
        private $notstand = 0;
        private $notstandWeight = 32;
                
        function __construct($length, $width, $height, $weight, $type) {
            $this->dimensions['length'] = $length;
            $this->dimensions['width'] = $width;
            $this->dimensions['height'] = $height;
            $this->type = $type;
            $this->weight = $weight;
            
            $this->setParcel();
        }
        
        private function setParcel() {
            
            $parcel = \Model::factory('CourierParcel')->where('type',$this->type)->where('id_courier',1)->find_one();

            //counting dimension weight
            $sumWeight = $this->dimensions['length']*$this->dimensions['width']*$this->dimensions['height']/5000;
            $sum = max($sumWeight,$this->weight);
            $sum = ceil($sum);
            if($parcel instanceof \CourierParcel) {
           
                $standPriceNet = $parcel->weights()->where_gte('max_weight',$sum)->where_lte('min_weight',$sum)->find_one();
                if($standPriceNet instanceof \CourierParcelWeight) {
                    //$standPriceNet = \Model::factory('CourierParcelWeight')->where_gte('min_weight',$sum)->where_lte('max_weight',$sum)->find_one();
                    //counting dimensions and setting price
                    $dimTmp = $this->dimensions;
                    rsort($dimTmp);
//                    var_dump($dimTmp);
                    $sumDim = $dimTmp[0]+2*$dimTmp[1]+2*$dimTmp[2];
                    $parcelType = $parcel->dimensions()->where_raw(' min_dim<=? AND max_dim>=?', array($sumDim,$sumDim))->find_one();     

                    //$parcelType = \Model::factory('CourierParcelDimension')->where_gte('min_dim', $sumDim)->where_lte('max_dim',$sumDim)->find_one();
                    if($parcelType instanceof \CourierParcelDimension) {
                        $this->price_net = number_format($standPriceNet->price_netto+$parcelType->price_netto,2, '.', '');
                        if($parcelType->notstand == 0) {
                            $notStand = $parcel->dimensions()->where('notstand',1)->where_raw('(`min_length`<=? OR min_width<=? OR min_height<=?)', array($this->dimensions['length'],$this->dimensions['width'],$this->dimensions['height']))->find_array();
                            array_walk_recursive($notStand, function ($value, $key) use (& $result) {
                              $result[$key] = $value;
                            });
                            if($result['notstand']==1) { $this->price_net = number_format($this->price_net+$result['price_netto'],2, '.', ''); $this->notstand = 1;}
                            elseif ($sum>=$this->notstandWeight) {$sumNotstand = $parcel->dimensions()->where('notstand',1)->find_one(); if($sumNotstand instanceof \CourierParcelDimension) {$this->price_net = number_format($this->price_net+$sumNotstand->price_netto,2, '.', ''); $this->notstand = 1;}}
                        } else $this->notstand = 1;
                    } else throw new \Exception('Wystąpił błąd w rozmiarach przesyłek');
                } else throw new \Exception('Paczka przekroczyła dozwoloną wagę');
                //wage zamienic na int (moze ktos dal przecinek)
            } else throw new \Exception ('Niezgodny typ paczki z danymi kuriera');
        }
        
        public function getPrice(){
            return $this->price_net;
        }
        
        public function getNotstand(){
            return $this->notstand;
        }
}
?>