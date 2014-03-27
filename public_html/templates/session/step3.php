{mask:main}
	<div class="mainleft" style="width: 360px;">
		<h3>Data nadania przesyłki</h3>
		<p>
			<input type="text" name="data_nad" value="{mis:DATA_NAD}Możliwie najszybciej{/}{set:DATA_NAD}{DATA_NAD}{/}" onblur="if(this.value=='') this.value='Możliwie najszybciej';" onfocus="if(this.value=='Możliwie najszybciej') this.value='';" style="width: 115px;">
			<img alt="Data…" src="img/calend.png" class="kalendarz">
		</p>
		<p class="mopts">
			<label><input type="checkbox" name="dodat_ubezp" value="1"{set:DODAT_UBEZP} checked{/}> Dodatkowe ubezpieczenie</label>
			<input type="text" name="dod_ubez_kw" value="{DOD_UBEZ_KW}" style="text-align: right;"> zł
		</p>
		<p class="mopts">
			<label><input type="checkbox" name="przes_pobr" value="1"{set:PRZES_POBR} checked{/}> Przesyłka pobraniowa</label>
			<input type="text" name="przes_pobr_kw" value="{PRZES_POBR_KW}" style="text-align: right;"> zł
		</p>
		<p class="mopts">
			<label><input type="checkbox" name="rod" value="1"{set:ROD} checked{/}> Zwrot potwierdzonych dokumentów (ROD)</label>
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<label><input type="checkbox" name="rod_expr" value="1"{set:ROD_EXPR} checked{/}> Express (tylko K-EX)</label>
		</p>
	</div>
	<div class="mainright" style="color: #757575; line-height: 150%;">
		<p><b>Odbiory paczek odbywają się<br>w godziach od 10 do 18.</b><br>Nie ma możliwości ustalenia<br>konkretnej godziny odbioru.</p>
		<p><b>Dodatkowe ubezpiecznie:</b>
			<br>UPS - maksymalna wysokość<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ubezpieczenia wynosi 50 000 zł,
			<br>K-EX - maksymalna wysokość<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ubezpieczenia wynosi 100 000 zł.
		</p>
		<p><b>Zwrot potwierdzonych dokumentów (ROD):</b>
			<br>(dołączonych do listu przewozowego)
			<br>UPS - do 5 dni roboczych
			<br>K-EX - do 12 lub 5 (express) dni roboczych
		</p>
		
	</div>
	<br style="clear: both;">
{/mask}