<?php

class Additional extends Model{
        
    public static $_table = 'additionals';
    public static $_id_column = 'id_add';
     
    function insurances(){
        return $this->has_many('Insurance', 'id_add'); // Note we use the model name literally - not a pluralised version
    }
}
?>
