

function load_traduction()
{
}


function comm_traduction(etape, cmd) {

  var lgi = document.getElementById('lgi').value;
  var mod = document.getElementById('mod').value;
  var lgo = document.getElementById('lgo').value;
  var lgc = document.getElementById('lgc').value;

  var tm = document.getElementById('tm').value;
  var dt = document.getElementById('dt').value;
  var flt = document.getElementById('flt').value;
  var cr = document.getElementById('cr').value;
  var ts = document.getElementById('ts').value;
  var to = document.getElementById('to').value;
  var lt = document.getElementById('lt').value;
  var idxf = document.getElementById('idxf').value;
  var dest = document.getElementById('dest').value;
	
  var scrap = getScrap();

  if (cmd == 'annuler')
    {  
      window.close();
    }
  else if (cmd == 'valider')
    {
      window.opener.location.href=SERVER_URL+"?etape=traduire&cmd=valider&lgi="+lgi+"&mod="+mod+"&lgo="+lgo+
        "&lgc="+lgc+"&errscrap="+scrap+"&tm="+tm+"&dt="+dt+"&flt="+flt+"&cr="+cr+"&ts="+ts+"&to="+to+
	"&lt="+lt+"&dest="+dest+'&idxf='+idxf;
      window.close();
    }
  else if (cmd == 'chercher')
    {
      var tb = document.getElementById('orig');
      var txt = tb.value.substring(tb.selectionStart,tb.selectionEnd);
      var txtok = txt.replace(new RegExp('([\\f\\n\\r\\t\\v ])+', 'g')," ");

      window.open(SERVER_URL+'?etape=chercher&mod='+mod+'&lgi='+lgi+'&lgc='+lgc+'&lgr='+lgo+'&rech='+txtok, 
	'', 'dialog,modal,centerscreen,width=550,height=500');
    }
  else if (cmd == 'commenter')
    {
      window.open(SERVER_URL+'?etape=commenter&mod='+mod+'&lgi='+lgi+'&lt='+lt+'&cmd2=traduc&lgc='+lgc, 
	'', 'dialog,modal,centerscreen,width=400,height=200');
    }
  else 
    {
      window.close();
    }
}

