<?php
function auteurs_complets_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	if ($exec=='auteurs_edit'){
		include_spip('inc/auteurs_complets_gestion');
		$id_article = $flux['args']['id_auteur'];
		$flux['data'] .= auteurs_complets_ajouts($id_auteur);
	}
	return $flux;
}
?>