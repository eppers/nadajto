<?php

namespace UPS;
// interface with UPS
class Tools extends \lib\Tools{
  //Configuration
    private $environment = 'onlinetools';//'wwwcie'; //onlinetools

    private $to_address;
  	private $shipment_details;
  	private $carrier,$residential,$signatureRequired,$saturdayDelivery;
    private $pickup;
  	
	  private $from_address;
	  private $ordernum;
    private $cod;
    private $insurance;
    private $notstand;
    private $rod;
        
	  public $trackingnumber,$shipcost;
	  public $EPLlabel='EPL';	//0 if PNG,1 if EPL;
	  public $pkgweight, $pkgdimensions, $pkgType = 02, $pkgService = 'st';
  
	  private $access = UPSaccess;
  	private $userid = UPSuserid;
  	private $passwd = UPSpasswd;
  	private $accountnum = UPSaccountnum;
    private $outputFileName = "XOLTResult.xml";
    private $outputPickupFileName = "XOLTResultPickup.xml";

    private $shippingmethod;
    private $allowNameLength = 22;
    private $courierId = 1;

    private $pickupRetryNumber = 3;
    
    function ship_from_db($id) {
        
        $delivery = \Model::factory('Delivery')->where('id_order',$id)->find_one();
        if($delivery instanceof \Delivery) {
            
            $this->ordernum = $delivery->id_order;
            $this->EPLlabel = 'PNG';
            
            $this->from_address->email = $delivery->from_email;
            $this->from_address->company = clear_spec_char($delivery->from_company);
            //
            $this->from_address->name = clear_spec_char($delivery->from_lname);
            $this->from_address->addr = clear_spec_char($delivery->from_street.' '.$delivery->from_no);
            $this->from_address->addr .= clear_spec_char((!empty($delivery->from_no2))? '/'.$delivery->from_no : '');
            $this->from_address->city = clear_spec_char($delivery->from_city);
            $this->from_address->zip = $delivery->from_zip;
            $this->from_address->country = $delivery->from_country;
            $this->from_address->phone = $delivery->from_phone;
            
            $this->to_address->email = $delivery->to_email;
            $this->to_address->company = clear_spec_char($delivery->to_company);
            $this->to_address->name = clear_spec_char($delivery->to_lname);
            $this->to_address->addr = clear_spec_char($delivery->to_street.' '.$delivery->to_no);
            $this->to_address->addr .= clear_spec_char((!empty($delivery->to_no2))? '/'.$delivery->to_no : '');
            $this->to_address->city = clear_spec_char($delivery->to_city);
            $this->to_address->zip = $delivery->to_zip;
            $this->to_address->country = $delivery->to_country;
            $this->to_address->phone = $delivery->to_phone;
            
            $parcel = \Model::factory('Parcel')->where('id_delivery', $delivery->id_del)->find_one();
            
            if($parcel instanceof \Parcel) {
                $parcelType = \Model::factory('CourierParcel')->where('id_courier', $this->courierId)->where('type',$parcel->type)->find_one();
                $this->pkgType = $parcelType->courier_type;
                $this->pkgService = $parcelType->service;
                $this->pkgweight = $parcel->weight;
                $this->pkgdimensions->length = $parcel->length;
                $this->pkgdimensions->width = $parcel->width;
                $this->pkgdimensions->height = $parcel->height;
                
                
                $addCOD = \Model::factory('OrderAdditional')->where('id_order',$id)->filter('getCOD')->find_one();
                if(count($addCOD)>0) {
                    $this->cod = $addCOD->price;
                } 
                unset($addCOD);
                
                $addInsurance = \Model::factory('OrderAdditional')->where('id_order',$id)->filter('getInsurance')->find_one();
                if(count($addInsurance)>0) {
                    $this->insurance = $addInsurance->price;
                }
                
                $addNotstand = \Model::factory('OrderAdditional')->where('id_order',$id)->filter('getNotstand')->find_array();
                if(count($addNotstand)>0) {
                    $this->notstand = true;
                }
                
                $addROD = \Model::factory('OrderAdditional')->where('id_order',$id)->filter('getROD')->find_array();
                if(count($addROD)>0) {
                    $this->rod = true;
                }
                
                
    file_put_contents("debug.txt", "from".  serialize($this->from_address)." \n\n",FILE_APPEND);
    file_put_contents("debug.txt", "to".  serialize($this->to_address)." \n\n",FILE_APPEND);
    
               $label = $this->ship($this->pkgService);
               if($label !== false) {
                   $order = \Model::factory('Order')->find_one($id);
                   $order->tracking = str_replace('.gif', '', $label);
                   $order->save();

                   if($this->pickup($delivery->date)) {
                       $res = SendMail($this->from_address->email, array('PICKUP'=>$this->pickup->date,'TRACKLINK'=>'http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1='.$order->tracking.'&track.x=0&track.y=0%22','TRACKING'=>$order->tracking,'NAME'=>$this->from_address->name, 'EMAIL'=>$this->from_address->email, 'PHONE'=>$this->from_address->phone), 7, false, array('etykieta'=>$GLOBALS['REALPATH'].'/public_html/labels/'.$label));
                       if($res===TRUE) return TRUE;
                       else file_put_contents("debug.txt", "\n\n emailSend".  serialize($res)." \n\n",FILE_APPEND);
                   }
               }
    
                
            }
        }
        
    }
    
     

