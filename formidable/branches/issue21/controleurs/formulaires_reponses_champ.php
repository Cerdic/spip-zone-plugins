<?php

/**
 * Controleur de Crayons pour les champs d'une réponse
 *
 * @param array $regs
 * @param array|null $c
 * @return array Liste html, erreur
 */
function controleurs_formulaires_reponses_champ_dist($regs, $c = null) {
	include_spip('inc/saisies');
	list(,$crayon, $type, $champ, $id) = $regs;
	$id_formulaires_reponses_champ = $regs[4];

	// Cas 1. On est sur un champ déjà enregistreé
	if (strpos($id_formulaires_reponses_champ, 'new') === false) {
	// Recuperer id_formulaires_reponse et id_formulaire
	// Note, sans doute pourrait-on passer directement cela en classe
	// Mais
	// 1. Cela ferait une exception
	// 2. Des gens utilisent peut être pas #VOIR_REPONSE{xxx,edit}
		//
		$data = sql_fetsel('saisies, nom, valeur', 'spip_formulaires_reponses_champs JOIN spip_formulaires_reponses JOIN spip_formulaires', "id_formulaires_reponses_champ=$id_formulaires_reponses_champ AND spip_formulaires_reponses.id_formulaires_reponse = spip_formulaires_reponses_champs.id_formulaires_reponse AND spip_formulaires.id_formulaire = spip_formulaires_reponses.id_formulaire");
		$nom = $data['nom'];
		$valeur = $data['valeur'];
	} else {
		$valeur = '';
		preg_match('#new-(.*)-(.*)#', $id_formulaires_reponses_champ, $match);
		$nom = $match[1];
		$id_formulaires_reponse = $match[2];
		$data = sql_fetsel('saisies', 'spip_formulaires_reponses JOIN spip_formulaires', "id_formulaires_reponse=$id_formulaires_reponse  AND spip_formulaires.id_formulaire = spip_formulaires_reponses.id_formulaire");
	}

	$saisie = saisies_chercher(unserialize($data['saisies']), $nom);

	$n = new Crayon(
		$type . '-valeur-' . $id_formulaires_reponses_champ,
		$valeur,
		array('controleur' => 'controleurs/formulaires_reponses_champ')
	);
	$key = $n->key;

	unset($saisie['options']['label']);
	unset($saisie['options']['explication']);
	unset($saisie['options']['class']);
	unset($saisie['options']['li_class']);
	// Crayons utilise son propre formalisme pour le 'name' des saisies.
	$nom_crayons = 'content_' . $key . '_valeur';
	$saisie['options']['nom'] = $nom_crayons;
	spip_log($this_saisie, 'formidable_crayons');

	$contexte = array('_saisies' => array($saisie), $nom_crayons =>  $valeur);
	$html = $n->formulaire($contexte);

	$status = $scripts = null;

	// probablement pas la meilleure idée du siècle…
	// mais tenter d'afficher correctement le picker de date du plugin saisies dans l'espace public
	if (!test_espace_prive() and $saisie['saisie'] == 'date') {
		$scripts = '<link rel="stylesheet" type="text/css" href="' . find_in_path('css/ui/jquery-ui.css') . '" />';
	}

	return array($html . $scripts, $status);
}
