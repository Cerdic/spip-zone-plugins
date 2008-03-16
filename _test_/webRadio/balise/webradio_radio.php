<?php

/* Test de sécurité */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/abstract_sql');

function balise_WEBRADIO_RADIO ($p) {
	return calculer_balise_dynamique($p,'WEBRADIO_RADIO', array());
}

function balise_WEBRADIO_RADIO_stat($args, $filtres) {
	return ($args);
}

function balise_WEBRADIO_RADIO_dyn() {
	
	// calcul de la playlist
	$query = sql_select(
		Array('titre','descriptif','fichier'),
		array('spip_documents'),
		array('playlist = '.sql_quote('oui'))
	);

	// construction de la liste de fichier qui sera traité par dewplayer-multi
	$list = '';
	while( $row = sql_fetch($query)) {
		$list .= $row['fichier'].'|';
	}
	$list = substr($list,0,strlen($list)-1);

	// ce code n'est pas valide W3C, mais fonctionne
	$object = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"'
		.'codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0"'
		.' width="240" height="20" id="dewplayer" align="middle">'
		.'<param name="bgcolor" value="EEEEEE" />'
		.'<param name="allowScriptAccess" value="sameDomain" />'
		.'<param name="movie" value="'._DIR_PLUGIN_WEBRADIO.'dewplayer-multi.swf?mp3='.$list
		.'&amp;autostart=0'
		.'&amp;autoreplay=0'
		.'&amp;showtime=1'
		.'&amp;nopointer=1" />'
		.'<param name="quality" value="high" />'
		.'<param name="bgcolor" value="EEEEEE" />'
		.'<embed src="'._DIR_PLUGIN_WEBRADIO.'dewplayer-multi.swf?mp3='.$list
		.'&amp;autostart=0'
		.'&amp;autoreplay=0'
		.'&amp;showtime=1'
		.'&amp;nopointer=1"'
		.' quality="high"'
		.' bgcolor="FFFFFF" width="240" height="20"'
		.' name="dewplayer"'
		.' align="middle"'
		.' allowScriptAccess="sameDomain"'
		.' type="application/x-shockwave-flash"'
		.' pluginspage="http://www.macromedia.com/go/getflashplayer"></embed></object>';

	// ce code est valide W3C (mais ne semble pas fonctionner ...)
	/*$object = '<object type="application/x-shockwave-flash"'
			.'data="'._DIR_PLUGIN_WEBRADIO.'dewplayer-multi.swf?'.$list
			.'&amp;autoreplay=false'
			.'&amp;showtime=true'
			.'&amp;randomplay=false'
			.'" width="240" height="20">'
			.'<param name="wmode" value="transparent">'
			.'<param name="movie" value="dewplayer-multi.swf?'.$list
			.'&amp;autoreplay=false'
			.'&amp;showtime=true'
			.'&amp;randomplay=false'
			.'" /></object>';*/

	return $object;
}

?>