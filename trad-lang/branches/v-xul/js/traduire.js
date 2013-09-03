

function load_traduire() {
  var lti = document.getElementById('lt');
  var idxf = document.getElementById('idxf').value;

  var lgi = getParam("lgi");
  document.getElementById('lgi').value = lgi;

  var lgc = getParam("lgc");
  document.getElementById('lgc').value = lgc;

  var lgo = getParam("lgo");
  document.getElementById('lgo').value = lgo;

  var mod = getParam("mod");
  document.getElementById('mod').value = mod;

  lti.scrollToIndex(idxf);
  lti.focus();
}


function comm_traduire(etape, cmd) {

  var lgi = document.getElementById('lgi').value;
  var mod = document.getElementById('mod').value;
  var lgo = document.getElementById('lgo').value;
  var lgc = document.getElementById('lgc').value;

  var tm = document.getElementById('tm').value;
  var dt = document.getElementById('dt').value;
  var flt = document.getElementById('flt').value;
  var cr = document.getElementById('cr').value;
  var to = document.getElementById('to').value;
  var lt = document.getElementById('lt').value;
	
  var ts = "off"; 
  if (document.getElementById('ts').checked == true)
    ts = "on";

  var lti = document.getElementById('lt');
  var idxf = lti.getIndexOfFirstVisibleRow();

  var scrap = getScrap();

  if (cmd == "traduction")
    {
      window.open(SERVER_URL+'?etape=traduction&mod='+mod+'&lgi='+lgi+'&lgo='+lgo+'&lgc='+
	lgc+'&errscrap='+scrap+'&tm='+tm+'&dt='+dt+'&flt='+flt+'&cr='+cr+'&ts='+ts+'&to='+
	to+'&lt='+lt+'&idxf='+idxf, '', 'dialog,modal,centerscreen,width=500,height=300');     
    }
  else
    {
      location.href=SERVER_URL+"?etape="+etape+"&lgi="+lgi+"&mod="+mod+'&idxf='+idxf+"&lgo="+lgo+
        "&lgc="+lgc+"&errscrap="+scrap+"&tm="+tm+"&dt="+dt+"&flt="+flt+"&cr="+cr+"&ts="+ts+"&to="+to+"&lt="+lt;
    }
}

