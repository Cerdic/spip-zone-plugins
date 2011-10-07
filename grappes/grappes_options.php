<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction revision_grappe
 * Permet notamment de crayonner les grappes
 *
 * @return
 * @param object $id_grappe
 * @param object $c[optional]
 */
function revision_grappe($id_grappe, $c=false) {
	modifier_contenu('grappe', $id_grappe,
		array(
			'champs' => array('titre', 'descriptif', 'liaisons','options'),
			'nonvide' => array('titre' => _T('info_sans_titre'))
		),
		$c);
}
?>
