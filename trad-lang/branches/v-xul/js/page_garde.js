

function load_page_garde() {

  var lgi = getParam("lgi");
  document.getElementById('lgi').value = lgi;

  var lgc = getParam("lgc");
  document.getElementById('lgc').value = lgc;

  var lgo = getParam("lgo");
  document.getElementById('lgo').value = lgo;

  var mod = getParam("mod");
  document.getElementById('mod').value = mod;
}


function comm_page_garde(etape) {

  var lgi = document.getElementById('lgi').value;
  var mod = document.getElementById('mod').value;
  var lgo = document.getElementById('lgo').value;
  var lgc = document.getElementById('lgc').value;
  var nlgc = document.getElementById('nlgc').value;
	
  var scrap = getScrap();

  location.href=SERVER_URL+"?etape="+etape+"&lgi="+lgi+"&mod="+mod+"&lgo="+lgo+"&lgc="+lgc+"&nlgc="+nlgc+"&errscrap="+scrap;
}

