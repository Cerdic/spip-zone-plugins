

function load_administrer()
{
  var lti = document.getElementById('lt');
  var idxf = document.getElementById('idxf').value;

  lti.scrollToIndex(idxf);
  lti.focus();

  var lgc = getParam("lgc");
  document.getElementById('lgc').value = lgc;

  var lgo = getParam("lgo");
  document.getElementById('lgo').value = lgo;

  var mod = getParam("mod");
  document.getElementById('mod').value = mod;

  var lgi = getParam("lgi");
  document.getElementById('lgi').value = lgi;
}


function comm_administrer(etape, cmd, mess) {

  var lgi = document.getElementById('lgi').value;
  var mod = document.getElementById('mod').value;
  var flt = document.getElementById('flt').value;
  var nouv = document.getElementById('nouv').value;
  var lt = document.getElementById('lt').value;
  var val = document.getElementById('val').value;

  var lti = document.getElementById('lt');
  var idxf = lti.getIndexOfFirstVisibleRow();

  var scrap = getScrap();

  if (cmd == 'commenter')
    {  
      window.open(SERVER_URL+'?etape=commenter&mod='+mod+'&lgi='+lgi+'&lt='+lt+'&cmd=admin', 
	'', 'dialog,modal,centerscreen,width=400px,height=200px');
    }
  else if (cmd == 'effacer')
    {
       if (confirm(mess))
        location.href=SERVER_URL+"?etape=administrer&lgi="+lgi+"&mod="+
   	  mod+"&errscrap="+scrap+"&flt="+flt+"&lt="+lt+"&nouv="+nouv+
	  "&val="+val+"&idxf="+idxf+"&cmd=supprimer";
    }
  else if (cmd == 'nouveau')
    {
      window.open(SERVER_URL+"?etape=creer&lgi="+lgi+"&mod="+
   	mod+"&errscrap="+scrap+"&flt="+flt+"&lt="+lt+"&nouv="+nouv+
	"&val="+val+"&idxf="+idxf+"&cmd="+cmd,
	'', 'dialog,modal,centerscreen,width=300,height=110');
    }
  else
    {
      location.href=SERVER_URL+"?etape="+etape+"&lgi="+lgi+"&mod="+
   	mod+"&errscrap="+scrap+"&flt="+flt+"&lt="+lt+"&nouv="+nouv+
	"&val="+val+"&idxf="+idxf+"&cmd="+cmd;
    }

}