    function set_tracking_num($response){
            $this->trackingnumber=$response->ShipmentResults->ShipmentIdentificationNumber;
            \UPS\Label::set_tracking($this->trackingnumber);
    }
    
    function set_name($name, $lname) {
            if((strlen($name)+strlen($lname))>$this->allowNameLength) {
                $name = strtoupper(substr($name, 0, 1)).'.';
                if(strlen($lname)>$this->allowNameLength-3) {
                    $lname = strtoupper(substr($lname, 0, $this->allowNameLength-3));
                }
                
            }
            return $name.' '.$lname;
    }

    function set_pkg_cost($response){
            $this->cost=$response->ShipmentResults->ShipmentCharges->TotalCharges->MonetaryValue;
    }
        
    //called by processShipment to set from address
    function set_shipper(){
    //$shipper['TaxIdentificationNumber'] = '123456';

            $shipper['Name'] = $this->from_address->company;
            if(!empty($this->from_address->name))            
                $shipto['AttentionName'] = $this->from_address->name;
            $shipper['ShipperNumber'] = $this->accountnum;
            $address['AddressLine'] = $this->from_address->addr;
            $address['City'] = $this->from_address->city;
            $address['PostalCode'] = $this->from_address->zip;
            $address['CountryCode'] = $this->from_address->country;
            $shipper['Address'] = $address;
            $phone['Number'] = $this->from_address->phone;
        //$phone['Extension'] = '1';
            $shipper['Phone'] = $phone;	
            return $shipper;
    }
    //called by processShipment to set from address
    function set_ship_to(){
            $shipto['Name'] = $this->to_address->company;
            if(!empty($this->to_address->name))
                $shipto['AttentionName'] = $this->to_address->name;
            $addressTo['AddressLine'] = array($this->to_address->addr,$this->to_address->addr2);
            $addressTo['City'] = $this->to_address->city;
            $addressTo['PostalCode'] = $this->to_address->zip;
            $addressTo['CountryCode'] = $this->to_address->country;

            $phone2['Number'] = $this->to_address->phone;
            $shipto['Address'] = $addressTo;
            $shipto['Phone'] = $phone2;
            return $shipto;
    }
    //called by processShipment to set from address
    function set_ship_from(){
		$addressFrom['AddressLine'] = $this->from_address->addr;
		$addressFrom['City'] = $this->from_address->city;
		$addressFrom['PostalCode'] = $this->from_address->zip;
		$addressFrom['CountryCode'] = $this->from_address->country;
		$phone3['Number'] = $this->from_address->phone;
                if(!empty($this->from_address->name))
                    $shipfrom['AttentionName'] = $this->from_address->name;                
                $shipfrom['Name'] = $this->from_address->company;
		$shipfrom['Address'] = $addressFrom;
		$shipfrom['Phone'] = $phone3;
                
		return $shipfrom;
    }
    
