

function load_chercher()
{
}

function comm_chercher(etape, cmd) {

  var lgi = document.getElementById('lgi').value;
  var lgr = document.getElementById('lgr').value;
  var lgc = document.getElementById('lgc').value;
  var mod = document.getElementById('mod').value;
  var rech = document.getElementById('rech').value;

  var scrap = getScrap();

  if (cmd == 'annuler')
    {  
      window.close();
    }
  else 
    {
      location.href = SERVER_URL+"?etape=chercher&lgi="+lgi+"&mod="+mod+'&lgc='+
	lgc+'&lgr='+lgr+'&rech='+rech+
	"&errscrap="+scrap+"&lgr="+lgr;
    }
}

