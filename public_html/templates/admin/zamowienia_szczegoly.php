{% extends 'layout.php' %}

{% block page_title %}Szczegóły{% endblock %}
{% block content %}
	<p class="header">Zamówienia</p>

  <div class="container">
    <table class="cennik" cellpadding="0" cellspacing="0" border="0">
        <thead>
            <tr>
             <th>Numer trackingowy</th>
             <th>Data złożenia zamówienia</th>
             <th>Pobranie</th>
             <th>Ubezp.</th>
             <th>Zwrot dok.</th>
             <th>Cena</th>
             <th>Typ wysyłki</th>
             <th>Forma płatności</th>
             <th>Data zwrotu</th>
            </tr>
        </thead>
        <tbody>
          <tr>
            <td class="usluga">{{order.tracking}}</td>
            <td>{{order.date}}</td>
            <td>{% if order.COD %}{{order.COD}}{% else %}brak{%endif%}</td>
            <td>{% if order.Insurance %}{{order.Insurance}}{% else %}brak{%endif%}</td>
            <td>{% if order.ROD %}tak{% else %}nie{%endif%}</td>
            <td>{{order.amount}}</td>
            <td>{% if order.delivery==1 %}Paczka{% elseif delivery==2 %}Koperta{%endif%}</td>
            <td>{% if order.payment == 1%}Prepaid{% elseif payment == 2%}Pobranie{% else %}Błąd{% endif %}</td>
            <td>{% if order.payment == 2%}{{order.date_repay}}{% endif %}</td>
          </tr>
 	</tbody>
</table>
      <p>Miejsce docelowe</p>
      <table class="cennik" cellpadding="0" cellspacing="0" border="0" style="width: auto;">
          <tbody>
              <tr>
                  <td>Firma</td><td>{{order.company}}</td>
              </tr>
              <tr>
                  <td>Imię</td><td>{{order.name}}</td>
              </tr>
              <tr>                  
                  <td>Nazwisko</td><td>{{order.lname}}</td>
              </tr>
              <tr>
                  <td>Adres</td><td>{{order.addr}}</td>
              </tr>
              <tr>
                  <td>Miasto</td><td>{{order.city}}</td>
              </tr>
              <tr>
                  <td>Kod pocztowy</td><td>{{order.zip}}</td>
              </tr>
              <tr>
                  <td>Telefon</td><td>{{order.phone}}</td>
              </tr>
          </tbody>
      </table>
        <a href="/admin/zamowienia" class="back">Zamówienia</a>
  </div>


{% endblock %}