    function process_shipment($p_method){
            //create soap request
            $requestoption['RequestOption'] = 'nonvalidate';
            $request['Request'] = $requestoption;

            $shipment['Shipper'] = $this->set_shipper();
            $shipment['ShipTo'] =  $this->set_ship_to();
            $shipment['ShipFrom'] = $this->set_ship_from();

            $shipmentcharge['Type'] = '01'; // zmiana powoduje blad ?! jak dac pobranie ? // to samo w pickup
            $billshipper['AccountNumber'] = $this->accountnum;
            $shipmentcharge['BillShipper'] = $billshipper;
            $paymentinformation['ShipmentCharge'] = $shipmentcharge;
            $shipment['PaymentInformation'] = $paymentinformation;

            $service['Code'] = $this->get_service_code($p_method);
            $shipment['Service'] = $service;

    //    $package['Description'] = '';
            $packaging['Code'] = '02';
            $package['Packaging'] = $packaging;
            $unit['Code'] = 'CM';
            $unit['Description'] = 'Centimeters,';
            $dimensions['UnitOfMeasurement'] = $unit;
            $dimensions['Length'] = $this->pkgdimensions->length;
            $dimensions['Width'] = $this->pkgdimensions->width;
            $dimensions['Height'] = $this->pkgdimensions->height;
            $package['Dimensions'] = $dimensions;
    
            $unit2['Code'] = 'KGS';
            $unit2['Description'] = 'Kilograms';
            $packageweight['UnitOfMeasurement'] = $unit2;
            $packageweight['Weight'] = $this->pkgweight;
            $package['PackageWeight'] = $packageweight;



//		$referencenumber['Value']=$this->ordernum;
//		$referencenumber['Code']="IK";

//		$package['ReferenceNumber']=$referencenumber;
//		if($this->signatureRequired){
//			$deliveryconfirmation['DCISType']=3; //adult signature required
//			$packageserviceoptions['DeliveryConfirmation']=$deliveryconfirmation;
//		}
            
            
                        
            //NIESTANDARDOWE
            if($this->notstand) {
                $package['AdditionalHandlingIndicator'] = '0';
            }


            
            //UBEZPIECZENIE 
            if(!empty($this->insurance)) {
                //$insuredValue['DeclaredValue'] = $this->insurance;
                //$packageserviceoptions['InsuredValue'] = $insuredValue;
                //$insuredValue['DeclaredValue'] = $this->insurance;
                $declaredValue['MonetaryValue'] = $this->insurance;
                $declaredValue['CurrencyCode'] = 'PLN';
                $packageserviceoptions['DeclaredValue'] = $declaredValue;
                $package['PackageServiceOptions'] = $packageserviceoptions;

            }
          
            $shipment['Package'] = $package;
            $label=($this->EPLlabel=='PNG')? \UPS\Label::set_png() : \UPS\Label::set_epl();	
            $request['LabelSpecification'] = $label;
            $shipmentserviceoptions=null;
            if($this->saturdayDelivery){
                $shipmentserviceoptions['SaturdayDeliveryIndicator']=1;
            }
            
            //ZWROT DOKUMENTOW
            if($this->rod) {
                $shipmentserviceoptions['ReturnOfDocumentIndicator'] = '0';
                
            }
            
            //POBRANIE
            if(!empty($this->cod)) {
                $codAmount['MonetaryValue'] = $this->cod;
                $codAmount['CurrencyCode'] = 'PLN';
                $cod['CODFundsCode'] = 1;
                $cod['CODAmount'] = $codAmount;
                $shipmentserviceoptions['COD'] = $cod;
            }

            
            $shipmentratingoptions['NegotiatedRatesIndicator']=1;
            $shipment['ShipmentRatingOptions']=$shipmentratingoptions;
            if (!is_null($shipmentserviceoptions)) $shipment['ShipmentServiceOptions']=$shipmentserviceoptions;	
            $request['Shipment'] = $shipment;
  //    print_r($request);
            return $request;
  	}
        
