// $LastChangedRevision: 15805 $
// $LastChangedBy: paladin@quesaco.org $
// $LastChangedDate: 2007-10-06 07:48:57 +0200 (sam., 06 oct. 2007) $

// ne semble plus utilisé (CP-20071006)

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
    setTimeout("document.location.href = '?exec=spiplistes_liste_gerer'",5000);
    }else{
    document.getElementById("meleuse").innerHTML = response;
    setTimeout("callServer('?exec=spiplistes_autocron')",15000);
    }
  }
}
callServer("?exec=spiplistes_autocron");
