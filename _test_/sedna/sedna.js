/* Fixer le path du cookie sur le repertoire local (et non pas /) */
function recuperer_cookiepath() {
	var cookiepath = location.href;
	/* remonter au repertoire */
	cookiepath = cookiepath.substring(0,cookiepath.lastIndexOf('/'));
	/* supprimer la methode http:// */
	cookiepath = cookiepath.substring(cookiepath.indexOf('://')+3);
	/* supprimer hostname */
	cookiepath = cookiepath.substring(cookiepath.indexOf('/')+1);
	/* alert(cookiepath); */
	return cookiepath;
}

/* create- and readCookie are adapted from (c) Paul Sowden http://www.alistapart.com/articles/alternate/ */
function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else expires = "";
	document.cookie = name+"="+value+expires+"; path=/"+recuperer_cookiepath();
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}


/*
 * Fonctions pour Sedna (c) Fil -- GNU/GPL
 */

/* une fonction pour modifier l'affichage des item d'un site */
function change_site(id) {
	var cookie = readCookie('sedna_ignore_'+id);

	/* supprimer le site du cookie */
	if (cookie) {
		createCookie('sedna_ignore_'+id, '', -1);
	} else {
		createCookie('sedna_ignore_'+id, 1, 365);
	}
}

/* gerer le cookie des articles lus */
/* attention on ne doit pas depasser 3ko */
function jai_lu(id) {
	if (!est_lu(id)) {
		sedna_nouv--;
		var cookie = readCookie("sedna_lu");
		cookie = cookie ? (id + '-' + cookie) : id;
		cookie = cookie.substring(0,3000);
		createCookie("sedna_lu", cookie, 365);
		var a = document.getElementById('news'+id);
		a.className='linkvu'; /* ce lien change de style */
		document.title = sedna_title+' ('+sedna_nouv+'/'+sedna_total+')';
	}
}

/* Afficher ou masquer les descriptifs */
function style_desc(aff) {
	var a;
	var c=1;
	createCookie("sedna_style", aff, 365);
	while (a = document.getElementById('desc_'+(c++))) {
		a.className="desc_"+aff;
	}
	if (aff == 'masquer') {
		document.getElementById('desc_masquer').className='selected';
		document.getElementById('desc_afficher').className='';
	} else {
		document.getElementById('desc_masquer').className='';
		document.getElementById('desc_afficher').className='selected';
	}
}

/* passer en mode synchro */
function sedna_synchro(on) {
	if (on == 'oui') {
		createCookie('sedna_synchro', 'oui', 365);
	} else if (on == 'non') {
		createCookie('sedna_synchro', '', 0);
	}

	if (document.getElementById('synchrooui')) {
		if (on == 'oui') {
			document.getElementById('synchrooui').className='selected';
			document.getElementById('synchronon').className='';
		} else {
			document.getElementById('synchrooui').className='';
			document.getElementById('synchronon').className='selected';
		}
	}
}

// nous dit si l'article id est lu ou pas
function est_lu(id) {
	var cookie = '--'+escape(readCookie("sedna_lu"))+'-';
	return (cookie.indexOf('-'+id+'-') > 0);
}

// lien player sur les mp3 ; src= http://musicplayer.sourceforge.net/
// cf. http://www.alistapart.com/articles/flashsatay/
function play(e,url) {
	var player = document.createElement('div')
	player.className = 'musicplayer';

	player.innerHTML = '<object type="application/x-shockwave-flash" ' +
	'data="musicplayer.swf?autoplay=false" ' +
	'width="18" height="18">' +
	'<param name="wmode" value="transparent" />' +
	'<param name="allowScriptAccess" value="sameDomain" />' +
	'<param name="flashVars" value="song_url='+url+'" />' +
	'<param name="movie" value="musicplayer.swf?autoplay=false" />' +
	'<param name="quality" value="high" />' +
	'</object>';

	// version alternative sans flash (N-K)
	// ne fonctionne pas sur Safari ; sur firefox, charge tous les fichiers
/*
	player.innerHTML = '<object ' +
            ' data="'+url+'" style="width:10em; height:1em;" '+
            ' type="audio/mpeg"><param name="autostart" value="false"/></object>';
*/

	var f = e.parentNode.parentNode.parentNode;
	f.parentNode.insertBefore(player, f);
}

/*
	appelee par le body onload pour remettre le bon etat sur les
	liens qui ont change de couleur mais qui se trouvent sur des
	pages valables dans le cache du navigateur
*/
function sedna_init() {

	// regler la position du bouton "synchro"
	sedna_synchro(readCookie('sedna_synchro'));

	// globals
	sedna_nouv = 0;
	sedna_total =0;

	var a,d,i;
	d = document.getElementsByTagName('a');
	for(i=0; a=d[i]; i++) {
		if (a.id
		&& (a.id.substr(0,4) == 'news')) {
			if (est_lu(a.id.substr(4))) {
				a.className='linkvu';
			} else {
				sedna_nouv++;
			}
			sedna_total++;
		}

		// Reperer les liens mp3 et ajouter le player
		if (a.rel=='enclosure' && a.href.match(/\.mp3$/i)) {
			play(a,a.href);
		}
	}

	// Marquer dans la barre de titre le nombre d'articles nouveaux
	sedna_title = document.title;
	document.title = sedna_title + ' (' + sedna_nouv + '/' + sedna_total + ')';

}


//
// addLoadEvent()
// Adds event to window.onload without overwriting currently assigned onload functions.
// Function found at Simon Willison's weblog - http://simon.incutio.com/
// & from lightbox.js
function addLoadEvent(func)
{	
	var oldonload = window.onload;
	if (typeof window.onload != 'function'){
    	window.onload = func;
	} else {
		window.onload = function(){
		oldonload();
		func();
		}
	}

}



addLoadEvent(sedna_init);	// run initLightbox onLoad