<?php

class OrderAdditional extends Model{
        
    public static $_table = 'order_additionals';
    public static $_id_column = 'id_ord_add';
      
    public function getCOD($orm) {
        return $orm->select_many('order_additionals.price','additionals.type')->join('additionals', array('order_additionals.id_add', '=', 'additionals.id_add'))->where('additionals.type','COD');
    } 
    
    public function getInsurance($orm) {
        return $orm->select_many('order_additionals.price','additionals.type')->join('additionals', array('order_additionals.id_add', '=', 'additionals.id_add'))->where('additionals.type','Insurance')->order_by_desc('order_additionals.price');
    }
    
    public function getNotstand($orm) {
        return $orm->select_many('order_additionals.price','additionals.type')->join('additionals', array('order_additionals.id_add', '=', 'additionals.id_add'))->where('additionals.type','Notstand');
    } 
    
    public function getROD($orm) {
        return $orm->select_many('order_additionals.price','additionals.type')->join('additionals', array('order_additionals.id_add', '=', 'additionals.id_add'))->where('additionals.type','ROD');
    } 
}
?>
