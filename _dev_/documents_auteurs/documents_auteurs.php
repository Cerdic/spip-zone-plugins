<?php
function documents_auteurs_affiche_gauche($flux) {
	$exec =  $flux['args']['exec'];
	if ($exec=='auteur_infos'){
		include_spip('inc/documents_auteurs_gestion');
		$id_auteur = $flux['args']['id_auteur'];
		$flux['data'] .= documents_auteurs_ajouts($id_auteur);
	}
	return $flux;
}
?>