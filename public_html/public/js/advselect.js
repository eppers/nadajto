var mySelectDown = false;

function styleSelect(id, cls)
{
 var selem = id.getElementsByTagName('select');
 if(selem.length > 0)
  for(var i=0;i<selem.length;i++)
  {
   // box
   var newddbox = document.createElement('span');
   newddbox.className = cls;
   selem[i].parentNode.insertBefore(newddbox, selem[i]);
   // selected value box
   var newddtxt = document.createElement('span');
   newddtxt.onclick = function() {
	   	if(mySelectDown && mySelectDown != this.parentNode.parentNode.parentNode.getElementsByTagName('select')[0].getAttribute('id'))
		{
			document.getElementById(mySelectDown).parentNode.getElementsByTagName('ul')[0].style.display='';
			mySelectDown = false;
		}
   		var tseldd = this.parentNode.getElementsByTagName('ul')[0];
		if(tseldd.style.display == '')
		{
			tseldd.style.display = 'block';
			mySelectDown = this.parentNode.parentNode.parentNode.getElementsByTagName('select')[0].getAttribute('id');
		}
		 else
		{
			tseldd.style.display = '';
			mySelectDown = false;
		}
   		return!1;
	   }
   newddbox.appendChild(newddtxt);
   // button
   var newddbtn = document.createElement('a');
   newddbtn.className = 'ddbtn';
   newddbtn.setAttribute('href', '#');
   newddbtn.onclick = function() {
	   	if(mySelectDown && mySelectDown != this.parentNode.parentNode.parentNode.getElementsByTagName('select')[0].getAttribute('id'))
		{
			document.getElementById(mySelectDown).parentNode.getElementsByTagName('ul')[0].style.display='';
			mySelectDown = false;
		}
   		var tseldd = this.parentNode.getElementsByTagName('ul')[0];
		if(tseldd.style.display == '')
		{
			tseldd.style.display = 'block';
			mySelectDown = this.parentNode.parentNode.parentNode.getElementsByTagName('select')[0].getAttribute('id');
		}
		 else
		{
			tseldd.style.display = '';
			mySelectDown = false;
		}
   		return!1;
	   }
   newddbox.appendChild(newddbtn);
   // list
   var optelem = selem[i].getElementsByTagName('option'); 
   if(optelem.length > 0)
   {
    var nddid = selem[i].getAttribute('id');
    var newddlst = document.createElement('ul');
	for(j=0; j<optelem.length; j++)
	{
	 var newddli = document.createElement('li');
	 newddlst.appendChild(newddli);
	 var newdda = document.createElement('a');
	 newdda.setAttribute('href', '#');
	 newdda.setAttribute('rel', nddid+','+j);
	 newdda.onclick = function() {
		var par = this.getAttribute('rel').split(',', 2);
		document.getElementById(par[0]).selectedIndex = par[1];
		this.parentNode.parentNode.parentNode.getElementsByTagName('span')[0].innerHTML = this.innerHTML;
		this.parentNode.parentNode.style.display='';
		if(document.getElementById(par[0]).onchange) document.getElementById(par[0]).onchange();
		mySelectDown = false;
		return!1;
	   }
	 var txc = optelem[j].innerHTML;
	 if(txc.length < 1) txc = '?';
	 newdda.appendChild(document.createTextNode(txc));
	 if(optelem[j].getAttribute('title'))
	 {
	  if(optelem[j].getAttribute('title').indexOf('.gif') >= 0 || optelem[j].getAttribute('title').indexOf('.png') >= 0)
	  	newdda.innerHTML = '<'+'img style="display:inline-block; vertical-align:middle; margin-right:10px; border:0;" alt="" src="'+optelem[j].getAttribute('title')+'">'+newdda.innerHTML;
	  else
	  	newdda.setAttribute('title', optelem[j].getAttribute('title'));
	 }
	 newddli.appendChild(newdda);
	 if(selem[i].selectedIndex == j)
	 {
	  txc = selem[i].options[selem[i].selectedIndex].innerHTML;
	  if(optelem[j].getAttribute('title') && (optelem[j].getAttribute('title').indexOf('.gif') >= 0 || optelem[j].getAttribute('title').indexOf('.png') >= 0))
	   txc = '<'+'img style="display:inline-block; vertical-align:middle; margin-right:10px; border:0;" alt="" src="'+optelem[j].getAttribute('title')+'">'+txc;
	  newddbox.getElementsByTagName('span')[0].innerHTML = txc;
	 }
	}
    newddbox.appendChild(newddlst);
   }
   // hide select
   selem[i].style.display = 'none';
  }//select's
 return true;
}

function resetSelect(id)
{
 var selem = document.getElementById(id).getElementsByTagName('select');
 if(selem.length > 0)
  for(i=0;i<selem.length;i++)
  {
   mn=selem[i].previousSibling;
   if(mn.nodeType!=1) mn=mn.previousSibling;
   var optelem=selem[i].getElementsByTagName('option'); 
   mn.getElementsByTagName('span')[0].innerHTML=optelem[selem[i].selectedIndex].innerHTML;
  }
 return true;
}

function updateSelect(el)
{
 mn=el.previousSibling;
 if(mn.nodeType!=1) mn=mn.previousSibling;
 mid=el.getAttribute('id');
 lst=mn.getElementsByTagName('li');
 if(lst.length>0)
 {
  ule=mn.getElementsByTagName('ul')[0];
  ule.innerHTML='';
  var optelem=el.getElementsByTagName('option'); 
  if(optelem.length > 0)
  {
   for(j=0;j<optelem.length;j++)
   {
    var newddli = document.createElement('li');
	ule.appendChild(newddli);
	var newdda = document.createElement('a');
	newdda.setAttribute('href', '#');
	newdda.setAttribute('rel', mid+','+j);
	newdda.onclick = function() {
	 	var par = this.getAttribute('rel').split(',', 2);
	 	document.getElementById(par[0]).selectedIndex=par[1];
		this.parentNode.parentNode.parentNode.getElementsByTagName('span')[0].innerHTML=this.innerHTML;
		this.parentNode.parentNode.style.display='';
		if(document.getElementById(par[0]).onchange) document.getElementById(par[0]).onchange(); 
		return!1;
	   }
	var txc=optelem[j].innerHTML;
	if(txc.length<1) txc='?';
	newdda.appendChild(document.createTextNode(txc));
	newddli.appendChild(newdda);
   }
   mn.getElementsByTagName('span')[0].innerHTML=optelem[0].innerHTML;
  }
 }
 return!1;
}
