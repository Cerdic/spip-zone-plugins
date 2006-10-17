/*
	appelee par le body onload, cette fonction affiche les players mp3 et genere les playlistes associées
	auteur :BoOz - booz@rezo.net
	code gnu - gpl

 par BoOz booz AT rezo.net
*
* Fonctionne avec jQuery.
**/


$(document).ready(function(){
//playliste();

mp3Array = new Array();

$("a[@rel='enclosure'][@href$=mp3]").each(
		function(i) {	 
				// we store mp3 links in an array (for a gallery)
				mp3Array.push(this.href);
		}
	);
		

$("a[@rel='enclosure'][@href$=mp3]").each(

function(i) {
	playlist='';
	deb=0;
	for(j=i; j < mp3Array.length ; j++) {
	if(deb > 0){
	playlist = playlist + ',' + mp3Array[j];
	}else{
	playlist = mp3Array[j];
	deb=1;
	}
	}

$(this).before('<span class="player"><object style="margin-right:0.1em" ' +
	'classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ' +
	'codebase="' +
	'http://fpdownload.macromedia.com/pub/shockwave/cabs/'+
	'flash/swflash.cab#version=6,0,0,0"' +
	'width="18" height="18" align="middle">' +
	'<param name="wmode" value="transparent" />' +
	'<param name="allowScriptAccess" value="sameDomain" />' +
	'<param name="flashVars" value="song_url='+playlist+'" />' +
	'<param name="movie" value="'+musicplayerurl+'?autoplay=false" />' +
	'<param name="quality" value="high" />' +
	'<embed style="margin-left:0.1em" ' +
	'src="'+musicplayerurl+'?autoplay=false" '+
	'flashVars="song_url='+playlist+'"' +
	'quality="high" wmode="transparent" width="18" height="18" name="player"' +
	' allowScriptAccess="sameDomain" type="application/x-shockwave-flash"' +
	' pluginspage="http://www.macromedia.com/go/getflashplayer" /></object></span>');

}
)

});

var ap_instances = new Array();

function ap_stopAll(playerID) {
	for(var i = 0;i<ap_instances.length;i++) {
		try {
			if(ap_instances[i] != playerID) document.getElementById("audioplayer" + ap_instances[i].toString()).SetVariable("closePlayer", 1);
			else document.getElementById("audioplayer" + ap_instances[i].toString()).SetVariable("closePlayer", 0);
		} catch( errorObject ) {
			// stop any errors
		}
	}
}

function ap_registerPlayers() {
	var objectID;
	var objectTags = document.getElementsByTagName("object");
	for(var i=0;i<objectTags.length;i++) {
		objectID = objectTags[i].id;
		if(objectID.indexOf("audioplayer") == 0) {
			ap_instances[i] = objectID.substring(11, objectID.length);
		}
	}
}

var ap_clearID = setInterval( ap_registerPlayers, 100 );


function play() {
    document.monFlash.SetVariable("player:jsPlay", "");
}
function pause() {
    document.monFlash.SetVariable("player:jsPause", "");
}
function stop() {
    document.monFlash.SetVariable("player:jsStop", "");
}
function volume(n) {
    document.monFlash.SetVariable("player:jsVolume", n);
}
