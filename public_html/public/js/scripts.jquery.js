var $j = jQuery.noConflict();
$j(document).ready(function() {
    $j(".ajax-loader").hide();
    $j("input[name=nad_nazwa]").prop('disabled', true);
    $j("input[name=nad_nip]").prop('disabled', true);  
    $j("input[name=odb_nazwa]").prop('disabled', true);
    $j("input[name=odb_nip]").prop('disabled', true); 
    
    $j("input[name=rodzaj]").click(function(){
        $j("input#pkg_weight").val('').prop('readonly', false);
        $j("input#pkg_length").val('').prop('readonly', false);
        $j("input#pkg_width").val('').prop('readonly', false);
        $j("input#pkg_height").val('').prop('readonly', false);
        $j("input#Notstand").closest("p").show();
    });
    
    $j("input#pkg_type_env").click(function(){
        $j("input#pkg_weight").val(1).prop('readonly', true);
        $j("input#pkg_length").val(35).prop('readonly', true);
        $j("input#pkg_width").val(25).prop('readonly', true);
        $j("input#pkg_height").val(5).prop('readonly', true);
        $j("input#Notstand").closest("p").hide();
    }); 
    
    $j("#dialog").dialog({
      autoOpen: false,
      modal: true
    });
    
    $j('.nadajto').css('top','-100px');
   
    $j('.nadajto').attr('top','-100px');
    
    $j('#tabbed li a').click(function(){
        var clas = $j(this).closest('li').attr('class'),
             top = '0px';
        switch (clas) {
            case '1': top = '-100px'; break;
            case '2': top = '0px'; break;
            case '3': top = '-130px'; break;
        }
        $j('.nadajto').css('top',top);
    })
    
    
    
    $j('input[name=COD_check]').click(function(){
        $j('#bank-account').toggle();
        if(!$j(this).prop('checked')) {
            $j('input[name=COD_input]').val('');
        }
    })
    
    $j('input[name*=_imie]').keyup(function(){
        $j('.formError').remove();
        var allowed = 22;
        
        var lname = $j(this).closest('div').find('input[name*=_nazwisko]');
        var nameAllowed = allowed-lname.val().length;
        var thisVal = $j(this).val();
        //var sum = $j(this).val().length+lname.val().length;
        if(thisVal.length>=nameAllowed) { 
            $j(this).addClass('error'); 
            formError('Przekraczasz 22 znaków dla sumy imienia i nazwiska.',$j(this));
            $j(this).val(thisVal.substr(0,nameAllowed));
        }
    });
    
    $j('input[name*=_nazwisko]').keyup(function(){
        $j('.formError').remove();
        var allowed = 22;
        
        var lname = $j(this).closest('div').find('input[name*=_imie]');
        var nameAllowed = allowed-lname.val().length;
        var thisVal = $j(this).val();
        //var sum = $j(this).val().length+lname.val().length;
        if(thisVal.length>=nameAllowed) { 
            $j(this).addClass('error'); 
            formError('Przekraczasz 22 znaków dla sumy imienia i nazwiska.',$j(this));
            $j(this).val(thisVal.substr(0,nameAllowed));
        }
    });

    $j('#start').find('input[type=checkbox]').click(function(){
       checkboxChange();
    });
    
    $j('#subpage1 input[type=text]').keyup(function(){
       checkboxChange();
    });
    
    $j('#subpage2 input[name*=kod1]').keyup(function(){
       if($j(this).val().length == 2) $j(this).closest('p').find('input[name*=kod2]').focus();
    });
    
    $j('#subpage3 input[type=text]').keyup(function() {
        var checkbox = $j(this).closest('p').find('input[type=checkbox]');
        if($j.trim($j(this).val()).length > 0 && checkbox.prop('checked')===true) checkboxChange();
    });
    
    $j('#go_c1').bind('mouseover',function(e) {
        ddrivetip('Nie wszystkie potrzebne pola zostały uzupełnione.');
        e.preventDefault();
        });
    $j('#go_c1').bind('mouseout',function(e) {
        hideddrivetip();
        e.preventDefault();        
    });
    
    
    $j.datepicker.regional['pl'] = {
        closeText: 'Zamknij',
        prevText: '&#x3c;Poprzedni',
        nextText: 'Następny&#x3e;',
        currentText: 'Dziś',
        monthNames: ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec',
        'Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
        monthNamesShort: ['Sty','Lu','Mar','Kw','Maj','Cze',
        'Lip','Sie','Wrz','Pa','Lis','Gru'],
        dayNames: ['Niedziela','Poniedzialek','Wtorek','Środa','Czwartek','Piątek','Sobota'],
        dayNamesShort: ['Nie','Pn','Wt','Śr','Czw','Pt','So'],
        dayNamesMin: ['N','Pn','Wt','Śr','Cz','Pt','So'],
        weekHeader: 'Tydz',
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''};
    $j.datepicker.setDefaults($j.datepicker.regional['pl']);
    
    $j("#data_nad").datepicker({ 
        beforeShowDay: $j.datepicker.noWeekends,
        minDate: 0
    });
    $j(".kalendarz").click(function() { 
        var d = new Date();
        var h = d.getHours();
        if(h>=12) $j('#data_nad').datepicker('option', 'minDate', '1');
        $j("#data_nad").datepicker( "show" );
    });
    
    $j("#btn-prepay").click(function() { 
        
        var user = [],
            amount = $j('input[name=amount]').val();
        if(amount==0) alert('Wpisz wartość doładowania');
        else {
            user['name'] = $j('input[name=name]').val();
            user['lname'] = $j('input[name=lname]').val();
            user['email'] = $j('input[name=email]').val();
            user['phone'] = $j('input[name=phone]').val();
        
            prepayadd(user,amount);
        }
    });
    
    $j('body').find('input').keypress(function(){
       if($j(this).hasClass('error')) $j(this).removeClass('error');
    });
    
    
    //on key pres chyba lepiej dac podswietalnie klawisza - nie unbinduje oraz nie binduje . do sprawdzenia.
    $j('#start').find('input[type=text]').change(function(){
        
        if(checkFields()===true && $j('#subpage1 input[type=radio]:checked').val()!=3) {
            $j("#go_c1").unbind('mouseover').unbind('mouseout');
            $j('#go_c1').unbind('click').bind('click', function(){
              var from = new Array(),
                    to = new Array(),
                   pkg = new Array(),
                  date = '',
                  user = new Array(),
                  amount = '',
                  bankacc, //bank account for COD
                  courier = 1; //1 dla UPS (id courier)
              
              $j('#start').find('.error').removeClass('error');
        
              user['id'] = $j('#user_id').val();
        
              pkg['type'] = $j('.pkg_type:checked').val();    
              pkg['weight'] = $j('#pkg_weight').val();
              pkg['length'] = $j('#pkg_length').val();
              pkg['width'] = $j('#pkg_width').val();
              pkg['height'] = $j('#pkg_height').val();
              pkg['notstand'] = $j('#pkg_notstand').val();
              
              //from['type'] = $j('input[name=nad_typ]:checked').val();
              from['company'] = $j('input[name=nad_nazwa]').val();
              from['nip'] = $j('input[name=nad_nip]').val();
              from['name'] = $j('input[name=nad_imie]').val();
              from['lname'] = $j('input[name=nad_nazwisko]').val(); 
              from['addr'] = $j('input[name=nad_ulica]').val();
              from['addr_house'] = $j('input[name=nad_nrdomu]').val() +' '+ $j('input[name=nad_nrlok]').val();  
              from['city'] = $j('input[name=nad_miasto]').val(); 
              from['zip'] = $j('input[name=nad_kod1]').val() +'-'+ $j('input[name=nad_kod2]').val();
              from['phone'] = $j('input[name=nad_telef]').val();
              from['email'] = $j('input[name=nad_email]').val();
              from['email2'] = $j('input[name=nad_email2]').val();
              
              //to['type'] = $j('input[name=odb_typ]:checked').val();
              to['company'] = $j('input[name=odb_nazwa]').val();
              to['nip'] = $j('input[name=odb_nip]').val();
              to['name'] = $j('input[name=odb_imie]').val();
              to['lname'] = $j('input[name=odb_nazwisko]').val(); 
              to['addr'] = $j('input[name=odb_ulica]').val();
              to['addr_house'] = $j('input[name=odb_nrdomu]').val() +' '+ $j('input[name=odb_nrlok]').val();  
              to['city'] = $j('input[name=odb_miasto]').val(); 
              to['zip'] = $j('input[name=odb_kod1]').val() +'-'+ $j('input[name=odb_kod2]').val();
              to['phone'] = $j('input[name=odb_telef]').val();
              
              if($j('#odb_priv').is(':checked')) {
                  to['priv'] = $j('#odb_priv').val();
              } else to['priv']=0;
              
              date = $j('input[name=data_nad]').val();
              amount = $j(this).parent().find('.price').html();
              if($j('#bank-account').is(":visible")) {
                  bankacc=$j('#bank-account').val(); 
              } else bankacc=false;
              
              var form1 = $j('#subpage1form').serialize(),
                 form3 =  $j('#subpage3form').serialize(),
                 form = form1+'&'+form3;
//shipping                
//              ship(from,to,pkg,date);
              sendpayu(courier, user['id'],from,to,pkg,date,amount,form,bankacc);
            });
            $j('#go_c1').css('background','#0da65e');
        } else {
            $j('#go_c1').bind('mouseover',function(e) {
                ddrivetip('Nie wszystkie potrzebne pola zostały uzupełnione.');
                e.preventDefault();
                });
            $j('#go_c1').bind('mouseout',function(e) {
                hideddrivetip();
                e.preventDefault();        
            });
            $j("#go_c1").unbind('click');
            $j('#go_c1').css('background','#70706d');
        }
    });
    
    $j('#register-btn').click(function(e){
        e.preventDefault();
        var imie = $j('input[name=imie]').val(),
            nazwisko = $j('input[name=nazwisko]').val(),
            telefon = $j('input[name=telefon]').val(),
            email = $j('input[name=email]').val(),
            passw = $j('input[name=passw]').val(),
            passw2 = $j('input[name=passw2]').val();
            
        $j('.bform').find('p.error').remove();    
        if($j('#akct').is(":checked"))
            register(imie,nazwisko,telefon,email,passw,passw2);
        else alert('Musisz zaakceptować regulamin.');
    });
    
    $j('#login-btn').click(function(e){
        e.preventDefault();
        var login = $j('input[name=login]').val(),
            pass = $j('input[name=passw]').val();
            
        $j('.bform').find('p.error').remove();  

            loginToAccount(login,pass);

    });
    
    
 $j(".confirmLink").click(function(e) {
    e.preventDefault();
    var targetUrl = $j(this).attr("href");

    $j("#dialog").dialog({
      buttons : {
        "Tak" : function() {
          var id = $j('#orderId').val();  
          cancelShipment(id);
        },
        "Anuluj" : function() {
          $j(this).dialog("close");
        }
      }
    });

    $j("#dialog").dialog("open");
  });



});