        function set_pickup_address(){
            $pickupaddress['CompanyName'] = $this->from_address->company;
            $pickupaddress['ContactName'] = $this->from_address->name;
            $pickupaddress['AddressLine'] = $this->from_address->addr;
            $pickupaddress['City'] = $this->from_address->city;
            $pickupaddress['PostalCode'] = $this->from_address->zip;
            $pickupaddress['CountryCode'] = $this->from_address->country;
            $pickupaddress['ResidentialIndicator'] = 'Y';
            $phone['Number'] = $this->from_address->phone;
            $pickupaddress['Phone'] = $phone;
            
            return $pickupaddress;
        }
        
        function set_pickup_date(){
            $pickupdateinfo['CloseTime'] = '1800';
            $pickupdateinfo['ReadyTime'] ='0900';
            $pickupdateinfo['PickupDate'] = $this->pickup->date;  //'20100104';
            
            return $pickupdateinfo;
        }
        
        function process_pickup(){
                   //create soap request
            $requestoption['RequestOption'] = '1';
            $request['Request'] = $requestoption;
            $request['RatePickupIndicator'] = 'N';
            $account['AccountNumber']= $this->accountnum;
            $account['AccountCountryCode'] = 'PL';
            $shipper['Account'] = $account;
            $request['Shipper'] = $shipper;
            
            $request['PickupDateInfo'] = $this->set_pickup_date();
          
            $request['PickupAddress'] = $this->set_pickup_address();
            $request['AlternateAddressIndicator'] = 'Y';
            
            $pickuppiece['ServiceCode'] = '0'.$this->get_service_code($this->pkgService); //service type - 11 UPS Standard in PL
            $pickuppiece['Quantity'] = '1';
            $pickuppiece['DestinationCountryCode'] = $this->to_address->country;
            $pickuppiece['ContainerCode'] = $this->pkgType; //01 = PACKAGE 02 = UPS LETTER 03 = PALLET
            $request['PickupPiece'] = $pickuppiece;
            
            $totalweight['Weight'] = $this->pkgweight;
            $totalweight['UnitOfMeasurement'] = 'KGS';
            $request['TotalWeight'] = $totalweight;
            $request['OverweightIndicator'] =  'N';
            $request['PaymentMethod'] = '01'; //shipperaccount 01 - prepaid 02 - collect on delivery ??
            $request['SpecialInstruction'] =  '';
            $request['ReferenceNumber'] = '';
            $cnfrmemailaddr =  array
            (
                $this->from_address->email
            );
            $notification['ConfirmationEmailAddress'] = $cnfrmemailaddr;
            $notification['UndeliverableEmailAddress'] = '';
            $request['Notification'] = $notification;
            $csr['ProfileId'] = $this->accountnum;
            $csr['ProfileCountryCode'] = 'PL';
            $request['CSR'] = $csr;

            return $request;
        }
        
  function get_service_code($service){
  	switch($service){
  		case 'st':
  			return '11';
  			break;
  		case 'ex':
  			return '07';
  			break;
  		case 'tex':
  			return '85';
  			break;
  		case 'texs':
  			return '86';
  			break;
  		case 'ts':
  			return '82';
  			break;
  		case 'exp':
  			return '54';
  			break;
                case 'saver':
                        return '65';
                        break;
  	}
  }
  

