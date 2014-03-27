<?php

namespace lib;

class InvoicePDF extends \FPDF {

	private $PG_W = 190;

    public function __construct($tabProd,$tabVat,$tabKontrahent,$tabOwner,$nazwa,$caloscNetto,$caloscBrutto,$caloscVat,$dataWystawienia,$formaPlatnosci,$terminPlatnosci,$print) {
        parent::__construct();		
    
    
	//print_r($tabProd);
    $tabKontrahent['firma']=Utf2Iso($tabKontrahent['firma']);
    $tabKontrahent['nazwiskoImie']=Utf2Iso($tabKontrahent['nazwiskoImie']);
    $tabKontrahent['adres']=Utf2Iso($tabKontrahent['adres']);
    $tabOwner['nazwisko']=Utf2Iso($tabOwner['nazwisko']);
    $tabOwner['adres']=Utf2Iso($tabOwner['adres']);
    $nazwa=Utf2Iso($nazwa);
    $formaPlatnosci=Utf2Iso($formaPlatnosci);
    $print=Utf2Iso($print);


    $this->AddFont('arialpl','','arialpl.php');
    $this->AddFont('arialpl','B','arialbdpl.php');
    $this->SetAutoPageBreak(true, 40);
    $this->Header($nazwa);
    $this->LineItems($tabProd,$tabVat,$tabKontrahent,$tabOwner,$nazwa,$caloscNetto,$caloscBrutto,$caloscVat,$dataWystawienia,$formaPlatnosci,$terminPlatnosci,$print);
	}
	
	public function Header() {

        }
		
		
		
