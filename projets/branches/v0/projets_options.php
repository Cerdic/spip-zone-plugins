<?php

// On definit le titre de la boite logo d'un projet afin d'utiliser iconifier()
$GLOBALS['logo_libelles']['id_projet'] = _T('projets:logo_projet');

/**
 * Enregistre une revision de projet
 *
 * @param object $id_article
 * @param object $c [optional]
 * @return
 */
function revision_projet($id_projet, $c=false) {

	// Si le projet est publie, invalider les caches et demander sa reindexation
	$t = sql_getfetsel("statut", "spip_projets", "id_projet=$id_projet");
	if ($t == 'publie') {
		$invalideur = "id='id_projets/$id_projet'";
		$indexation = true;
	}

	modifier_contenu('projet', $id_projet,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur,
			'indexation' => $indexation
		),
		$c);

	return ''; // pas d'erreur
}

?>