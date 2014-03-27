{% extends 'layout.php' %}

{% block page_title %}Lista klientów{% endblock %}
{% block content %}
<p class="header">Klienci</p>

  <div class="container">
    <table class="cennik" cellpadding="0" cellspacing="0" border="0">
        <thead>
            <tr>
             <th>Firma</th>
             <th>Imię</th>
              <th>Nazwisko</th>
              <th>Adres</th>
              <th>Miasto</th>
              <th>Email</th>
              <th>Telefon</th>
              <th>Rabat</th>
              <th>Opcje</th>
            </tr>
        </thead>
        <tbody>
          {% for user in users  %}
          <tr>
            <td class="usluga">{{user.company}}</td>
            <td>{{user.name}}</td>
            <td>{{user.lname}}</td>
            <td>{{user.addr}}</td>
            <td>{{user.city}}</td>
            <td>{{user.email}}</td>
            <td>{{user.phone}}</td>
            <td>{{user.discount}}</td>
            <td><a href="/admin/klienci/edytuj/{{user.id_customer}}">Edytuj</a></td>
          </tr>
          {% else %}
          <tr>
            <td colspan="9">Brak klientów</td>
          </tr>            
          {% endfor %}
			</tbody>
    </table>
  </div>


{% endblock %}