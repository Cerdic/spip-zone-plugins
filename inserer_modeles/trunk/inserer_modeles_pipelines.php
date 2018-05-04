<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function inserer_modeles_header_prive($flux) {
	$js = find_in_path('javascript/autosize.min.js');
	$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
	return $flux;
}

function inserer_modeles_insert_head($flux){
	if(lire_config('barre_outils_public') == 'oui'){
		$js = find_in_path('javascript/autosize.min.js');
		$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
	}
	return $flux;
}



function inserer_modeles_affiche_droite($flux) {
	$config_meta = inserer_modeles_configuration();
	if (in_array($flux['args']['exec'], $config_meta['objets'])) {
		include_spip('inc/inserer_modeles');
		if (count(inserer_modeles_lister_formulaires_modeles()) > 0) {
			$flux['data'] .= recuperer_fond('inserer_modeles', $flux['args']);
		}
	}

	return $flux;
}

// Insertion dans le porte-plume

function inserer_modeles_porte_plume_barre_pre_charger($barres) {
	include_spip('inc/inserer_modeles');
	$formulaires_modeles = inserer_modeles_lister_formulaires_modeles();

	if (count($formulaires_modeles) > 0) {
		$barre = &$barres['edition'];
		$barre->ajouterApres('grpCaracteres', array(
					'id' => 'sepInsMod',
					'separator' => '---------------',
					'display' => true,
		));

		$sous_menu = array();
		foreach ($formulaires_modeles as $nom => $formulaire) {
			if (substr($nom, -5) == '.yaml') {
				$nom = substr($nom, 0, -5);
			}
			$sous_menu[] = array(
				'id' => 'inserer_modele_'.$nom,
				'name' => _T_ou_typo($formulaire['nom']),
				'className' => 'outil_inserer_modele_'.$nom,
				'beforeInsert' => "function() {jQuery.modalboxload('".url_absolue(generer_url_public(
					'inserer_modeles',
					"modalbox=oui&formulaire_modele=$nom&id_article='+$(\"[name='id_article']\").val()+'&id_rubrique='+$(\"[name='id_rubrique']\").val()+'&id_breve='+$(\"[name='id_breve']\").val()"
				)).",{minHeight: '90%'});}",
				'display' => true,
			);
		}

		$barre->ajouterApres('sepInsMod', array(
			//groupe inserer_modeles et bouton inserer_modeles
			'id' => 'inserer_modeles',
			'name' => _T('inserer_modeles:outil_inserer_modeles'),
			'key' => 'M',
			'className' => 'outil_inserer_modeles',
			'beforeInsert' => "function() {jQuery.modalboxload('".url_absolue(generer_url_public(
				'inserer_modeles',
				"modalbox=oui&id_article='+$(\"[name='id_article']\").val()+'&id_rubrique='+$(\"[name='id_rubrique']\").val()+'&id_breve='+$(\"[name='id_breve']\").val()"
			)).",{minHeight: '90%'});}",
			'display' => true,
			'dropMenu' => $sous_menu,
		 ));
	}

	return $barres;
}

// Icones pour le porte-plume

function inserer_modeles_porte_plume_lien_classe_vers_icone($flux) {
	include_spip('inc/inserer_modeles');
	$formulaires_modeles = inserer_modeles_lister_formulaires_modeles();
	if (count($formulaires_modeles) > 0) {
		$icones = array();
		$icones['outil_inserer_modeles'] = 'inserer_modeles.png';
		foreach ($formulaires_modeles as $nom => $formulaire) {
			if (substr($nom, -5) == '.yaml') {
				$nom = substr($nom, 0, -5);
			}
			$icones['outil_inserer_modele_'.$nom] = $formulaire['icone_barre'];
		}

		return array_merge($flux, $icones);
	} else {
		return $flux;
	}
}

/*
 Merci marcimat pour l'astuce (cf. plugin links z90425)
 */
function inserer_modeles_configuration() {
	$configuration = array();
	if (isset($GLOBALS['meta']['inserer_modeles'])) {
		include_spip('inf/filtres');
		$configuration = unserialize($GLOBALS['meta']['inserer_modeles']);
		// Comme on utilise la saisie choisir_objets,
		// on retravaille les objets selectionnes pour avoir leur url_edit
		foreach ($configuration['objets'] as $key => $objet) {
			$configuration['objets'][$key] = objet_info($objet, 'url_edit');
		}
	}
	$inserer_modeles = (count($configuration) > 0) ? $configuration : array(
		'objets' => array('article_edit', 'breve_edit', 'rubrique_edit', 'mot_edit'),
	);
	$inserer_modeles = array_map('array_filter', $inserer_modeles);

	return $inserer_modeles;
}
