<?php

class Prepay extends Model{
        
    public static $_table = 'prepay';
    public static $_id_column = 'id_prepay';
    
    public function sum($orm) {
        return $orm->where_raw('(`status` = "paid" OR `id_order` is not NULL)',array())->sum('amount');
    }
    
    public function customer() {
        return $this->belongs_to('Customer','id_customer');
    }
}
?>