	public function LineItems($tabProd, $tabVat, $tabKontrahent, $tabOwner, $nazwa, $caloscNetto, $caloscBrutto, $caloscVat, $dataWystawienia,$formaPlatnosci,$terminPlatnosci,$print) {

         

		

		// Data
/*				$textWrap = str_repeat("this is a word wrap test ", 3);
		$textNoWrap = "there will be no wrapping here thank you";
		
		$data = array();
				
		$data[] = array($textWrap, 1, 50, 50, 0, 50);
		$data[] = array($textNoWrap, 1, 10500, 10500, 0, 10500);
		$data[] = array($textNoWrap, 1, 20500, 20500, 0, 10500);

	*/			
		/* Layout */




		$this->SetDataFont();
		$this->AddPage();

		// Headers and widths
		
	   $this->SetFont('arialpl', 'B', 16);
		$this->Cell($this->PG_W, 8, 'Nadajto', 0, 0, 'C');
		$this->Ln();



		$this->Cell($this->PG_W, 5, "FAKTURA VAT $nazwa", 0, 0, 'C');

		$this->Ln(10);

		$this->SetFont('arialpl', 'B', 10);
                
                if ($print=='oryginal') {
                    $this->Cell($this->PG_W, 5, Utf2Iso('Oryginał') , 0, 0, 'C');   
                    $this->Ln(10);
                }
                else {
                    $this->Cell($this->PG_W, 5, 'Kopia' , 0, 0, 'C');
                    $this->Ln(10);
                }

                if ($tabKontrahent['firma']!='')  $wartosc=$tabKontrahent['firma'];
                else $wartosc=$tabKontrahent['nazwiskoImie'];

		//$wartosc=iconv('iso-8859-2','windows-1250//TRANSLIT', $wartosc);
		$this->Cell(20, 5, "Sprzedawca: ".$tabOwner['firma'].' '.$tabOwner['nazwisko'] , 0, 0, 'L');
		$this->Cell(91, 5, '' , 0, 0, 'L');
		$this->Cell(46, 5, "Nabywca: $wartosc", 0, 0, 'L');

		$this->Ln();

		$this->setTextFont(false);
		$this->Cell(24, 5, "Adres:", 0, 0, 'R');
		$this->Cell(86, 5, $tabOwner['adres'], 0, 0, 'L');
		$this->Cell(20, 5, "Adres:", 0, 0, 'R');
		$this->Cell(40, 5, $tabKontrahent['adres'], 0, 0, 'L');

                $this->Ln();
                
                $this->Cell(25, 5, "Nr identyf.: ", 0, 0, 'R');
		$this->Cell(24, 5, $tabOwner['NIP'], 0, 0, 'L');
		$this->Cell(66, 5, " ", 0, 'L');
		if ($tabKontrahent['NIP']!='') {
		    $this->Cell(24, 5, "Nr identyf.: ".$tabKontrahent['NIP'], 0, 0, 'L');
    }
		$this->Ln(10);

		$this->Cell($this->PG_W / 4, 5, "Data wystawienia faktury:", 0, 0, 'L');
		$this->Cell($this->PG_W / 4, 5, date("d/m/Y", strtotime('last day of previous month')), 0, 0, 'L');

		$this->Ln(10);
	
		
		$this->setTextFont(true);
		//wstawic zmienne
		$this->Cell($this->PG_W / 4, 5, Utf2Iso("Sposób zapłaty: $formaPlatnosci"), 0, 0, 'L');
		
                $this->setTextFont(false);
		$this->Cell($this->PG_W / 4, 5, Utf2Iso("Termin zapłaty: $terminPlatnosci"), 0, 0, 'L');
		$this->Ln(10);

    if ($formaPlatnosci=='przelew') {
         $this->setTextFont(true);
		     $this->Cell($this->PG_W, 5, "Dane do przelewu:", 0, 0, 'L');
		     $this->setTextFont(false);
		
		    $this->Ln();
		    $this->Cell($this->PG_W, 5, "Nr konta bankowego: ".$tabOwner['konto'], 0, 0, 'L');
		
		    $this->Ln();
		    $this->Ln();
    }
//koniec heada

		$w = array(65, 10, 25, 20, 20, 20, 25);
                $header = array("Nazwa", Utf2Iso("Ilość."), "Cena netto", Utf2Iso("Wartość netto"), "Stawka VAT", "Kwota VAT", Utf2Iso("Wartość brutto"));

                for($i = 0; $i < count($header); $i++) {
			$this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
		}

		$this->Ln();

		// Mark start coords

		$x = $this->GetX();
		$y = $this->GetY();
		$i = 0;
		
		$set = 0;

                        $tabProd['nazwa']=Utf2Iso($tabProd['nazwa']);
			$y1 = $this->GetY();
			$this->MultiCell($w[0], 5, $tabProd['nazwa'], 'LRB');	
			$y2 = $this->GetY();
			$yH = $y2 - $y1;
						
		  // if it is a new page, reset the y axis value, (This will work only for two pages)
      if($this->pageNo() == 2 && !$set)
      {
            $yH= 6;
            $set = 1; //set this so that this will apply the value only for the first record.
      }
			
                        //Nazwa		
			$this->SetXY($x + $w[0], $this->GetY() - $yH);
			//Ilość
			$this->Cell($w[1], $yH, $tabProd['ilosc'], 'LRB', 0 ,'C');
       			//Cena netto
                        $this->Cell($w[2], $yH, number_format($tabProd['netto'], 2), 'LRB', 0, 'R');
			//Warto?? netto
                        $this->Cell($w[3], $yH, number_format($tabProd['netto'], 2), 'LRB', 0, 'R');
			//stawka Vat
			$this->Cell($w[4], $yH, number_format($tabProd['vat'], 0).'%', 'LRB', 0, 'C');
			//Kwota VAT
			$this->Cell($w[5], $yH, number_format($tabProd['vatamount'], 2), 'LRB', 0, 'R');
			//Warto?? brutto
			$this->Cell($w[6], $yH, number_format($tabProd['brutto'], 2), 'LRB', 0, 'R');

			



						
			$this->Ln();
			
	
		
		$this->Ln(10);

		$this->setTextFont();
		$this->Cell($w[0] + $w[1] + $w[2], 6, 'Suma', 'TB', 0, 'L');

		$this->Cell($w[3], 6, number_format($caloscNetto, 2), 'TB', 0, 'R');
		$this->Cell($w[4], 6, 'X', 'TB', 0, 'C');
		$this->Cell($w[5], 6, number_format($caloscVat, 2), 'TB', 0, 'R');
		$this->Cell($w[6], 6, number_format($caloscBrutto, 2), 'TB', 0, 'R');
		
    $this->Ln();
    /*
	foreach ($tabVat as $row) {

	//W tym (tutaj zrobic zliczenie w mysql ilosc pozycji z roznym vatem nastepnie selekt dla kazdego vatu i w petli obliczyc sumy dla kazdego)
            $this->Cell($w[0] + $w[1] + $w[2] + $w[3], 5, '');
            $this->Cell($w[4], 5, number_format($row[0], 2), 'TRBL', 0, 'R');
            $this->Cell($w[5], 5, number_format($row[1], 0).'%', 'TRB', 0, 'C');
            $this->Cell($w[6], 5, number_format($row[2], 2), 'TRB', 0, 'R');
            $this->Cell($w[7], 5, number_format($row[3], 2), 'TRB', 0, 'R');
    
            $this->Ln();
        }
*/
 
    
		$this->Write(10, "Uwagi: " . Utf2Iso("Dziękujemy za współpracę."),0 , 0, 'L');
		$this->Ln(20);	
		
		

		$this->SetFont('arialpl', '', 6);
		$this->SetFillColor(255); 
		$this->Cell(55, 3, Utf2Iso("Podpis osoby upoważnionej do obioru faktury VAT"), 'T', 0, 'C');
		$this->Cell(70);
		$this->Cell(55, 3, Utf2Iso("Podpis osoby upoważnionej do wystawiania faktury VAT"), 'T', 0, 'C');
		
	}

