{mask:main}
	<div class="mainleft">
		<h3>Rodzaj</h3>
		<p>
			<label><input type="radio" name="rodzaj" onclick="qwery('#ludzik')[0].src = 'img/pict_1.png';" value="1"{set:RODZAJ:1} checked{/}> Paczka</label>
			<label><input type="radio" name="rodzaj" onclick="qwery('#ludzik')[0].src = 'img/pict_2.png';" value="2"{set:RODZAJ:2} checked{/}> Koperta</label>
			<label><input type="radio" name="rodzaj" onclick="qwery('#ludzik')[0].src = 'img/pict_3.png';" value="3"{set:RODZAJ:3} checked{/}> Paleta</label>
		</p>
		<h3 style="padding-top: 10px;">Rozmiar</h3>
		<p><label><span style="display: inline-block; width: 100px; vertical-align: middle;">Waga przesyłki:</span> <input type="text" name="waga" value="{WAGA}" style="text-align: right; width: 85px;"> kg</label></p>
		<p class="wymiar"><span>Wymiary:</span>
			<label><input type="text" name="dlu" title="Długość" value="{DLU}"> x </label>
			<label><input type="text" name="szer" title="Szerokość" value="{SZER}"> x </label>
			<label><input type="text" name="wys" title="Wysokość" value="{WYS}"> cm</label>
			<br>
			<var><span>dł.</span> &nbsp; <span>szer.</span> &nbsp; <span>wys.</span></var>
		</p>
		<p style="padding-top: 1px;">
			<label><input type="checkbox" name="niestandardowa" value="1"{set:NIESTAND:1} checked{/}> Niestandardowa przesyłka</label>
			<img class="help" src="img/help.gif" alt="Pomoc" onmouseover="ddrivetip('Przesyłka o nietypowym kształcie.<br>Opłata naliczana zgodnie z cennikiem kuriera.');" onmouseout="hideddrivetip();">
		</p>
	</div>
	<div class="mainright">
		<h3>Dokąd</h3>
		<select name="kraj" id="kraj" onchange="if(this.value!=177){ alert('Wysyłka do Czech:\nusługa w trakcie przygotowywania.'); this.value=177; this.selectedIndex=1; return!1; }">
			<option value="60"{set:KRAJ:60} selected{/}>Republika Czeska</option>
			<option value="177"{set:KRAJ:177} selected{/}>Polska</option>
		</select>
		<h3>Usługi w cenie</h3>
		<p><span>K-EX</span>
			<var>
				<label><input type="checkbox" name="kex_ubezp1" value="1" onclick="return!1;"{set:KEX_UBEZP1:1} checked{/}> Darmowe ubezpieczenie do 100 zł</label>
				<br>
				<label><input type="checkbox" name="kex_gwar1" value="1" onclick="return!1;"{set:KEX_GWAR1} checked{/}> Gwarancja dostarczenia na następny dzień</label>
			</var>
		</p>
		<p style="padding-top: 10px;"><span>UPS</span>
			<var>
				<label><input type="checkbox" name="kex_ubezp1" value="1" onclick="return!1;"{set:UPS_UBEZP1} checked{/}> Darmowe ubezpieczenie do 323 zł</label>
			</var>
		</p>
	</div>
	<br style="clear: both;">
{/mask}