function checkboxChange() {
    
        var form1 = $j('#subpage1form').serialize(),
            form3 =  $j('#subpage3form').serialize(),
            form = form1+'&'+form3;
    
            updatePrice(form)
}

function cancelShipment(id) {
     $j.ajax({
              type: 'POST',
	      url: "/api/ship/void",
		  //cache: false,
              data: {
                  id : id
              },
              dataType: "json",
              beforeSend: function(){
                $j('.ajax-loader').show();
              },
              success: function(data) {
               $j('.ajax-loader').hide();
               $j("#dialog").dialog('close'); 
                if(data.error == undefined && data == 'ok') {
                  $j('tr.tr'+id).find('td.status').html('anulowane');
                  $j('tr.tr'+id).find('a[rel=cancel]').hide();
                  alert('zamówienie zostało anulowane');
                } else {
                    alert(data.error);
                }

                console.log(data);
                    
                
              },
            error: function(xhr,textStatus,err)
                {
                    console.log("readyState: " + xhr.readyState);
                    console.log("responseText: "+ xhr.responseText);
                    console.log("status: " + xhr.status);

                }                       
        
	 });
            
}

function updatePrice(form) {
     $j.ajax({
              type: 'POST',
	      url: "/form/price/update",
		  //cache: false,
              data: {
                  form : form
              },
              dataType: "json",
              beforeSend: function(){
                $j('.ajax-loader').show();
              },
              success: function(data) {
               $j('.ajax-loader').hide();
                //console.log(data);
                    if(data.error == undefined) {
                        for(var i in data)
                        {
                            //console.log(data[i]);
                            $j('#courier_'+i+'.price').html(data[i]['price_net']);
                            $j('#courier_'+i+'.price_brutto').html(data[i]['price_brut']);
                        }
                    } else {
                        alert(data.error);
                    }
                console.log(data);
                    
                
              },
            error: function(xhr,textStatus,err)
                {
                    console.log("readyState: " + xhr.readyState);
                    console.log("responseText: "+ xhr.responseText);
                    console.log("status: " + xhr.status);

                }                       
        
	 });
            
}