    public function ship($pMethod,$emailAddress='eppers.m@gmail.com'){

            if($pMethod=='g' && $this->residential) $pMethod='gr';//if the residential flag is set to true and we are shipping ground then modify the shipping method to home ground.
            $this->shippingmethod=$this->get_service_code($pMethod);
            $wsdl = $GLOBALS['REALPATH']."/lib/UPS/wsdl/Ship.wsdl";
            $operation = "ProcessShipment";
            //$endpointurl = 'https://wwwcie.ups.com/webservices/Ship'; //wersja test
            $endpointurl = 'https://'.$this->environment.'.ups.com/webservices/Ship'; //wersja prod
            

          try {
            $client=$this->generate_soap($wsdl,$endpointurl);
            //var_dump($client);
            //$arrayShip = $this->process_shipment($pMethod);
            //file_put_contents("debug.txt", "Ship".  serialize($this->process_shipment($pMethod))." \n\n",FILE_APPEND);
            //$array = array($this->process_shipment($pMethod));
            //var_dump($array);
            if(strcmp($operation,"ProcessShipment") == 0 )
                    $resp = $client->__soapCall('ProcessShipment', array($this->process_shipment($pMethod)));
            else if (strcmp($operation , "ProcessShipConfirm") == 0)
                    $resp = $client->__soapCall('ProcessShipConfirm',array($this->process_shipConfirm()));
            else
                    $resp = $client->__soapCall('ProcessShipAccept',array($this->process_shipAccept()));
//print "\n resp...\n\n";
//print_r($resp);


            file_put_contents("debug.txt", "Resp".  serialize($resp)." \n\n",FILE_APPEND);
		$this->set_tracking_num($resp);
		$this->set_pkg_cost($resp);
		$labelName=\UPS\Label::save($resp);

          $fw = fopen($this->outputFileName , 'w');
          fwrite($fw , "Request: \n" . $client->__getLastRequest() . "\n");
          fwrite($fw , "Response: \n" . $client->__getLastResponse() . "\n");
          fclose($fw);
	  }
    catch (\SoapFault $e) {
              print "<br>Błąd wysyłki: <br>";  
              $error = serialize ($e->detail);
              $error = explode("{", $error);
              $error = $error[4];
              $error = explode(";", $error);
              $error = $error[3];
              $error = strstr($error, '"');
             
              print_r($error);
    }
	  catch(\Exception $ex)
	  {
           print $ex->getMessage()."\n";
           print $ex->getLine();
              //var_dump($ex->getTrace());
		//	file_put_contents($this->outputFileName, serialize($lulok));
                        return false;
	  }
	  return $labelName;
    }
    
    public function pickup($date) {
       
            $wsdl = $GLOBALS['REALPATH']."/lib/UPS/wsdl/Pickup.wsdl";
            $operation = "ProcessPickupCreation";
            $endpointurl = 'https://'.$this->environment.'.ups.com/webservices/Pickup';
            
            $i = 0;//retry number start
            
            
            while(true) {
                try {
                    $this->pickup->date = str_replace("-","",$date);
                    
                    $client=$this->generate_soap($wsdl,$endpointurl);
                    $resp = $client->__soapCall($operation,array($this->process_pickup()));
                    $fw = fopen($this->outputPickupFileName , 'w');
                    fwrite($fw , "Request: \n" . $client->__getLastRequest() . "\n");
                    fwrite($fw , "Response: \n" . $client->__getLastResponse() . "\n");
                    fclose($fw);
                    
                    $delivery = \Model::factory('Delivery')->where('id_order',$this->ordernum)->find_one();
                    $delivery->date = $date;
                    $delivery->save();
                    return true;
                }
                catch (\SoapFault $e) {
                  
                    if($i++==$this->pickupRetryNumber) {
                      print "<br>Błąd pickup: <br>";  
                      $error = serialize ($e->detail);
                     // $error = explode("{", $error);
                      //$error = $error[4];
                      //$error = explode(";", $error);
                      //$error = $error[3];
                      //$error = strstr($error, '"');
                      print_r($error);
                    } else {
                     $date = date('Y-m-d', strtotime($date.' +1 Weekday'));
                    }
                }
                catch(Exception $ex){
                    print_r ($ex);
                    return false;
                  
                }
            }
    }  

