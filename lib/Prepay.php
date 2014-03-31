<?php
/**
 * Description of Prepay
 *
 * @author Peter
 */
namespace lib;

class Prepay {
    
    private $id;
    
    public function getId() {
        return $this->id;
    }

    public function addPrepayForOrder(\Order $order) {
        $prepay = \Model::factory('Prepay')->create();
        $prepay->id_customer = $order->id_customer;
        $prepay->date = date('Y-m-d H:i:s');
        $prepay->id_order = $order->id_order;
        $prepay->amount -= $order->price;
        $prepay->save();
        
        $this->id = $prepay->id();
    }
}

?>
