<?php
/*
 * Spip SMS Liste
 * Gestion de liste de diffusion de SMS
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

function autoriser_smslist_administrer($faire, $type, $id, $qui, $opt) {
	return
		( 
		($qui['statut'] == '0minirezo')
		AND (!$qui['restreint'])
		AND $GLOBALS["options"]=="avancees" 
		AND (!isset($GLOBALS['meta']['activer_smslist']) OR $GLOBALS['meta']['activer_smslist']!="non")
		);
}

?>