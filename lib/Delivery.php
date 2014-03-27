<?php

namespace lib;

abstract class Delivery {
    
    protected $ptracknum;
    protected $status='';

    abstract protected function setStatus($ptracknum);
    abstract function getStatus();
    
    public static function printStatus($status) {
        switch($status) {
            case 'ER': $result = 'błąd'; break;
            case 'D': $result = 'doręczona'; break;
            case 'I': $result = 'w drodze'; break;
            case 'P': $result = 'oczekuje'; break;
            case 'M': $result = 'oczekuje'; break;
            case 'MV': $result = 'anulowane'; break;
            case 'EL': $result = 'inne'; break;
            case 'W': $result = 'przetwarza'; break;
            default: $result = 'nieznane';
        }
        
        return $result;
    }
    
}
?>
