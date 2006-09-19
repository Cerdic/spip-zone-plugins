/***************************************************************************
* Actionscript pour le plug-in console
****************************************************************************/
/*
_root.spiplog= "http://localhost/_spip/core/ecrire/?exec=spiplog&logfile=spip";
_root.sqllog= "http://localhost/_spip/core/ecrire/?exec=spiplog&logfile=mysql";
*/

//
// Parametres
var duration:Number = 1000;     // duree de rafraichissement de la console (en ms)

//
// comportement bouttons
but_spip.onRelease = function() {
	urllog = spiplog;
	loadXMLLog();
}

but_sql.onRelease = function() {
	urllog = sqllog;
	loadXMLLog();
}

//
// MAIN
System.useCodepage = true;
var intervalId:Number;

var xml_log:XML = new XML();
urllog = spiplog;
showlog = log_texte;

xml_log.onLoad = function(success:Boolean) {
	if (success) {				
		traiteXMLLog();
	} else {
		showlog = "erreur: impossible de changer le fichier log";
		//clearInterval(intervalId); // ne pas surcharger le serveur
	}
}

function traiteXMLLog():Void {
	// Ces 2 tableaux permettront de stocker et traiter les donnees lues depuis le fichier XML...
	log = xml_log.firstChild.childNodes;
	log_texte = log[0].firstChild.nodeValue;
	showlog = log_texte;

	//clearInterval(intervalId);
}

function loadXMLLog():Void {
	trace(urllog);
	xml_log.load(urllog);
}


// rafraichir la console
if (intervalId == null) loadXMLLog(); // premier chargement
intervalId = setInterval(this, "loadXMLLog" , duration);				  
stop();