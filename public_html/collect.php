<?php

require('ShippingTools_UPS.php');
require('LabelClass.php');


$ups = new ShippingTools_UPS;

    //create soap request
    
    $ups->ordernum = '1';
    $ups->EPLlabel = 'PNG';

    $ups->from_address->company = 'ShipperName';
    $ups->from_address->addr = 'Astrów';
    $ups->from_address->city = 'Goleszów';
    $ups->from_address->state = 'MD';
    $ups->from_address->zip = '43440';
    $ups->from_address->country = 'PL';
    $ups->from_address->phone = '1115554758';

    $ups->to_address->name = 'Happy Dog Pet Supply';
    $ups->to_address->company = '1160b_74';
    $ups->to_address->addr = 'Hallera';
    $ups->to_address->city = 'Cieszyn';
    $ups->to_address->zip = '43400';
    $ups->to_address->country = 'PL';
    $ups->to_address->phone = '501975099';
  
    $ups->pkgweight = '12';
    $ups->pkgdimensions->length = '7';
    $ups->pkgdimensions->width = '5';
    $ups->pkgdimensions->height = '2';

  
    $ups->ship('st');
?>