    public function process_void($tracknum){
            //create soap request
            $tref['CustomerContext'] = 'Add description here';
            $req['TransactionReference'] = $tref;
            $request['Request'] = $req;
            $voidshipment['ShipmentIdentificationNumber'] = strtoupper($tracknum);
            $request['VoidShipment'] = $voidshipment;
            return $request;
    }

    public function delete_shipment($ptracknum){
		$wsdl = $GLOBALS['REALPATH']."/lib/UPS/wsdl/Void.wsdl";
	  	$operation = "ProcessVoid";
  		$endpointurl = 'https://'.$this->environment.'.ups.com/webservices/Void';
		try
		{
			$client=$this->generate_soap($wsdl,$endpointurl);
			$resp = $client->__soapCall($operation ,array($this->process_void($ptracknum)));
			return "Success";
		}
                catch(\SoapFault $soap){
                  // var_dump($soap->detail);
                  //var_dump($soap->faultcode, $soap->faultstring, $soap->faultactor, $soap->detail, $soap->_name, $soap->headerfault); 
                 return false;
                }                
		catch(\Exception $ex)
		{
			//print_r ($ex);
			return false;
		}
      }  

      public function processTrack($ptracknum) {
          //create soap request
        $req['RequestOption'] = '0';
        //$tref['CustomerContext'] = 'Add description here';
        //$req['TransactionReference'] = $tref;
        $request['Request'] = $req;
        $request['InquiryNumber'] = $ptracknum;
              $request['TrackingOption'] = '01';

             // echo "Request.......\n";
             //print_r($request);
            //echo "\n\n";
              $fw = fopen($this->outputFileName,'w');
                        fwrite($fw , "Request: \n" . $request . "\n");
                        fclose($fw);
          return $request;
      }
  
      public function track($ptracknum){
  
  		$wsdl = $GLOBALS['REALPATH']."/lib/UPS/wsdl/Track.wsdl";
	  	$operation = "ProcessTrack";
  		$endpointurl = 'https://'.$this->environment.'.ups.com/webservices/Track';
		try
		{
			$client=$this->generate_soap($wsdl,$endpointurl);
			$resp = $client->__soapCall($operation ,array($this->processTrack($ptracknum)));

                        return $resp;
		}
                catch(\SoapFault $ex){
                      $error = serialize ($ex->detail);
                      //var_dump($error);
                      //var_dump($ex->faultcode, $ex->faultstring, $ex->faultactor, $ex->detail, $ex->_name, $ex->headerfault); 
                      if(strpos($error,'151044')!==false) return -1;
                      else return false;
                          
                  // var_dump($soap->detail);
                  

                }  
		catch(\Exception $ex)
		{
			print_r ($ex);
//                        $response = var_export($ex->faultcode.' '.$ex->faultstring.' '.$ex->faultactor.' '.$ex->detail.' '.$ex->_name.' '.$ex->headerfault,true); 
//                        file_put_contents('XOLTResult2.xml',$response);
			return false;
		}
  }  

