<?php

namespace UPS;
/*
 * Class checking prices for parcels
 * and correction of parcel's dimensions
 */
class Delivery extends \lib\Delivery{
        
        function __construct($ptracknum) {
            $tool = new Tools;
            $resp = $tool->track($ptracknum);
            if($resp==-1 || $resp===false) $this->setStatus($resp);
            else $this->setStatus($resp->Shipment->Package->Activity->Status);
        }
        
        protected function setStatus($respStat) {
            if($respStat==-1) $this->status = 'W';
            elseif($respStat===false) $this->status = 'ER';
            else {
                switch($respStat->Type) {
                    case false: $this->status = 'ER'; break;
                    case 'D': $this->status = 'D'; break;
                    case 'P': $this->status = 'P'; break;
                    //case 'MB': $this->status = 'M'; break;
                    //case 'MP': $this->status = 'M'; break;
                    case 'M': $this->status = 'M'; break;
                    case 'MV': $this->status = 'MV'; break;
                    case 'I': $this->status = 'I'; break;
                    default: $this->status = 'EL';
                }

                if(strpos($respStat->Code,'V')!==false) {$this->status = 'MV';}
            }
            
        }
                
        function getStatus(){
            return $this->status;
        }

}
?>