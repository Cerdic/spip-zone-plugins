<?php

/**
 * Enregistre une revision de ticket
 *
 * @return
 * @param int $id_ticket
 * @param array $c[optional]
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function revision_ticket($id_ticket, $c=false) {

	// invalider le cache quelque soit la circonstance.
	// une modification de base = effacer les caches.
	$invalideur = "id='id_ticket/$id_ticket'";
	$indexation = true;

	modifier_contenu('ticket', $id_ticket,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur,
			'indexation' => $indexation,
			'date_modif' => 'date_modif' // champ a mettre a date('Y-m-d H:i:s') s'il y a modif
		),
		$c);

	return ''; // pas d'erreur
}

?>