<?php


namespace lib;

class CourierManager {
    const UPS = 1;
    private $mode;
    private $nameSpace;
    
    function __construct($id) {
        $this->mode = $id;
    }
    
    /*
     * Funkcja zwraca obj kuriera na podstawie przesłanej stałej do konstruktora
     * W celu dodania nowego kuriera należy dopisać nowy const dla klasy oraz dodać
     * pozycję do switcha.
     * 
     * return obj
     */
    function getCourier() {
        switch($this->mode){
            default : return new \UPS\Tools();
        }
    }
    
    public function getParcel($length, $width, $height, $weight, $type) {
        switch($this->mode){
            case 1 : return new \UPS\Parcel($length, $width, $height, $weight, $type); break;
            default : return false;
        }
    }
    
}

?>
