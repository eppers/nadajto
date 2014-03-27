<?php

class Courier extends Model{
        
    public static $_table = 'couriers';
    public static $_id_column = 'id_courier';
      
    public function additionals() {
        return $this->has_many('Additional', 'id_courier'); // Note we use the model name literally - not a pluralised version
    }
    
     public function prices() {
        return $this->has_many('Price', 'id_courier'); // Note we use the model name literally - not a pluralised version
    }
    
     public function parcels() {
        return $this->has_many('CourierParcel', 'id_courier'); // Note we use the model name literally - not a pluralised version
    }
    
}
?>
