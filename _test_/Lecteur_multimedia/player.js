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
$(this).before('<span class="player"><object style="margin-left:0.1em" ' +
	'classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ' +
	'codebase="' +
	'http://fpdownload.macromedia.com/pub/shockwave/cabs/'+
	'flash/swflash.cab#version=6,0,0,0"' +
	'width="18" height="18" align="middle">' +
	'<param name="wmode" value="transparent" />' +
	'<param name="allowScriptAccess" value="sameDomain" />' +
	'<param name="flashVars" value="song_url='+playlist+'" />' +
	'<param name="movie" value="plugins/Lecteur_multimedia/musicplayer.swf?autoplay=false" />' +
	'<param name="quality" value="high" />' +
	'<embed style="margin-left:0.1em" ' +
	'src="plugins/Lecteur_multimedia/musicplayer.swf?autoplay=false" '+
	'flashVars="song_url='+playlist+'"' +
	'quality="high" wmode="transparent" width="18" height="18" name="player"' +
	' allowScriptAccess="sameDomain" type="application/x-shockwave-flash"' +
	' pluginspage="http://www.macromedia.com/go/getflashplayer" /></object></span>');
}
)

});




function expand(e,aff){
if(aff==0){
e.style.width='60%';
e.style.height='15%';
aff=1;
}else{
e.style.width='8%';
e.style.height='8%';
aff=0;
}
return aff;
}

function deplier(e,height_var,aff){
if(aff==0){
aff=e.style.height;
e.style.height=height_var;
}else{
e.style.height=aff;
aff=0;
}
return aff;
}

