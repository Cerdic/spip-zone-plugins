<?php

// Renvoie une chaine qui decrit la session courante pour savoir si on peut
// utiliser un cache enregistre pour cette session.
// Par convention cette chaine ne doit pas contenir de caracteres [^0-9A-Za-z]
// Attention on ne peut *pas* inferer id_auteur a partir de la session, qui
// pourrait etre une chaine arbitraire -- ce n'est pas le cas pour l'instant
// http://doc.spip.org/@spip_session
function spip_session() {
	static $session;

	if (!isset($session)) {
		$session = $GLOBALS['auteur_session']
			? 'session'
				.$GLOBALS['auteur_session']['id_auteur']
				.'_'
				.$_COOKIE['spip_session']
			: '';
	}

	#spip_log('session: '.$session);
	return $session;
}



?>
