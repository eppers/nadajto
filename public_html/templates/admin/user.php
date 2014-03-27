{% extends 'layout.php' %}

{% block page_title %}Edytuj klienta{% endblock %}
{% block content %}
<p class="header">Klient</p>

    <div class="container">
        <form action="/admin/klienci/edytuj" method="POST" id="user-form">
            <div class="mainleft"> 
                <input type="hidden" name="id" value="{{user.id}}">
                <p><label for="company">Firma</label><input type="text" name="company" placeholder="Firma" value="{{user.company}}" style="width: 258px;"></p>
                <p><label for="name">Imię</label><input type="text" name="name" placeholder="Imię" value="{{user.name}}" style="width: 258px;"></p>
                <p style="margin-bottom: 30px;"><label for="lname">Nazwisko</label><input type="text" name="lname" placeholder="Nazwisko" value="{{user.lname}}" style="width: 258px;"></p>
                <p><label for="addr">Adres</label><input type="text" name="addr" placeholder="Adres" value="{{user.addr}}" style="width: 258px;"></p>
                <p><label for="city">Miasto</label><input type="text" name="city" placeholder="Miasto" value="{{user.city}}" style="width: 258px;"></p>
                <p><label for="zip">Kod pocztowy</label><input type="text" name="zip" placeholder="Kod pocztowy" value="{{user.zip}}" style="width: 258px;"></p>
            </div>
            <div class="mainright" style="width: 290px;">
                <p><label for="email">Email</label><input type="text" name="email" placeholder="Email" value="{{user.email}}" style="width: 258px;"></p>
                <p style="margin-bottom:30px;"><label for="phone">Telefon</label><input type="text" name="phone" placeholder="Telefon" value="{{user.phone}}" style="width: 258px;"></p>

                <p><label for="discount">Rabat</label><input type="text" name="discount" placeholder="Rabat" value="{{user.discount}}" style="width: 258px;"></p>
            </div>  
            <div class="price-container" style="clear: both; margin-left: 40px;"> 
                <button type="button" class="dark" id="btn-submit" >Wyślij</button><span class="ajax-loader"></span>
            </div>
        </form>
    </div>
<script>
$j('#btn-submit').click(function(){
    $j('#user-form').submit();
})
</script>

{% endblock %}