function checkFields() {
  var proceed = 0;
  var n;
  //subpage1
  n = $j('#subpage1 input[type=radio]:checked').length;
  if(n!=1) proceed++;
  

  $j('#subpage1').find('input[type="text"]').each(function() {
      if($j(this).val()=='') {proceed++;}
  });
  
  //subpage2

  
  $j('#subpage2').find('input[type="text"][rel="require"]').each(function() {
      if($j(this).val()=='' && $j(this).prop('disabled')==false) { proceed++ };
  });
    

   if(proceed>0)return false; else return true;
};

 function nadFirm()
 {
	if($j('#nadfirma').is(':checked')) {
		$j('#nad_firma').show();
                $j("input[name=nad_nazwa]").prop('disabled', false);
                $j("input[name=nad_nip]").prop('disabled', false);                
        } else {
		$j('#nad_firma').hide();
                $j("input[name=nad_nazwa]").prop('disabled', true);
                $j("input[name=nad_nip]").prop('disabled', true);                
        }
 }

 function odbFirm()
 {
	if($j('#odbfirma').is(':checked')) {
		$j('#odb_firma').show();
                $j("input[name=odb_nazwa]").prop('disabled', false);
                $j("input[name=odb_nip]").prop('disabled', false);
        } else {
		$j('#odb_firma').hide();
                $j("input[name=odb_nazwa]").prop('disabled', true);
                $j("input[name=odb_nip]").prop('disabled', true);
        }
 }



