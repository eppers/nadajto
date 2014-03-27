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
              <th>Cena</th>
              <th>Kurier</th>
              <th>Typ wysyłki</th>
              <th><a href="/admin/zamowienia/pobranie" title="Pobranie">Forma płatności</a></th>
              <th>Data zwrotu</th>
              <th>Status UPS</th>
              <th>Opcje</th>
              <th>Anuluj</th>  
              <th>Wyślij</th> 
            </tr>
        </thead>
        <tbody>
          {% for order in orders  %}
          <tr>
            <td class="usluga">
                {{order.tracking}}
                <input type="hidden" value="{{order.id}}" id="orderId">
            </td>
            <td>{{order.date}}</td>
            <td>{{order.amount}}</td>
            <td>{{order.courier}}</td>
            <td>{% if order.delivery==1 %}Paczka{% elseif order.delivery==2 %}Koperta{%endif%}</td>
            <td>{% if order.payment == 1%}Prepaid{% elseif order.payment == 2%}<a href="/admin/zamowienia/pobranie/{{order.id}}">Pobranie</a>{% else %}Błąd{% endif %}</td>
            <td>{% if order.payment == 2%}{{order.date_repay}}{% endif %}</td>
            <td class="status">{{order.status}}</td>  
            <td><a href="/admin/zamowienia/szczegoly/{{order.id}}">Szczegóły</a></td>            
            <td>{% if order.code=='M' %}<a class="button cancel confirmLink" rel="cancel" href="">Anuluj</a>{% endif %}</td>
            <td><form action="/admin/ups/ship" method="post"><input type="hidden" name="id" value="{{order.id}}"><input type="submit" value="wyślij"></form></td>
          </tr>
          {% else %}
          <tr>
            <td colspan="7">Brak zamówień</td>
          </tr>            
          {% endfor %}

			</tbody>
		</table>
        <a href="/admin/zamowienia" class="back">Zamówienia</a>
  </div>
<div id="dialog" title="Anulowanie przesyłki">
  Czy napewno chcesz anulować przesyłkę ?
</div>

{% endblock %}