	//helper function to format Address Correction request.
	function process_XAV($address){
      //create soap request
      $option['RequestOption'] = '3';
      $request['Request'] = $option;
      $addrkeyfrmt['ConsigneeName'] = $address->company;
      $addrkeyfrmt['AddressLine'] = array
      (
         $address->addr,
 	     $address->addr2,
      );
 	  $addrkeyfrmt['PoliticalDivision2'] = $address->city;
 	  $addrkeyfrmt['PoliticalDivision1'] = $address->state;
 	  $addrkeyfrmt['PostcodePrimaryLow'] = $address->zipcode;
 	  $addrkeyfrmt['CountryCode'] = 'US';
 	  $request['AddressKeyFormat'] = $addrkeyfrmt;
      return $request;
  	}
  	public function address_changed($orignial_addr,$orignial_addr2,$orignial_city,$orignial_state,$orignial_zipcode){
		$changed=false;
  		if(strtolower($this->toaddr)!=strtolower($orignial_addr)) $changed=true;
  		if(strtolower($this->toaddr2)!=strtolower($orignial_addr2)) $changed=true;
  		if(strtolower($this->tocity)!=strtolower($orignial_city)) $changed=true;
  		if(strtolower($this->tostate)!=strtolower($orignial_state)) $changed=true;
  		if(strtolower($this->tozip)!=strtolower($orignial_zipcode)) $changed=true;
  		if(strtolower($this->tocountry)!=strtolower($orignial_addr)) $changed=true;  		
  		return $changed;
  	}
	public function address_checker($address){
		$wsdl = "c:/inetpub/wwwroot/cgi-bin/IncludeCode/UPS/XAV.wsdl";
	  	$operation = "ProcessXAV";
	  	$endpointurl = "https://".$this->environment.".ups.com/webservices/XAV";
		try
		{
			$client=$this->generate_soap($wsdl,$endpointurl);
			$resp = $client->__soapCall($operation ,array($this->processXAV($address)));
			$valid_address=1;
			$multiple_address=0;
			$valid_address=1;
			$multiple_address=1;
			$candidate=count($resp->Candidate)>1 ? $resp->Candidate[0] : $resp->Candidate;
			$address=$candidate->AddressKeyFormat;
			if (is_array($address->AddressLine)){
				$this->toaddr=$address->AddressLine[0];
				$this->toaddr2=$address->AddressLine[1];
			}
			else{
				$this->toaddr=$address->AddressLine;
			}

			$this->tocity=$address->PoliticalDivision2;
			$this->tostate=$address->PoliticalDivision1;
			$this->tozip=$address->PostcodePrimaryLow;
			$this->tocountry=$address->CountryCode;
			$this->residential=$candidate->AddressClassification->Code==2 ? 1 : 0; //if code==2 then this is residential
			//if a single address is returned we still need to check if this address has been changed.
			if(!$multiple_address)
				$multiple_address=$this->address_changed($address->addr,$address->addr2,$address->city,$address->state,$address->zipcode);

			$returnarr=array('confirmed'=>$valid_address,'removed'=>$multiple_address);
		}
		catch(Exception $ex)
		{
			print_r ($ex);
		}
		return($returnarr);
	}

	//check that there is a valid return
	//find ground service and return number of transit days
	public function transit_time_helper($response){
		if (!property_exists($response,'TransitResponse'))
			return 99;
		$services=$response->TransitResponse->ServiceSummary;
		foreach($services as $service){
			if($service->Service->Code=='GND')
				return $service->EstimatedArrival->BusinessDaysInTransit;
		}
		return 99;
	}

	//shipDate must be in the format YYYY-MM-DD HH:MM:SS using a 24 hour clock
	//date('c') returns a format that is compatible.
	//returns days for ground to arrive at destination
	public function transit_time($shipDate){
	  $wsdl = "c:/inetpub/wwwroot/cgi-bin/IncludeCode/UPS/TNTWS.wsdl";
	  $operation = "ProcessTimeInTransit";
	  $endpointurl = "https://".$this->environment.".ups.com/webservices/";
	  try
		{
			$client=$this->generate_soap($wsdl,$endpointurl);
			$requestoption['RequestOption'] = 'TNT';
			$request['Request'] = $requestoption;
			$addressFrom['CountryCode'] = 'US';
			$addressFrom['PostalCode'] = $this->from_address->zip;
			$shipFrom['Address'] = $addressFrom;
			$request['ShipFrom'] = $shipFrom;
			$addressTo['CountryCode'] = 'US';
			$addressTo['PostalCode'] = $this->tozip;
			$shipTo['Address'] = $addressTo;
			$request['ShipTo'] = $shipTo;
			$pickup['Date'] = date('Ymd',strtotime($shipDate));
			$request['Pickup'] = $pickup;
			$unitOfMeasurement['Code'] = 'LBS';
			$shipmentWeight['UnitOfMeasurement'] = $unitOfMeasurement;
			$shipmentWeight['Weight'] = '10';
			$request['ShipmentWeight'] = $shipmentWeight;
			$request['TotalPackagesInShipment'] = '1';
			$request['MaximumListSize'] = '1';
			$resp = $client->__soapCall($operation ,array($request));
			return $this->transit_time_helper($resp);
  		}
  	catch(Exception $ex)
		{
			return 99;
		}
	return 99;
	}