function ship(from,to,pkg,date) {
	 $j.ajax({
              type: 'POST',
	      url: "/api/ups/ship",
		  //cache: false,
              data: {
                  //nad_type : from['type'],        
                  nad_nazwa: from['company'],
                  nad_nip: from['nip'],
                  nad_imie: from['name'], 
                  nad_nazwisko: from['lname'], 
                  nad_addr: from['addr'], 
                  nad_nrdomu: from['addr_house'],
                  nad_miasto: from['city'], 
                  nad_zip: from['zip'],
                  nad_email: from['email'],
                  nad_email2: from['email2'],
                  //nad_country: 'PL',
                  nad_telef: from['phone'],
                  //odb_type : to['type'],              
                  odb_nazwa: to['company'],
                  odb_nip: to['nip'],              
                  odb_imie: to['name'], 
                  odb_nazwisko: to['lname'], 
                  odb_addr: to['addr'], 
                  odb_nrdomu: to['addr_house'],
                  odb_miasto: to['city'], 
                  odb_zip: to['zip'],
                  //odb_country: 'PL',
                  odb_telef: to['phone'],
                  waga: pkg['weight'],
                  dlu: pkg['lenght'],
                  szer: pkg['width'],
                  wys: pkg['height'],
                  data_nad: date
              },
              dataType: "json",
            beforeSend: function(){
                $j('.ajax-loader').show();
            },
            success: function(data) {
               $j('.ajax-loader').hide();
                  
                console.log(data);
                if(data.input == undefined) {
                    console.log(data);
                }
                else if(data.input.length>0) {
                    if($j.inArray('nad_zip', data.input)>-1) {
                        $j('input[name^=nad_kod').addClass('error');
                    }
                    if($j.inArray('odb_zip', data.input)>-1) {
                        $j('input[name^=odb_kod').addClass('error');
                    }
                     if($j.inArray('nad_email', data.input)>-1) {
                        $j('input[name^=nad_email').addClass('error');
                    }
                    $j.each(data.input, function() {
                        $j('input[name='+this+']').addClass('error');
                    });
                    
                    console.log(data);
                }
                               
              },
            error: function(xhr,textStatus,err)
                {
                    console.log("readyState: " + xhr.readyState);
                    console.log("responseText: "+ xhr.responseText);
                    console.log("status: " + xhr.status);

                }                       
        
	 });
            
}

 function prepayadd(user,amount) {
     $j.ajax({
            type: "POST",
            url: '/prepay/add',
            data: {
                   email : user['email'],
                   name : user['name'],
                   lname : user['lname'],
                   phone : user['phone'],
                   amount : amount
                 },
            dataType: "json",
            async: false,
            beforeSend: function(){
            $j('.ajax-loader').show();
            },
            success: function(data) {
               var tab, tmp;
               $j('.ajax-loader').hide();
               $j('#sessionId').val(data.sessionid);
               $j('#oauth_token').val(data.token);
               
              
                if(data.input == undefined) {
                    $j('form#sendingpayu').submit();
                } 
                else if(data.input.length>0) {
                    $j.each(data.input, function(index) {
                        if(!$j('input[name='+this+']').hasClass('error')) {
                            formError(data.msg[index],$j('input[name='+this+']'));
                        }
                        $j('input[name='+this+']').addClass('error');
              
                    });
                }
              
              
                 console.log(data);
                 
                 
                 
                },
             error: function(xhr,textStatus,err)
                {
                    console.log("readyState: " + xhr.readyState);
                    console.log("responseText: "+ xhr.responseText);
                    console.log("status: " + xhr.status);

                }
        });
 }

 function sendpayu(courierid, userid, from, to, pkg, date, amount, form, bank) {   
        $j('.formError').remove();
        $j.ajax({
            type: "POST",
            url: '/payment/payu/sending',
            data: {
                  courierid : courierid, 
                  userid : userid, 
                  amount : amount,
                  //nad_type : from['type'],        
                  nad_nazwa: from['company'],
                  nad_nip: from['nip'],
                  nad_imie: from['name'], 
                  nad_nazwisko: from['lname'], 
                  nad_addr: from['addr'], 
                  nad_nrdomu: from['addr_house'],
                  nad_miasto: from['city'], 
                  nad_zip: from['zip'],
                  nad_email: from['email'],
                  nad_email2: from['email2'],
                  //nad_country: 'PL',
                  nad_telef: from['phone'],
                  //odb_type : to['type'],              
                  odb_nazwa: to['company'],
                  odb_nip: to['nip'],              
                  odb_imie: to['name'], 
                  odb_nazwisko: to['lname'], 
                  odb_addr: to['addr'], 
                  odb_nrdomu: to['addr_house'],
                  odb_miasto: to['city'], 
                  odb_zip: to['zip'],
                  odb_priv: to['priv'],
                  //odb_country: 'PL',
                  odb_telef: to['phone'],
                  weight: pkg['weight'],
                  length: pkg['length'],
                  width: pkg['width'],
                  height: pkg['height'],
                  pkg_type : pkg['type'],
                  data_nad: date,
                  form: form,
                  bank: bank
                 },
            dataType: "json",
            async: false,
            beforeSend: function(){
            $j('.ajax-loader').show();
            },
            success: function(data) {
               var tab, tmp;
               $j('.ajax-loader').hide();
               $j('#sessionId').val(data.sessionid);
               $j('#oauth_token').val(data.token);
               
               //jezeli nie jest to input typu text to wrzucic tooltip z dymkiem gdzie info jest z msg obok danego pola
                if(data.error != undefined) {
                    alert('Wystąpił błąd. '+data.error);
                }                
                else if(data.prepay == 'prepay') {
                    alert('Opłata została pobrana ze skarbonki. Etykieta powinna zostać wysłana na Twój email');
                }
                else if(data.input == undefined) {
                    $j('form#sendingpayu').submit();
                } 
                else if(data.input.length>0) {
                    if($j.inArray('nad_zip', data.input)>-1) {
                        $j('input[name^=nad_kod').addClass('error');
                        formError(data.msg[$j.inArray( "nad_zip", data.input )],$j('input[name=nad_kod1]'));
                    }
                    if($j.inArray('odb_zip', data.input)>-1) {
                        $j('input[name^=odb_kod').addClass('error');
                        formError(data.msg[$j.inArray( "odb_zip", data.input )],$j('input[name=odb_kod1]'));
                    }
                     if($j.inArray('nad_email', data.input)>-1) {
                        if(!$j('input[name^=nad_email').hasClass('error')) {
                             formError(data.msg[$j.inArray( "nad_email", data.input )],$j('input[name^=nad_email]'));
                        }
                        $j('input[name^=nad_email').addClass('error');
                    }
                    $j.each(data.input, function(index) {
                        if(!$j('input[name='+this+']').hasClass('error')) {
                            formError(data.msg[index],$j('input[name='+this+']'));
                        }
                        $j('input[name='+this+']').addClass('error');
                        tmp = $j('input[name='+this+']').closest('div[id^="subpage"]').attr('id');
                        //console.log('tab '+tab);
                        console.log(tmp);
                        
                        if(tmp !== undefined)
                            tab = tmp.substring(7,8);
                    });
                    console.log('tab2 '+tab);
                  //przesuwanie tab do tej z bledem
                  //    emile($j('#start'), 'text-indent: -'+((1) * 920)+'px;', {duration: 500 });
                    
                }
                 console.log(data);
                },
             error: function(xhr,textStatus,err)
                {
                    console.log("readyState: " + xhr.readyState);
                    console.log("responseText: "+ xhr.responseText);
                    console.log("status: " + xhr.status);

                }
        });
}

 function register(imie, nazwisko, telefon, email, passw, passw2) {   
        $j.ajax({
            type: "POST",
            url: '/rejestracja',
            data: {
                  imie : imie, 
                  nazwisko : nazwisko, 
                  telefon : telefon,
                  email : email, 
                  passw : passw, 
                  passw2 : passw2,                  
                 },
            dataType: "json",
            async: false,
            beforeSend: function(){
            $j('.ajax-loader').show();
            },
            success: function(data) {
               $j('.ajax-loader').hide();
               
                if(data.input == undefined) {
                    window.location.href = "/";
                }
                else if(data.input.length>0) {
                  
                    if($j.inArray('passw', data.input)>-1) {
                        $j('input[name^=passw').addClass('error');
                    }
                    $j.each(data.input, function(index) {
                        $j('input[name='+this+']').closest('p').before('<p class="error">'+data.msg[index]+'</p>');
                        $j('input[name='+this+']').addClass('error');
                    });
                }
                 console.log(data);
                },
             error: function(xhr,textStatus,err)
                {
                    console.log("readyState: " + xhr.readyState);
                    console.log("responseText: "+ xhr.responseText);
                    console.log("status: " + xhr.status);

                }
        });
}

 function loginToAccount(login, pass) {   
        $j.ajax({
            type: "POST",
            url: '/logowanie',
            data: {
                  login : login, 
                  pass : pass                  
                 },
            dataType: "json",
            async: false,
            beforeSend: function(){
            $j('.ajax-loader').show();
            },
            success: function(data) {
               $j('.ajax-loader').hide();
               
                if(data == 'admin') {
                    window.location.href = "/admin/";
                }
                else if(data.input == undefined) {
                    window.location.href = "/user/faktury";
                }
                else if(data.input.length>0) {
                  $j.each(data.input, function(index) {
                        $j('input[name='+this+']').closest('p').before('<p class="error">'+data.msg[index]+'</p>');
                        $j('input[name='+this+']').addClass('error');
                  });
                }
                 console.log(data);
                },
             error: function(xhr,textStatus,err)
                {
                    console.log("readyState: " + xhr.readyState);
                    console.log("responseText: "+ xhr.responseText);
                    console.log("status: " + xhr.status);

                }
        });
}

function formError(msg,obj){
    obj.before('<p class="formError" style="color:red;">'+msg+'</p>');
}
