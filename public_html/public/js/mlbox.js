// v1.5a / www.airmedia.pl / licencja CC
var mlbox_idir='img/';
var mlbox_docs=Array('.php3','.php4','.php5','.php6','.php','.htm','.html','.txt','.py','.pyc','.asp','.aspx');
var mlbox_mld=document;
var mlbox_mlbg,mlbox_mlpg,mlbox_mlct,mlbox_mlim,mlbox_okpr,mlbox_elem;
var ms6=false;
/*@cc_on @*/
/*@if(@_jscript_version>=5 && @_jscript_version<=5.6) ms6=true;
@end @*/
var mlbox_chrome=navigator.userAgent.toLowerCase().indexOf('chrome')>-1;
AttachEvent(window,'load',initml,false);
function initml(){
 for(var i=0;i<mlbox_mld.links.length;i++)
	if(mlbox_mld.links[i].className && mlbox_mld.links[i].className.match(/(^|\s)mlbox($|\[|\s)/) && mlbox_mld.links[i].onclick==null) mlbox_mld.links[i].onclick=mlbox;
 mlbox_mlpg=mlbox_mld.createElement('img');
 mlbox_mlpg.setAttribute('src',mlbox_idir+'ml_load.gif');
 with(mlbox_mlpg.style){
	position='absolute';
	backgroundColor='white';
	display='none';
	zIndex='998';}
 mlbox_mld.body.appendChild(mlbox_mlpg);
 mlbox_mlpg.onclick=mldie;
}

function mlbox(){
 if(mlbox_elem) return!1;
 var bd=mlbox_mld.body;
 if(ms6){
	var s=mlbox_mld.getElementsByTagName('select');
	for(i=0;i<s.length;i++)s[i].style.visibility='hidden';
 }
 mlbox_elem=this;

 var mh=DocumentHeight();
 if(WindowHeight()>mh) mh=WindowHeight();
 mlbox_mlbg=mlbox_mld.createElement('div');
 with(mlbox_mlbg.style){
	top='0';
	left='0';
	position='absolute';
	width=DocumentWidth()+'px';
	height=mh+'px';
	backgroundColor='black';
	opacity='0.7';
	filter='alpha(opacity=70)';
	zIndex='997';}
 bd.insertBefore(mlbox_mlbg,mlbox_mlpg);

 with(mlbox_mlpg.style){
	top=(Math.round((WindowHeight()-mlbox_mlpg.offsetHeight-40)/2)+ScrollTop())+'px';
	left=(Math.round((WindowWidth()-mlbox_mlpg.offsetWidth-40)/2)+ScrollLeft())+'px';
	padding='20px';
	display='block';}

 ss=this.href.toLowerCase();
 fnd=false;
 for(i=0;i<mlbox_docs.length;i++) if(ss.indexOf(mlbox_docs[i])>=1){fnd=true;break;}
 if(fnd)
 {
  try{xmlhttp=new ActiveXObject('Msxml2.XMLHTTP');} catch(e1){
   try{xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');} catch(e2){xmlhttp=null;}}
  if(!xmlhttp){if(typeof XMLHttpRequest!='undefined'){xmlhttp=new XMLHttpRequest();}}
  if(xmlhttp)
  {
   sst='200';
   if(this.href.indexOf('file:/')>-1) sst='0';
   xmlhttp.open('GET',this.href,true);
   xmlhttp.onreadystatechange=function(){
	if(xmlhttp.readyState==4){
	 mlbox_mlim=mlbox_mld.createElement('div');
	 if(xmlhttp.status==sst) mlbox_mlim.innerHTML=xmlhttp.responseText;
	  else mlbox_mlim.innerHTML='Error! Status='+xmlhttp.status;
	 mlshow();
	}}
   xmlhttp.send(this.href);
  }
 }
  else
 {
  mlbox_mlim=mlbox_mld.createElement('img');
  if(mlbox_chrome) mlbox_mlim.setAttribute('src',this.href+='?t='+new Date().getTime()); else mlbox_mlim.setAttribute('src',this.href);
  mlbox_mlim.onclick=mldie;
  if(mlbox_mlim.complete) mlshow(); else mlbox_mlim.onload=mlshow;
 }
 return!1;
}

function mlshow()
{
 if(mlbox_elem && mlbox_mlim){
  mlbox_mlbg.onclick=mldie;
  mlbox_mlct=mlbox_mld.createElement("div");
  with(mlbox_mlct.style){
	position='fixed';
	backgroundColor='white';
	padding='20px';
	visibility='hidden';
	zIndex='999';
  }
  if(ms6) mlbox_mlct.style.position='absolute';
  
  mlbox_mld.body.insertBefore(mlbox_mlct,mlbox_mlpg);
  mlbox_mlim.style.cursor='pointer';
  mlbox_mlct.appendChild(mlbox_mlim);
  var tmx=Math.round((WindowHeight()-mlbox_mlct.offsetHeight)/2);
  if(ms6)tmx+=ScrollTop();
  if(tmx<0 && !ms6) {mlbox_mlct.style.position='absolute';if(ScrollTop()>Math.abs(tmx))tmx+=ScrollTop();else tmx=0;}
  mlbox_mlct.style.top=tmx+'px';
  var lmx=Math.round((WindowWidth()-mlbox_mlct.offsetWidth)/2)+ScrollLeft();
  if(lmx<0){lmx=0;mlbox_mlct.style.position='absolute';}
  mlbox_mlct.style.left=lmx+'px';

  var mh=DocumentHeight();
  if(WindowHeight()>mh) mh=WindowHeight();
  mlbox_mlbg.style.height=mh+'px';
  mlbox_mlbg.style.width=WindowWidth()+'px';

  if(mlbox_elem.getAttribute('title') && mlbox_elem.getAttribute('title').length>0){
   var o=mlbox_mld.createElement('p');
   with(o.style){
	margin='0';
	paddingTop='6px';
	color='black';
	lineHeight='135%';
   }
   t=mlbox_mld.createTextNode(mlbox_elem.getAttribute('title'));
   o.appendChild(t);
   mlbox_mlct.style.paddingBottom='10px';
   mlbox_mlct.appendChild(o);
   mlbox_mlct.style.top=(tmx-Math.round(o.offsetHeight/2)+5)+'px';
  }

  var s=mlbox_mld.createElement('img');
  with(s.style){
	display='block';
	position='absolute';
	top='1px';
	right='1px';
	width='16px';
	height='16px';
	cursor='pointer';
  }
  s.setAttribute('src',mlbox_idir+'ml_close.gif');
  s.setAttribute('alt','Klawisz ESC zamyka okno');
  s.onclick=function(){mldie()}
  mlbox_mlct.appendChild(s);
  mlbox_mlct.style.visibility='';
  mlbox_mlpg.style.display='none';
  okpr=mlbox_mld.onkeypress;
  mlbox_mld.onkeypress=function(e){if(!e)keycode=event.keyCode;else keycode=e.which;if(keycode==27)mldie();}

  if(mlbox_elem.className.indexOf('[')!=-1 && mlbox_elem.className.indexOf(']')!=-1){
   var g=mlbox_elem.className.replace(/^(.*)\[/,'').replace(/\](.*)?$/,'');
   var prev_obj=null;
   var next_obj=null;
   var tmp=false;
   var nxt=false;
   var reg=new RegExp('(^|\\s)mlbox\\['+g+'\\]($|\\s)','');
   for(var i=0;i<mlbox_mld.links.length;i++)
	if(mlbox_mld.links[i].className && mlbox_mld.links[i].className.match(reg))
	{
	 if(prev_obj!=null) nxt=true;
	 if(mlbox_mld.links[i]==mlbox_elem && prev_obj==null) prev_obj=tmp;
	 tmp=mlbox_mld.links[i];
	 if(nxt && mlbox_mld.links[i]!=mlbox_elem){next_obj=mlbox_mld.links[i]; break;}
	}
	w=Math.round(mlbox_mlim.offsetWidth/2-10);
	if(mlbox_mlim.nodeName.toLowerCase()=='div') w=45;
   if(prev_obj!=false && prev_obj!=null){
	var s=mlbox_mld.createElement('div');
	with(s.style){
	 position='absolute';
	 top='20px';
	 left='20px';
	 width=w+'px';
	 height=mlbox_mlim.offsetHeight+'px';
	 backgroundImage='url('+mlbox_idir+'ml_prev.gif)';
	 backgroundRepeat='no-repeat';
	 backgroundPosition='-1000px -1000px';
	 cursor='pointer';
	}
	s.onmouseover=function(){this.style.backgroundPosition='center left';}
	s.onmouseout=function(){this.style.backgroundPosition='-1000px -1000px';}
	s.onclick=function(){mlbox_mlbg.onclick=null;mlchg(prev_obj);}
	s.onmousedown=function(){return!1;}
	s.onselectstart=function(){return!1;}
	mlbox_mlct.appendChild(s);
   }
   if(next_obj!=null){
	var s=mlbox_mld.createElement('div');
	with(s.style){
	 position='absolute';
	 top='20px';
	 right='20px';
	 width=Math.round(mlbox_mlim.offsetWidth/2-10)+'px';
	 height=mlbox_mlim.offsetHeight+'px';
	 backgroundImage='url('+mlbox_idir+'ml_next.gif)';
	 backgroundRepeat='no-repeat';
	 backgroundPosition='-1000px -1000px';
	 cursor='pointer';
	}
	s.onmouseover=function(){this.style.backgroundPosition='center right';}
	s.onmouseout=function(){this.style.backgroundPosition='-1000px -1000px';}
    s.onclick=function(){mlbox_mlbg.onclick=null;mlchg(next_obj);}
	s.onmousedown=function(){return!1;}
	s.onselectstart=function(){return!1;}
    mlbox_mlct.appendChild(s);
   }
   if((prev_obj!=false && prev_obj!=null)||(next_obj!=null)){
	mlbox_mlim.onclick=null;
	mlbox_mlim.style.cursor='default';
  }}
 }
 return!1;
}

function mlchg(obj){
 var bd=mlbox_mld.body;
 if(mlbox_mlct){bd.removeChild(mlbox_mlct); mlbox_mlct=null; mlbox_mlim=null;}
 mlbox_elem=obj;
 mlbox_mlpg.style.top=(Math.round((WindowHeight()-mlbox_mlpg.offsetHeight-40)/2)+ScrollTop())+'px';
 mlbox_mlpg.style.left=(Math.round((WindowWidth()-mlbox_mlpg.offsetWidth-40)/2)+ScrollLeft())+'px';
 mlbox_mlpg.style.padding='20px';
 mlbox_mlpg.style.display='block';
 ss=obj.href.toLowerCase();
 fnd=false;
 for(i=0;i<mlbox_docs.length;i++) if(ss.indexOf(mlbox_docs[i])>=1){fnd=true;break;}
 if(fnd)
 {
  try{xmlhttp=new ActiveXObject('Msxml2.XMLHTTP');} catch(e1){
   try{xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');} catch(e2){xmlhttp=null;}}
  if(!xmlhttp){if(typeof XMLHttpRequest!='undefined'){xmlhttp=new XMLHttpRequest();}}
  if(xmlhttp)
  {
   sst='200';
   if(obj.href.indexOf('file:/')>-1) sst='0';
   xmlhttp.open('GET',obj.href,true);
   xmlhttp.onreadystatechange=function(){
	if(xmlhttp.readyState==4){
	 mlbox_mlim=mlbox_mld.createElement('div');
	 if(xmlhttp.status==sst) mlbox_mlim.innerHTML=xmlhttp.responseText;
	  else mlbox_mlim.innerHTML='Error! Status='+xmlhttp.status;
	 mlshow();
	}}
   xmlhttp.send(obj.href);
  }
 }
  else
 {
  mlbox_mlim=mlbox_mld.createElement('img');
  if(mlbox_chrome) mlbox_mlim.setAttribute('src',obj.href+='?t='+new Date().getTime()); else mlbox_mlim.setAttribute('src',obj.href);
  mlbox_mlim.onclick=mldie;
  if(mlbox_mlim.complete) mlshow(); else mlbox_mlim.onload=mlshow;
 }
 return!1;
}

function mldie(){
 if(mlbox_mlbg){
  var bd=mlbox_mld.body;
  bd.removeChild(mlbox_mlbg);
  mlbox_mlbg=null;
  if(mlbox_mlct){bd.removeChild(mlbox_mlct);mlbox_mlct=null;mlbox_mlim=null;}
  if(mlbox_mlim){bd.removeChild(mlbox_mlim);mlbox_mlim=null;}
  if(ms6){
	var s=mlbox_mld.getElementsByTagName('select');
	for(var i=0;i<s.length;i++)s[i].style.visibility='';
  }
  mlbox_mlpg.style.display='none';
  mlbox_elem=null;
 }
}

function WindowHeight(){var z;if(mlbox_mld.compatMode.toLowerCase().indexOf('back')==-1)z=mlbox_mld.documentElement.clientHeight; else z=mlbox_mld.body.clientHeight; return z;}
function WindowWidth(){var z;if(mlbox_mld.compatMode.toLowerCase().indexOf('back')==-1)z=mlbox_mld.documentElement.clientWidth; else z=mlbox_mld.body.clientWidth; return z;}
function ScrollTop(){var z;if(self.pageYOffset)z=self.pageYOffset; else if(mlbox_mld.documentElement && mlbox_mld.documentElement.scrollTop)z=mlbox_mld.documentElement.scrollTop; else if(mlbox_mld.body) z=mlbox_mld.body.scrollTop; return z;}
function ScrollLeft(){var z;if(self.pageXOffset)z=self.pageXOffset; else if(mlbox_mld.documentElement && mlbox_mld.documentElement.scrollLeft)z=mlbox_mld.documentElement.scrollLeft; else if(mlbox_mld.body) z=mlbox_mld.body.scrollLeft; return z;}
function DocumentHeight(){var z;if(mlbox_mld.compatMode.toLowerCase().indexOf('back')==-1)z=mlbox_mld.documentElement.scrollHeight; else z=mlbox_mld.body.scrollHeight; return z;}
function DocumentWidth(){var z;if(mlbox_mld.compatMode.toLowerCase().indexOf('back')==-1)z=mlbox_mld.documentElement.scrollWidth; else z=mlbox_mld.body.scrollWidth; return z;}
function AttachEvent(obj,evt,fnc,useCapture){
 if(!useCapture) useCapture=false;
 if(obj.addEventListener){
  obj.addEventListener(evt,fnc,useCapture);
  return true;
 } else if(obj.attachEvent) return obj.attachEvent('on'+evt,fnc);
  else{
   MyAttachEvent(obj,evt,fnc);
   obj['on'+evt]=function(){MyFireEvent(obj,evt)};
  }
 return true;
}
function MyAttachEvent(obj,evt,fnc){
 if(!obj.myEvents)obj.myEvents={};
 if(!obj.myEvents[evt])obj.myEvents[evt]=[];
 var evts=obj.myEvents[evt];
 evts[evts.length]=fnc;
}
function MyFireEvent(obj,evt){
 if(!obj || !obj.myEvents || !obj.myEvents[evt]) return;
 var evts=obj.myEvents[evt];
 for(var i=0,len=evts.length;i<len;i++) evts[i]();
}