	public function Footer() {


/*		$this->Ln();
				
			// Footer address
		
		$address = "Roxxor Ltd.\nSomewhere in London\nUK";
		
		$this->SetY(-(($this->getAddressLength($address) * 5) + 20));

		$this->SetFont('arialpl', '', 7);
		
		$this->Ln();
		$this->writeAddress($address); */
	}
        
        public static function generateLastMonth( \Customer $customer) {
            $month_ini = new \DateTime("first day of last month");
            $month_end = new \DateTime("last day of last month");

            $orders = \Model::factory('Order')->where('id_customer', $customer->id_customer)->where_gte('date',$month_ini->format('Y-m-d'))->where_lte('date',$month_end->format('Y-m-d'))->where('status','paid')->where('id_invoice',0);
            $ordersAmount = clone $orders;
            
            $orders = $orders->find_many();
            $ordersAmount = $ordersAmount->count();

            if($ordersAmount>0) {

                $sum = array();
                $invoice = \Model::factory('Invoice')->create();
                
                $invoice->date = $month_end->format('Y-m-d').' 23:59:59';
                
                $monthInvoices = \Model::factory('Invoice')->where_gte('date',$month_ini->format('Y-m-d').' 00:00:00')->where_lte('date',$month_end->format('Y-m-d').' 23:59:59')->find_array();
                $amountInvoices = count($monthInvoices);

                $invoiceName = "FV/NDT/".$month_ini->format('Y')."/".$month_ini->format('m')."/".(int)($amountInvoices+1);

                $invoice->name = $invoiceName;
                $invoice->filename = str_replace('/', '', $invoiceName);
                $invoice->save();
                
                foreach($orders as $order) {
                    if($order instanceof \Order) {
                        $sum['netto'] = number_format($sum['netto']+$order->price_netto, 2, '.', ''); 
                        $sum['brutto'] = number_format($sum['brutto']+$order->price, 2, '.', ''); 
                        $order->id_invoice = $invoice->id();
                        $order->save();
                    }
                }

                $caloscNetto = $sum['netto'];
                $caloscBrutto = $sum['brutto'];
                $caloscVat = $sum['netto']*$GLOBALS['CONFIG']['vat']/100;

                $dataWystawienia = $month_end->format('d-m-Y');
                $terminPlatnosci = 'zapłacone';
                $formaPlatnosci = 'PayU';

                $tabKontrahent['nazwiskoImie']=$customer->name.' '.$customer->lname;
                $tabKontrahent['firma']=$customer->company;
                $tabKontrahent['adres']=$customer->addr.' '.$cutomer->zip.' '.$cutomer->city;
                $tabKontrahent['NIP']=$customer->nip;

                $tabProd['nazwa']='Wysyłka kurierska';
                $tabProd['ilosc']=1;
                $tabProd['netto']=$sum['netto'];
                $tabProd['brutto']=$sum['brutto'];
                $tabProd['vat']=$GLOBALS['CONFIG']['vat'];
                $tabProd['vatamount']= $tabProd['netto']*($tabProd['vat']/100);

                $tabOwner['nazwisko']=$GLOBALS['CONFIG']['cmp_person'];
                $tabOwner['firma']=$GLOBALS['CONFIG']['cmp_name'];
                $tabOwner['adres']=$GLOBALS['CONFIG']['cmp_addr'];
                $tabOwner['NIP']=$GLOBALS['CONFIG']['cmp_nip'];
                //$tabOwner['konto']=$baza->wartosc('Konto');
                $print = 'oryginal';
                /*
                $id=intval($_GET['id']);
                $print=strip_tags($_GET['print']);
                */
                $pdf=new InvoicePDF($tabProd,$tabVat,$tabKontrahent,$tabOwner,$invoiceName,$caloscNetto,$caloscBrutto,$caloscVat, $dataWystawienia,$formaPlatnosci,$terminPlatnosci,$print);
                $pdf->Output($GLOBALS['REALPATH'].'/invoices/'.str_replace('/', '', $invoiceName).'.pdf','F');
            }

        }

	private function setTextFont($isBold = false) {
		$this->SetFont('arialpl', $isBold ? 'B' : '', 8);
	}
	
	private function setDataFont($isBold = false) {
		$this->SetFont('arialpl', $isBold ? 'B' : '', 8);
	}

	private function getAddressLength($address) {
		return count(explode("\n", $address));
	}
		
	private function writeAddress($address) {
		$lines = explode("\n", $address);
		foreach ($lines as $line) {
			$this->Cell($this->PG_W, 5, $line, 0, 0, 'C');
			$this->Ln(4);
		}
	}	
}
?>