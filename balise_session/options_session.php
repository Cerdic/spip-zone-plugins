<?php

/***************************************************************************\
 *  Balise #SESSION pour SPIP                                              *
 *  http://www.spip-contrib.net/balise-session                             * 
 *                                                                         *
 *  Auteur : james.at.rezo.net (c) 2006                                    *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
\***************************************************************************/

// Indique si on est dans l'espace prive
// http://doc.spip.org/@test_espace_prive
if (!defined('test_espace_prive')) {
function test_espace_prive() {
	return defined('_ESPACE_PRIVE') ? _ESPACE_PRIVE : false;
}
}
// Activer l'invalideur de session
// http://doc.spip.org/@invalideur_session
function invalideur_session(&$Cache) {
	$Cache['session']=spip_session();
	return '';
}
if (!isset($GLOBALS['spip_pipeline']['definir_session'])) 
	$GLOBALS['spip_pipeline']['definir_session'] = '';
// Renvoie une chaine qui decrit la session courante pour savoir si on peut
// utiliser un cache enregistre pour cette session.
// Par convention cette chaine ne doit pas contenir de caracteres [^0-9A-Za-z]
// Attention on ne peut *pas* inferer id_auteur a partir de la session, qui
// est une chaine arbitraire
// Cette chaine est courte (8 cars) pour pouvoir etre utilisee dans un nom
// de fichier cache
// http://doc.spip.org/@spip_session
function spip_session($force = false) {
	static $session;
	if ($force OR !isset($session)) {
		$s = pipeline('definir_session',
			$GLOBALS['auteur_session']
			? serialize($GLOBALS['auteur_session'])
				. '_' . @$_COOKIE['spip_session']
			: ''
		);
		$session = $s ? substr(md5($s), 0, 8) : '';
	}
	#spip_log('session: '.$session);
	return $session;
}

?>