<?php

// On passe pour le moment via affiche_droite
// Mais envisager dans le futur une intégration au porte plume
function inserer_modeles_affiche_droite($flux){
	if (in_array($flux['args']['exec'],array('articles_edit','breves_edit','rubriques_edit','mots_edit'))) {
		include_spip('inc/inserer_modeles');
		if (count(inserer_modeles_lister_formulaires_modeles())>0)
			$flux['data'] .= recuperer_fond('prive/inserer_modeles',$flux['args']);
	}
	return $flux;
}

// Insertion dans le porte-plume

function inserer_modeles_porte_plume_barre_pre_charger($barres) {
	$barre = &$barres['edition'];
	$barre->ajouterApres('grpCaracteres', array(
				"id" => "sepInsMod",
				"separator" => "---------------",
				"display"   => true,
	));
	$barre->ajouterApres('sepInsMod', array(
		//groupe inserer_modeles et bouton inserer_modeles
		"id"          => 'inserer_modeles',
		"name"        => _T('inserer_modeles:outil_inserer_modeles'),
		"key"         => "M",
		"className"   => 'outil_inserer_modeles',
		"beforeInsert" => "function() {jQuery.modalboxload('".generer_url_ecrire('inserer_modeles')."',{minHeight: '90%'});}",
		"display"     => true,
		// "dropMenu"    => array(
			
		// ),
	 ));
	
	return $barres;
}

// Icônes pour le porte-plume

function inserer_modeles_porte_plume_lien_classe_vers_icone($flux) {
	return array_merge($flux, array(
		'outil_inserer_modeles' => 'inserer_modeles.png',
	));
}

?>