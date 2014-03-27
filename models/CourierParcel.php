<?php

class CourierParcel extends Model{
        
    public static $_table = 'courier_parcels';
    public static $_id_column = 'id_cour_parcel';
      
    function dimensions() {
        return $this->has_many('CourierParcelDimension', 'id_cour_parcel');
    }
    
    function weights() {
        return $this->has_many('CourierParcelWeight', 'id_cour_parcel');
    }
}
?>
