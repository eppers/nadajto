<?php

class Order extends Model{
        
    public static $_table = 'orders';
    public static $_id_column = 'id_order';
    
    public function additionals() {
        return $this->has_many('OrderAdditional', 'id_order'); // Note we use the model name literally - not a pluralised version
    }
    
    public function invoice() {
        return $this->belongs_to('Invoice','id_invoice');
    }
    
    public function between($orm,$ini,$end) {
        return $orm->where_raw('(`date` BETWEEN "?" AND "?")', array($ini, $end));
    }
    
    public function delivery() {
        return $this->has_one('Delivery','id_order');
    }
}
?>
