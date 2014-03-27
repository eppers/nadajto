{% extends 'layout.php' %}

{% block page_title %}Strona główna{% endblock %}
{% block content %}
	<ul id="tabbed">
		<li class="1"><a href="loadpage?step=1" class="current">Przesyłka</a></li>
		<li class="2"><a href="loadpage?step=2">Nadawca / Odbiorca</a></li>
		<li class="3"><a href="loadpage?step=3">Usługi dodatkowe</a></li>
		<li id="progress"><img alt="wczytuję…" src="img/load.gif"></li>
		<li id="track">
			<form action="track" method="get" name="trackform" onsubmit="setTab(4); return!1;">
			<div>
				<input type="text" id="cno" name="cno" maxlength="12" placeholder="wpisz nr przewozowy" required>
				<button type="submit" title="Wyszukaj">Wyszukaj</button>
			</div>
			</form>
		</li>
	</ul>

<div style="position: relative;">

	<div id="start">
            <div id="subpage1">
                <form id="subpage1form">
                <div class="mainleft">
                        <h3>Rodzaj</h3>
                        <p>
                            <label><input type="radio" class="pkg_type" name="rodzaj" onclick="qwery('#ludzik')[0].src = 'img/pict_1.png';" value="1" checked> Paczka</label>
                            <label><input type="radio" class="pkg_type" name="rodzaj" id="pkg_type_env" onclick="qwery('#ludzik')[0].src = 'img/pict_2.png';" value="2"> Koperta</label>
                            <!--<label><input type="radio" class="pkg_type" name="rodzaj" onclick="qwery('#ludzik')[0].src = 'img/pict_3.png';" value="3"> Paleta</label>-->
                        </p>
                        <h3 style="padding-top: 10px;">Rozmiar</h3>
                        <p><label><span style="display: inline-block; width: 100px; vertical-align: middle;">Waga przesyłki:</span> <input type="text" id="pkg_weight" name="pkg_weight" value="" placeholder="kg" style="text-align: right; width: 85px;"> kg</label></p>
                        <p class="wymiar"><span>Wymiary:</span>
                            <label><input type="text" id="pkg_length" name="pkg_length" title="Długość" value="" placeholder="cm" maxlength="3"> x </label>
                            <label><input type="text" id="pkg_width" name="pkg_width" title="Szerokość" value="" placeholder="cm" maxlength="3"> x </label>
                            <label><input type="text" id="pkg_height" name="pkg_height" title="Wysokość" value="" placeholder="cm" maxlength="3"> cm</label>
                            <br>
                            <var><span>dł.</span> &nbsp; <span>szer.</span> &nbsp; <span>wys.</span></var>
                        </p>
                        <p style="padding-top: 1px;">
                                <label><input type="checkbox" id="Notstand" name="Notstand" value="1"> Niestandardowa przesyłka</label>
                                <img class="help" src="img/help.gif" alt="Pomoc" onmouseover="ddrivetip('Przesyłka niestandardowa:<br>-Przesyłki o nieregularnych kształtach (elementy wystające, okrągłe kształty)<br>-Paczki, których dł. przekracza 120 cm, szer. 80cm lub wys. 80 cm,bądź suma długości i obwodu przekracza 300 cm.<br>-Paczki o wadze powyżej 32 kg.<br>Dopłata 10 zł netto (12,3 zł brutto)<br>Więcej informacji w zakładce <b>Przesyłki niestandardowe<b>');" onmouseout="hideddrivetip();">
                        </p>
                </div>
                <div class="mainright">
                        <h3>Dokąd</h3>
                        <select name="kraj" id="kraj" onchange="if(this.value!=177){ alert('Wysyłka do Czech:\nusługa w trakcie przygotowywania.'); this.value=177; this.selectedIndex=1; return!1; }">
                            <option value="177" selected>Polska</option>
                            <option value="60">Republika Czeska</option>
                        </select>
                        <h3>Usługi w cenie</h3>
                       <!-- <p><span>K-EX</span>
                                <var>
                                        <label><input type="checkbox" name="kex_ubezp1" value="1" onclick="return!1;"{set:KEX_UBEZP1:1} checked{/}> Darmowe ubezpieczenie do 100 zł</label>
                                        <br>
                                        <label><input type="checkbox" name="kex_gwar1" value="1" onclick="return!1;"{set:KEX_GWAR1} checked{/}> Gwarancja dostarczenia na następny dzień</label>
                                </var>
                        </p> -->
                        <p style="padding-top: 10px;"><span>UPS</span>
                                <var>
                                        <label><input type="checkbox" name="ups_Insurance" value="360" onclick="return!1;" checked> Darmowe ubezpieczenie do 323 zł</label>
                                </var>
                        </p>
                </div>
                <br style="clear: both;">
                </form>
            </div>
            <div id="subpage2">
                <div class="mainleft" style="padding-right: 20px;">
                    <h3>Nadawca</h3>
                    <input type="hidden" id="user_id" val="{{session.id}}">

                    <div id="nad_firma" style="display: none;">
                            <p><input type="text" name="nad_nazwa" rel="require" placeholder="Nazwa firmy" value="" style="width: 258px;"></p>
                            <p><input type="text" name="nad_nip" rel="require" placeholder="NIP" value="" style="width: 258px;"></p>
                    </div>
                    <p><input type="text" name="nad_imie" maxlength="21" rel="require" placeholder="Imię lub nazwa firmy" value="{{session.user.name}}" style="width: 258px;"></p>
                    <p><input type="text" name="nad_nazwisko" maxlength="21" placeholder="Nazwisko" value="{{session.user.lname}}" style="width: 258px;"></p>
                    <p>
                            <input type="text" name="nad_ulica" rel="require" placeholder="Ulica" value="{{session.user.street}}" style="width: 115px;">
                            <input type="text" name="nad_nrdomu" rel="require" placeholder="Nr domu" value="{{session.user.no1}}" style="width: 55px;" maxlength="8">
                            <input type="text" name="nad_nrlok" placeholder="Nr lokalu" value="{{session.user.no2}}" style="width: 55px;" maxlength="8">
                    </p>
                    <p><input type="text" name="nad_miasto" rel="require" placeholder="Miasto" value="{{session.user.city}}" style="width: 258px;"></p>
                    <p>
                            <input type="text" name="nad_kod1" rel="require" placeholder="XX" value="{{session.user.zip1}}" style="width: 40px;" maxlength="2">
                            &mdash;
                            <input type="text" name="nad_kod2" rel="require" placeholder="XXX" value="{{session.user.zip2}}" style="width: 50px;" maxlength="3">
                            <span id="nad_miasto">Kod pocztowy</span>
                    </p>
                    <p><input type="email" name="nad_email" rel="require" placeholder="Adres e-mail" value="{{session.user.email}}" style="width: 258px;"></p>
                    <p><input type="email" name="nad_email2" rel="require" placeholder="Powtórz e-mail" value="{{session.user.email}}" style="width: 258px;"></p>
                    <p><input type="text" name="nad_telef" rel="require"  placeholder="Telefon" value="{{session.user.phone}}" style="width: 258px;"></p>
                </div>
                <div class="mainright">
                    <h3>Odbiorca</h3>

                    <div id="odb_firma" style="display: none;">
                            <p><input type="text" name="odb_nazwa" rel="require" placeholder="Nazwa firmy" value="" style="width: 258px;"></p>
                            <p><input type="text" name="odb_nip" rel="require" placeholder="NIP" value="" style="width: 258px;"></p>
                    </div>
                    <p><input type="text" name="odb_imie" maxlength="21" rel="require" placeholder="Imię lub nazwa firmy" value="" style="width: 258px;"></p>
                    <p><input type="text" name="odb_nazwisko" maxlength="21" placeholder="Nazwisko" value="" style="width: 258px;"></p>
                    <p style="padding-top: 1px;">
                            <label><input type="checkbox" id="odb_priv" name="odb_priv" value="1"> Odbiorca prywatny</label>
                    </p>
                    <p>
                            <input type="text" name="odb_ulica" rel="require" placeholder="Ulica" value="" style="width: 115px;">
                            <input type="text" name="odb_nrdomu" rel="require" placeholder="Nr domu" value="" style="width: 55px;" maxlength="8">
                            <input type="text" name="odb_nrlok" placeholder="Nr lokalu" value="" style="width: 55px;" maxlength="8">
                    </p>
                    <p><input type="text" name="odb_miasto" rel="require" placeholder="Miasto" value="" style="width: 258px;"></p>
                    <p>
                            <input type="text" name="odb_kod1" rel="require" placeholder="XX" value="" style="width: 40px;" maxlength="2">
                            &mdash;
                            <input type="text" name="odb_kod2" rel="require" placeholder="XXX" value="" style="width: 50px;" maxlength="3">
                            <span id="odb_miasto">Kod pocztowy</span>
                    </p>
                    <p><input type="text" name="odb_telef" rel="require" placeholder="Telefon" value="" style="width: 258px;"></p>
                </div>
                <br style="clear: both;">
            </div>
            <div id="subpage3">
                <form id="subpage3form">
                <div class="mainleft" style="width: 360px;">
                    <h3>Data nadania przesyłki</h3>
                    <p>
                            <input type="text" name="data_nad" id="data_nad" value="" placeholder="Możliwie najszybciej" readonly style="width: 115px;">
                            <img alt="Data…" src="img/calend.png" class="kalendarz">
                    </p>
                    <p class="mopts">
                            <label><input type="checkbox" name="Insurance_check" rel="input" value="1"> Dodatkowe ubezpieczenie</label>
                            <input type="text" name="Insurance_input" value="" style="text-align: right;"> zł
                    </p>
                    <p class="mopts">
                            <label><input type="checkbox" name="COD_check" rel="input" value="1"> Przesyłka pobraniowa</label>
                            <input type="text" name="COD_input" value="" style="text-align: right;"> zł
                            <input type="text" id="bank-account" name="account-no" value="" placeholder="Numer rachunku bankowego" style="display: none; width: 310px; margin-top: 10px; ">
                    </p>
                    
                   <p class="mopts">
                            <label><input type="checkbox" name="ROD" value="1"> Zwrot potwierdzonych dokumentów (ROD)</label>
                    </p>
                   
                  <!--  <p class="mopts">
                            <label><input type="checkbox" name="k-ex_RODexpr" value="1"> Express (tylko K-EX)</label>
                    </p>
                  -->
                </div>
                <div class="mainright" style="color: #757575; line-height: 150%;">
                    <p><b>Odbiory paczek odbywają się<br>w godziach od 10 do 18.</b><br>Nie ma możliwości ustalenia<br>konkretnej godziny odbioru.</p>
                    <p><b>Dodatkowe ubezpiecznie:</b>
                            <br>UPS - maksymalna wysokość<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ubezpieczenia wynosi 50 000 zł,
                            <!--<br>K-EX - maksymalna wysokość<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ubezpieczenia wynosi 100 000 zł.-->
                    </p>
                    <p><b>Zwrot pobrania następuje do 6 dni roboczych</b>
                            <!--<br>K-EX - do 12 lub 5 (express) dni roboczych-->
                    </p>

                </div>
                <br style="clear: both;">
                </form>
        </div>
        <div id="subpage4"></div></div>

	<div class="nadajto">
        {% for courier in couriers %}
        <div class="price-container" style="display:inline-block;">    
            <p><span class="price" id="courier_{{courier.id}}">{{courier.price}}</span> netto</p>
            <p><span class="price_brutto" id="courier_{{courier.id}}">{{courier.price_brutto}}</span> brutto</p>
            <button type="button" class="dark" id="go_c{{courier.id}}" >NadajTo <b>{{courier.name}}</b></button><span class="ajax-loader"></span>
        </div>
        {% else %} BRAK
        {% endfor %}
        
        <form method="GET" id="sendingpayu" action="{{payULink}}">
            <input type="hidden" id="sessionId" name="sessionId" value="">
            <input type="hidden" id="oauth_token" name="oauth_token" value="">
            <input type="hidden" name="showLoginDialog" value="False">
        </form>
		<span class="preload">
			<img alt="" src="img/pict_1.png">
			<img alt="" src="img/pict_2.png">
			<img alt="" src="img/pict_3.png">
		</span>
	</div>
	 </div>
<script defer="defer" type="text/javascript">
<!--
 qwery('#ludzik')[0].src = 'img/pict_1.png';

//-->
</script>
{% endblock %}