	//this sets up the soap client. All api calls must use this before they execute their specific api call.
	function generate_soap($wsdl,$endpointurl){
		$mode = array
			(
					 'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
					 'trace' => true,
           'exceptions' => true
			);	
		$client = new \SoapClient($wsdl , $mode);
		$client->__setLocation($endpointurl);
		$usernameToken['Username'] = $this->userid;
		$usernameToken['Password'] = $this->passwd;
		$serviceAccessLicense['AccessLicenseNumber'] = $this->access;
		$upss['UsernameToken'] = $usernameToken;
		$upss['ServiceAccessToken'] = $serviceAccessLicense;
		$header = new \SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0','UPSSecurity',$upss);
		$client->__setSoapHeaders($header);
		return $client;
	}
        
        public function quantumView() {
             $access = $this->access;
             $userid = $this->userid;
             $passwd = $this->passwd;
             $accessSchemaFile = $GLOBALS['REALPATH']."/lib/UPS/wsdl/AccessRequest.xsd";
             $requestSchemaFile = $GLOBALS['REALPATH']."/lib/UPS/wsdl/QuantumViewRequest.xsd";
             $responseSchemaFile = $GLOBALS['REALPATH']."/lib/UPS/wsdl/QuantumViewResponse.xsd";
             $endpointurl = 'https://wwwcie.ups.com/ups.app/xml/QVEvents';
             $outputFileName = "XOLTResult.xml";
            try {
                        //create AccessRequest data object
                    $das = \SDO_DAS_XML::create($accessSchemaFile);
                    $doc = $das->createDocument();
                    $root = $doc->getRootDataObject();
                    $root->AccessLicenseNumber=$access;
                    $root->UserId=$userid;
                    $root->Password=$passwd;
                    $accessrequest = $das->saveString($doc);

                    //create QuantumViewRequest data oject
                    $das = \SDO_DAS_XML::create($requestSchemaFile);
                    $requestaction = $das->createDataObject('','Request');
                    $requestaction->RequestAction='QVEvents';

                    $doc = $das->createDocument();
                    $root = $doc->getRootDataObject();
                    $root->Request = $requestaction;

                    $qvrequest = $das->saveString($doc);

                    //create Post request
                    $form = array
                    (
                       'http' => array
                       (
                          'method' => 'POST',
                          'header' => 'Content-type: application/x-www-form-urlencoded',
                          'content' => "$accessrequest$qvrequest"
                       )
                    );

                    //print form request
                    print_r($form);


                    $request = stream_context_create($form);
                    $browser = fopen($endpointurl , 'rb' , false , $request);
                    if(!$browser)
                    {
                       throw new Exception("Connection failed.");
                    }

                    //get response
                    $response = stream_get_contents($browser);
                    fclose($browser);

                    if($response == false)
                    {
                       throw new Exception("Bad data.");
                    }
                    else
                    {
                       //save request and response to file
                        $fw = fopen($outputFileName,'w');
                        fwrite($fw , "Response: \n" . $response . "\n");
                        fclose($fw);

                       //get response status
                       $resp = new \SimpleXMLElement($response);
                       echo $resp->Response->ResponseStatusDescription . "\n";

                    }
                }
                catch(SDO_Exception $sdo)
                {
                   echo ($sdo);
                }
                catch(Exception $ex)
                {
                   echo ($ex);
                }
        }
}
?>