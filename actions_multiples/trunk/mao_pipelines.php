<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_MAO_PATTERN_TR'))
	define('_MAO_PATTERN_TR', '%(<tr[^>]*>)(.*?)</tr>%s');

if (!defined('_MAO_PATTERN_TD_ID'))
	define('_MAO_PATTERN_TD_ID', "%<td([^>]*(?:class=([\"'])@classe@\\2)[^>]*|)>(.*?)</td>%Umis");

if (!defined('_MAO_PATTERN_A_ID'))
	define('_MAO_PATTERN_A_ID', "%<a\b[^>]*>(.*)</a\b>%Umis");


function mao_header_prive($flux) {
//	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/mao.css').'" type="text/css" media="all" />';
	return $flux;
}


function mao_recuperer_fond($flux) {
	static $fonds = array();

	// Acquerir la configuration des listes
	$actions = mao_lister_actions_multiples();
	// Extraire le nom du fond en cours
	$fond = $flux['args']['fond'];

	// On plonge la liste dans un formulaire en la modifiant
	// - si le fond est celui d'une liste configurée
	// - et si le texte de la liste n'est pas vide
	// - et si on ne repasse pas dans ce pipeline pour la même liste
	if (array_key_exists($fond, $actions)
	AND trim($flux['data']['texte'])
	AND !$fonds[$fond]) {
		// Extraction de l'objet et du contenu de la liste
		$objet = $actions[$fond]['objet'];

		// On enregistre le premier passage de la liste dans le pipeline pour éviter de boucler sans fin
		$fonds[$fond] = true;

		// On remplace la liste des objets par un formulaire qui appellera à nouveau cette liste
		$contexte = array('liste' => $fond, 'objet' => $objet);
		$texte = recuperer_fond('prive/squelettes/inclure/mao_actionner', $contexte);

		$flux['data']['texte'] = $texte;
	}

	return $flux;
}
?>
