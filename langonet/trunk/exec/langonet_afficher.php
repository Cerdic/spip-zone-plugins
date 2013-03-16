<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_langonet_afficher_dist(){
	global $spip_lang_right;
	// si pas autorise : message d'erreur
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		die();
	}

	$file_name = _request('fichier');
	if (!@is_readable($file_name)) {
		spip_log("*** LANGONET (action_langonet_afficher_dist) ERREUR: $file_name pas accessible en lecture");
		include_spip('inc/minipres');
		echo minipres();
		die();
	}

	// Lecture du fichier de log (.log) ou de langue (.php)
	$infos = pathinfo($file_name);
	$extension = $infos['extension'];
	if ($extension == 'log')
		$contexte = array('fichier' => file_get_contents($file_name));
	else
		$contexte = array('fichier' => htmlentities(file_get_contents($file_name)));

	// contenu de la popup de mediabox
 	ajax_retour(recuperer_fond('prive/contenu/langonet_afficher',  $contexte));
}

?>
