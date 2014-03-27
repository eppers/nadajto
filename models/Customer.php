<?php

class Customer extends Model{
        
    public static $_table = 'customers';
    public static $_id_column = 'id_customer';
    
    public function orders() {
        return $this->has_many('Order', 'id_customer'); // Note we use the model name literally - not a pluralised version
    }
        
    public function prepays() {
        return $this->has_many('Prepay', 'id_customer'); 
    }    
    
    public function between($orm,$ini,$end) {
        return $orm->where_raw('(`date` BETWEEN "?" AND "?")', array($ini, $end));
    }
    
}
?>
