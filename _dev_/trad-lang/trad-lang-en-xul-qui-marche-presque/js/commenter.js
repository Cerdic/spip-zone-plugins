

function load_commenter()
{
}


function comm_commenter(etape, cmd, cmd2) {

  var lgi = document.getElementById('lgi').value;
  var lgc = document.getElementById('lgc').value;
  var lt = document.getElementById('lt').value;
  var comm = document.getElementById('comm').value;
  var mod = document.getElementById('mod').value;

  var scrap = getScrap();

  if (cmd == 'annuler')
    {  
      window.close();
    }
  else 
    {
      location.href = SERVER_URL+"?etape=commenter&lgi="+lgi+"&mod="+mod+
	"&errscrap="+scrap+"&lt="+lt+"&comm="+comm+"&cmd="+cmd+"&cmd2="+cmd2+"&lgc="+lgc;
      window.close();
      window.opener.location.reload();
    }
}

