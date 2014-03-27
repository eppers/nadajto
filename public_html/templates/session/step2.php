{mask:main}
	<div class="mainleft" style="padding-right: 20px;">
		<h3>Nadawca</h3>
		<p><label><input type="radio" name="nad_typ" value="0" onclick="nadFirm();"{set:NAD_TYP:0} checked{/}> Osoba</label>
		   <label><input type="radio" id="nadfirma" name="nad_typ" value="1" onclick="nadFirm();"{set:NAD_TYP:1} checked{/}> Firma</label>
		</p>
		<div id="nad_firma"{mis:NAD_TYP:1} style="display: none;"{/}>
			<p><input type="text" name="nad_nazwa" value="{mis:NAD_FIRMA}Nazwa firmy{/}{set:NAD_FIRMA}{NAD_FIRMA}{/}" onblur="if(this.value=='') this.value='Nazwa firmy';" onfocus="if(this.value=='Nazwa firmy') this.value='';" style="width: 258px;"></p>
			<p><input type="text" name="nad_nip" value="{mis:NAD_NIP}NIP{/}{set:NAD_NIP}{NAD_NIP}{/}" onblur="if(this.value=='') this.value='NIP';" onfocus="if(this.value=='NIP') this.value='';" style="width: 258px;"></p>
		</div>
		<p><input type="text" name="nad_imie" value="{mis:NAD_IMIE}Imię{/}{set:NAD_IMIE}{NAD_IMIE}{/}" onblur="if(this.value=='') this.value='Imię';" onfocus="if(this.value=='Imię') this.value='';" style="width: 258px;"></p>
		<p><input type="text" name="nad_nazwisko" value="{mis:NAD_NAZWISKO}Nazwisko{/}{set:NAD_NAZWISKO}{NAD_NAZWISKO}{/}" onblur="if(this.value=='') this.value='Nazwisko';" onfocus="if(this.value=='Nazwisko') this.value='';" style="width: 258px;"></p>
		<p>
			<input type="text" name="nad_ulica" value="{mis:NAD_ULICA}Ulica{/}{set:NAD_ULICA}{NAD_ULICA}{/}" onblur="if(this.value=='') this.value='Ulica';" onfocus="if(this.value=='Ulica') this.value='';" style="width: 115px;">
			<input type="text" name="nad_nrdomu" value="{mis:NAD_NRDOMU}Nr domu{/}{set:NAD_NRDOMU}{NAD_NRDOMU}{/}" onblur="if(this.value=='') this.value='Nr domu';" onfocus="if(this.value=='Nr domu') this.value='';" style="width: 55px;" maxlength="4">
			<input type="text" name="nad_nrlok" value="{mis:NAD_NRLOK}Nr lokalu{/}{set:NAD_NRLOK}{NAD_NRLOK}{/}" onblur="if(this.value=='') this.value='Nr lokalu';" onfocus="if(this.value=='Nr lokalu') this.value='';" style="width: 55px;" maxlength="4">
		</p>
		<p>
			<input type="text" name="nad_kod1" value="{mis:NAD_KOD1}XX{/}{set:NAD_KOD1}{NAD_KOD1}{/}" onblur="if(this.value=='') this.value='XX';" onfocus="if(this.value=='XX') this.value='';" style="width: 40px;" maxlength="2">
			&mdash;
			<input type="text" name="nad_kod2" value="{mis:NAD_KOD2}XXX{/}{set:NAD_KOD2}{NAD_KOD2}{/}" onblur="if(this.value=='') this.value='XXX';" onfocus="if(this.value=='XXX') this.value='';" style="width: 50px;" maxlength="3">
			<span id="nad_miasto">Kod pocztowy</span>
		</p>
		<p><input type="text" name="nad_email" value="{mis:NAD_EMAIL}Adres e-mail{/}{set:NAD_EMAIL}{NAD_EMAIL}{/}" onblur="if(this.value=='') this.value='Adres e-mail';" onfocus="if(this.value=='Adres e-mail') this.value='';" style="width: 258px;"></p>
		<p><input type="text" name="nad_email2" value="{mis:NAD_EMAIL2}Powtórz e-mail{/}{set:NAD_EMAIL2}{NAD_EMAIL2}{/}" onblur="if(this.value=='') this.value='Powtórz e-mail';" onfocus="if(this.value=='Powtórz e-mail') this.value='';" style="width: 258px;"></p>
		<p><input type="text" name="nad_telef" value="{mis:NAD_TELEF}Telefon{/}{set:NAD_TELEF}{NAD_TELEF}{/}" onblur="if(this.value=='') this.value='Telefon';" onfocus="if(this.value=='Telefon') this.value='';" style="width: 258px;"></p>
	</div>
	<div class="mainright">
		<h3>Odbiorca</h3>
		<p><label><input type="radio" name="odb_typ" onclick="odbFirm();" value="0"{set:ODB_TYP:0} checked{/}> Osoba</label>
		   <label><input type="radio" id="odbfirma" name="odb_typ" onclick="odbFirm();" value="1"{set:ODB_TYP:1} checked{/}> Firma</label>
		</p>
		<div id="odb_firma"{mis:ODB_TYP:1} style="display: none;"{/}>
			<p><input type="text" name="odb_nazwa" value="{mis:ODB_FIRMA}Nazwa firmy{/}{set:ODB_FIRMA}{ODB_FIRMA}{/}" onblur="if(this.value=='') this.value='Nazwa firmy';" onfocus="if(this.value=='Nazwa firmy') this.value='';" style="width: 258px;"></p>
			<p><input type="text" name="odb_nip" value="{mis:ODB_NIP}NIP{/}{set:ODB_NIP}{ODB_NIP}{/}" onblur="if(this.value=='') this.value='NIP';" onfocus="if(this.value=='NIP') this.value='';" style="width: 258px;"></p>
		</div>
		<p><input type="text" name="odb_imie" value="{mis:ODB_IMIE}Imię{/}{set:ODB_IMIE}{ODB_IMIE}{/}" onblur="if(this.value=='') this.value='Imię';" onfocus="if(this.value=='Imię') this.value='';" style="width: 258px;"></p>
		<p><input type="text" name="odb_nazwisko" value="{mis:ODB_NAZWISKO}Nazwisko{/}{set:ODB_NAZWISKO}{ODB_NAZWISKO}{/}" onblur="if(this.value=='') this.value='Nazwisko';" onfocus="if(this.value=='Nazwisko') this.value='';" style="width: 258px;"></p>
		<p>
			<input type="text" name="odb_ulica" value="{mis:ODB_ULICA}Ulica{/}{set:ODB_ULICA}{ODB_ULICA}{/}" onblur="if(this.value=='') this.value='Ulica';" onfocus="if(this.value=='Ulica') this.value='';" style="width: 115px;">
			<input type="text" name="odb_nrdomu" value="{mis:ODB_NRDOMU}Nr domu{/}{set:ODB_NRDOMU}{ODB_NRDOMU}{/}" onblur="if(this.value=='') this.value='Nr domu';" onfocus="if(this.value=='Nr domu') this.value='';" style="width: 55px;" maxlength="4">
			<input type="text" name="odb_nrlok" value="{mis:ODB_NRLOK}Nr lokalu{/}{set:ODB_NRLOK}{ODB_NRLOK}{/}" onblur="if(this.value=='') this.value='Nr lokalu';" onfocus="if(this.value=='Nr lokalu') this.value='';" style="width: 55px;" maxlength="4">
		</p>
		<p>
			<input type="text" name="odb_kod1" value="{mis:ODB_KOD1}XX{/}{set:ODB_KOD1}{ODB_KOD1}{/}" onblur="if(this.value=='') this.value='XX';" onfocus="if(this.value=='XX') this.value='';" style="width: 40px;" maxlength="2">
			&mdash;
			<input type="text" name="odb_kod2" value="{mis:ODB_KOD2}XXX{/}{set:ODB_KOD2}{ODB_KOD2}{/}" onblur="if(this.value=='') this.value='XXX';" onfocus="if(this.value=='XXX') this.value='';" style="width: 50px;" maxlength="3">
			<span id="odb_miasto">Kod pocztowy</span>
		</p>
		<p><input type="text" name="odb_email" value="{mis:ODB_EMAIL}Adres e-mail{/}{set:ODB_EMAIL}{ODB_EMAIL}{/}" onblur="if(this.value=='') this.value='Adres e-mail';" onfocus="if(this.value=='Adres e-mail') this.value='';" style="width: 258px;"></p>
		<p><input type="text" name="odb_email2" value="{mis:ODB_EMAIL2}Powtórz e-mail{/}{set:ODB_EMAIL2}{ODB_EMAIL2}{/}" onblur="if(this.value=='') this.value='Powtórz e-mail';" onfocus="if(this.value=='Powtórz e-mail') this.value='';" style="width: 258px;"></p>
		<p><input type="text" name="odb_telef" value="{mis:ODB_TELEF}Telefon{/}{set:ODB_TELEF}{ODB_TELEF}{/}" onblur="if(this.value=='') this.value='Telefon';" onfocus="if(this.value=='Telefon') this.value='';" style="width: 258px;"></p>
	</div>
	<br style="clear: both;">
{/mask}