<?php

// On deroute inc/forum_insert pour verifier d'abord chez akismet
function inc_forum_insert() {
	require_once _DIR_RESTREINT.'inc/forum_insert.php';
	require_once _DIR_PLUGIN_AKISMET.'Akismet.class.php';

	// Antispam : si 'nobot' a ete renseigne, ca ne peut etre qu'un bot
	if (strlen(_request('nobot'))) {
		tracer_erreur_forum('champ interdit (nobot) rempli');
		return '!'; # echec silencieux du POST
	}

	// On ne passe pas dans akismet
	// si le visiteur est connecte
	// ou si akismet n'est pas configure
	if (
	!isset($GLOBALS['auteur_session']['statut'])
	AND $cfg = @unserialize($GLOBALS['meta']['akismet'])
	AND strlen($cfg['apiKey'])
	) {
		$akismet = new Akismet($GLOBALS['meta']["adresse_site"], $cfg['apiKey']); 
		// Utilisez votre propre cle de developpeur Wordress, 
		// disponible sur http://akismet.com/personal/ pour un usage personnel / non commercial
		// ou http://akismet.com/commercial/ sinon
		$akismet->setAuthor($_POST['auteur']);
		$akismet->setAuthorEmail($_POST['email_auteur']);
		$akismet->setAuthorURL($_POST['url_site']);
		$akismet->setContent($_POST['texte']);
		$akismet->setType('comment');
		$akismet->setPermalink('http://' . $GLOBALS['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		if ($akismet->isSpam()) {
			// sauvegarde le commentaire mais le garde comme spam
			// (en cas de faux positif)
			tracer_erreur_forum('Akismet a d&#233;tect&#233; un SPAM');
			return inc_forum_insert_dist('spam');

			// Si on prefere ne pas enregistrer et mourrrrir :
			// return self('&');
		}
	}

	return inc_forum_insert_dist();
}

?>
