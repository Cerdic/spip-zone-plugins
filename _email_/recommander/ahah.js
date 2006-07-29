function ahah(url, target, delay) {
  // afficher la roue ajax dans le premier <span> du <div> cible.
  document.getElementById(target).getElementsByTagName('SPAN')[0].innerHTML = '<div style="position:absolute;z-index:10;"><img src="ecrire/img_pack/searching.gif" alt="Waiting..." /></div>';
  if (window.XMLHttpRequest) {
    req = new XMLHttpRequest();
  } else if (window.ActiveXObject) {
    req = new ActiveXObject("Microsoft.XMLHTTP");
  }
  if (req != undefined) {
    req.onreadystatechange = function() {ahahDone(url, target, delay);};
    req.open("GET", url, true);
    req.send("");
  }
}  

function ahahDone(url, target, delay) {
  if (req.readyState == 4) { // only if req is "loaded"
    if (req.status == 200) { // only if "OK"
      document.getElementById(target).innerHTML = req.responseText;
    } else {
      document.getElementById(target).innerHTML="ahah error:\n"+req.statusText;
    }
    if (delay != undefined) {
       setTimeout("ahah(url,target,delay)", delay); // resubmit after delay
	    //server should ALSO delay before responding
    }
  }
}


function ahahform(url,target) {
      var obj = document.getElementById(target).getElementsByTagName('FORM')[0];
      var getstr = "?";
      for (i=0; i<obj.childNodes.length; i++) {
         if (obj.childNodes[i].tagName == "INPUT") {
            if ((obj.childNodes[i].type == "text") || (obj.childNodes[i].type == "hidden")) {
               getstr += obj.childNodes[i].name + "=" + encodeURIComponent(obj.childNodes[i].value) + "&";
            }
            if (obj.childNodes[i].type == "checkbox") {
               if (obj.childNodes[i].checked) {
                  getstr += obj.childNodes[i].name + "=" + encodeURIComponent(obj.childNodes[i].value) + "&";
               } else {
                  getstr += obj.childNodes[i].name + "=&";
               }
            }
            if (obj.childNodes[i].type == "radio") {
               if (obj.childNodes[i].checked) {
                  getstr += obj.childNodes[i].name + "=" + encodeURIComponent(obj.childNodes[i].value) + "&";
               }
            }
         }   
         if (obj.childNodes[i].tagName == "SELECT") {
            var sel = obj.childNodes[i];
            getstr += sel.name + "=" + encodeURIComponent(sel.options[sel.selectedIndex].value) + "&";
         }
      }
      ahah(url+getstr+'action=fragment&fragment='+target, target);
   }
