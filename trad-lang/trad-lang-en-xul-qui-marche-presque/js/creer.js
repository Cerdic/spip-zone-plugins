

function load_creer()
{
}

function comm_creer(etape, cmd) {

  var lgi = document.getElementById('lgi').value;
  var lgo = document.getElementById('lgo').value;
  var lgc = document.getElementById('lgc').value;
  var mod = document.getElementById('mod').value;
  var nouv = document.getElementById('nouv').value;

  var scrap = getScrap();

  if (cmd == 'valider')
    {
      window.opener.location.href=SERVER_URL+"?etape="+etape+"&lgi="+lgi+"&mod="+
   	mod+"&lgo="+lgo+"&lgc="+lgc+"&cmd="+cmd+"&nouv="+nouv;
      window.close();
    }
  else
    window.close();
}



