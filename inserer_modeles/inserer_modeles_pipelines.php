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
	include_spip('inc/inserer_modeles');
	$formulaires_modeles = inserer_modeles_lister_formulaires_modeles();
	
	if (count($formulaires_modeles)>0) {
		$barre = &$barres['edition'];
		$barre->ajouterApres('grpCaracteres', array(
					"id" => "sepInsMod",
					"separator" => "---------------",
					"display"   => true,
		));
		
		$sous_menu = array();
		foreach ($formulaires_modeles as $nom => $formulaire) {
			if (substr($nom,-5)=='.yaml')
				$nom = substr($nom,0,-5);
			$sous_menu[] = array(
				"id" => 'inserer_modele_'.$nom,
				"name" => _T_ou_typo($formulaire['nom']),
				"className" => 'outil_inserer_modele_'.$nom,
				"beforeInsert" => "function() {jQuery.modalboxload('".generer_url_ecrire(
					'inserer_modeles',
					"formulaire_modele=$nom&id_article='+$(\"[name='id_article']\").val()+'&id_rubrique='+$(\"[name='id_rubrique']\").val()+'&id_breve='+$(\"[name='id_breve']\").val()"
				).",{minHeight: '90%'});}",
				"display" => true
			);
		}
		
		$barre->ajouterApres('sepInsMod', array(
			//groupe inserer_modeles et bouton inserer_modeles
			"id"          => 'inserer_modeles',
			"name"        => _T('inserer_modeles:outil_inserer_modeles'),
			"key"         => "M",
			"className"   => 'outil_inserer_modeles',
			"beforeInsert" => "function() {jQuery.modalboxload('".generer_url_ecrire(
				'inserer_modeles',
				"id_article='+$(\"[name='id_article']\").val()+'&id_rubrique='+$(\"[name='id_rubrique']\").val()+'&id_breve='+$(\"[name='id_breve']\").val()"
			).",{minHeight: '90%'});}",
			"display"     => true,
			"dropMenu"    => $sous_menu
		 ));
	}
	
	return $barres;
}

// Icônes pour le porte-plume

function inserer_modeles_porte_plume_lien_classe_vers_icone($flux) {
	include_spip('inc/inserer_modeles');
	$formulaires_modeles = inserer_modeles_lister_formulaires_modeles();
	if (count($formulaires_modeles)>0) {
		$icones = array();
		$icones['outil_inserer_modeles'] = 'inserer_modeles.png';
		foreach ($formulaires_modeles as $nom => $formulaire) {
			if (substr($nom,-5)=='.yaml')
				$nom = substr($nom,0,-5);
			$icones['outil_inserer_modele_'.$nom] = $formulaire['icone_barre'];
		}
		return array_merge($flux, $icones);
	} else
		return $flux;
}

?>