<?php

class CourierParcelDimension extends Model{
        
    public static $_table = 'courier_parcel_dimension';
    public static $_id_column = 'id_cour_par_dim';
 
    public function notStandDim($orm,$length, $width, $height) {
        return $orm->where_raw('(`min_length`<? OR min_width<? OR min_height<?)', array($length, $width, $height));
    }
}
?>
