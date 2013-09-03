

function commApp(cmd)
{
  // fonc. pour le menu de l'appli.

  var lgi = document.getElementById('lgi').value;
  var mod = document.getElementById('mod').value;
  var lgo = document.getElementById('lgo').value;
  var lgc = document.getElementById('lgc').value;
	
  var scrap = getScrap();

  var etape='page_garde';
  if (cmd == 'traduire')
    etape = 'verif';
  else if (cmd == 'administrer')
    etape = 'administrer';

  location.href=SERVER_URL+"?etape="+etape+"&lgi="+lgi+"&mod="+mod+"&lgo="+lgo+"&lgc="+lgc+"&errscrap="+scrap;

}


function chLg(cmd) {

  var lgi = document.getElementById('lgi');
  lgi.value = cmd;
}


function chLgo(cmd) {

  var lgo = document.getElementById('lgo');
  lgo.value = cmd;
}


function chLgc(cmd) {

  var lgc = document.getElementById('lgc');
  lgc.value = cmd;
}


function chMod(cmd) {

  var mod = document.getElementById('mod');
  mod.value = cmd;
}


function getScrap() {

  var query=this.location.search.substring(1);
  var ret="";

  if (query.length > 0){
    var params=query.split("&");

    for (var i=0 ; i<params.length ; i++){

      var pos = params[i].indexOf("=");
      var name = params[i].substring(0, pos);

      if (name!="erreur" && name!="messerreur" && name!="errscrap")
      {
        if (i==0)
          ret = params[i];
        else 
          ret = ret + "&" + params[i];
      }
   }

   return escape(ret);
 }
}


function getParam(rname) {

  var query=this.location.search.substring(1);
  var ret="";

  if (query.length > 0){
    var params=query.split("&");

  for (var i=0 ; i<params.length ; i++){

    var pos = params[i].indexOf("=");
    var name = params[i].substring(0, pos);
    if (name==rname) {
      var lg = params[i].length;
      ret = params[i].substring(pos+1,lg);
      return ret;
      }
    }
  }

  return "";
}


//Start phpRequest Object
function phpRequest() {
  //Set some default variables
  this.parms = new Array();
  this.parmsIndex = 0;

  //Set the server url
  this.server = SERVER_URL;

  //Add two methods
  this.execute = phpRequestExecute;
  this.add = phpRequestAdd;
}

function phpRequestAdd(name,value) {
  //Add a new pair object to the params
  this.parms[this.parmsIndex] = new Pair(name,value);
  this.parmsIndex++;
}

function phpRequestExecute() {
  //Set the server to a local variable
  var targetURL = this.server;
  
  //Try to create our XMLHttpRequest Object
  try {
    var httpRequest = new XMLHttpRequest();
  }catch (e){
    alert('Error creating the connection!');
    return;
  }
  
  //Make the connection and send our data
  try {
    var txt = "?1";
    for(var i in this.parms) {
      txt = txt+'&'+this.parms[i].name+'='+this.parms[i].value;
    }
    //Two options here, only uncomment one of these
    //GET REQUEST
    httpRequest.open("GET", targetURL+txt, false, null, null);  

    //POST REQUEST EXAMPLE
    /*
    httpRequest.open("POST", targetURL+txt, false, null, null);  
    httpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    */
    httpRequest.send('');
  }catch (e){
    alert('An error has occured calling the external site: '+e);
    return false;
  } 

  //Make sure we received a valid response
  switch(httpRequest.readyState) {
    case 1,2,3:
      alert('Bad Ready State: '+httpRequest.status);
      return false;
    break;
    case 4:
      if(httpRequest.status !=200) {
        alert('The server respond with a bad status code: '+httpRequest.status);
        return false;
      } else {
        var response = httpRequest.responseText;
      }
    break;
  }
  
  return response;
}

//Utility Pair class
function Pair(name,value) {
  this.name = name;
  this.value = value;
}
