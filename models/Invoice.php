<?php

class Invoice extends Model{
        
    public static $_table = 'invoices';
    public static $_id_column = 'id_invoice';
    
    public function orders() {
        return $this->has_many('Order', 'id_invoice'); // Note we use the model name literally - not a pluralised version
    }  
    
    public static function getCustomerInvoices($orm, $idCustomer) {
        return $orm->raw_query('SELECT i.* FROM invoices i JOIN orders o ON i.id_invoice=o.id_invoice WHERE o.id_customer=:idCustomer  GROUP BY o.id_invoice ORDER BY i.date', array('idCustomer'=>$idCustomer));
    }
}
?>
