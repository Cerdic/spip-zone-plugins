if (window.XMLHttpRequest) { 
    xmlHttp = new XMLHttpRequest();
} else if (window.ActiveXObject) { 
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
}
function callServer(url) {
  xmlHttp.open("GET", url, true);
  xmlHttp.onreadystatechange = updatePage;
  xmlHttp.send(null);
}
function updatePage() {
  if (xmlHttp.readyState == 4) {
    var response = xmlHttp.responseText;
    var fin="fin";
    if(response.indexOf(fin) == 0){
    document.getElementById("meleuse").innerHTML = "<p align='center'><strong>100%</strong>";
    setTimeout("document.location.href = '?exec=spip_listes'",5000);
    }else{
    document.getElementById("meleuse").innerHTML = response;
    setTimeout("callServer('?exec=autocron')",15000);
    }
  }
}
callServer("?exec=autocron");
