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
             <th>Cena</th>
             <th>Typ wysyłki</th>
             <th>Forma płatności</th>
             <th>Status UPS</th>             
             <th>Opcje</th>
             <th>Anuluj</th>
            </tr>
        </thead>
        <tbody>
          {% for order in orders  %}
          <tr class="tr{{order.id}}">
            <td class="usluga">
                {{order.tracking}}
                <input type="hidden" value="{{order.id}}" id="orderId">
            </td>
            <td>{{order.date}}</td>
            <td>{% if order.COD %}{{order.COD}}{% else %}brak{%endif%}</td>
            <td>{% if order.Insurance %}{{order.Insurance}}{% else %}brak{%endif%}</td>
            <td>{{order.amount}}</td>
            <td>{% if order.delivery==1 %}Paczka{% elseif order.delivery==2 %}Koperta{%endif%}</td>
            <td>{% if order.payment == 1%}Prepaid{% elseif order.payment == 2%}<a href="/admin/zamowienia/pobranie/{{order.id}}">Pobranie</a>{% else %}Błąd{% endif %}</td>
            <td class="status">{{order.status}}</td>            
            <td><a href="/user/zamowienia/dostawa/{{order.id}}">Szczegóły</a></td>
            <td>{% if order.code=='M' %}<a class="button cancel confirmLink" rel="cancel" href="">Anuluj</a>{% endif %}</td>
          </tr>
          {% else %}
          <tr>
            <td colspan="7">Brak zamówień</td>
          </tr>            
          {% endfor %}
			</tbody>
		</table>

  </div>
<div id="dialog" title="Anulowanie przesyłki">
  Czy napewno chcesz anulować przesyłkę ?
</div>

{% endblock %}