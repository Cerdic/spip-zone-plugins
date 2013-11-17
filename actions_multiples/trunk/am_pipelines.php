<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_AM_PATTERN_TR'))
	define('_AM_PATTERN_TR', '%(<tr[^>]*>)(.*?)</tr>%s');

if (!defined('_AM_PATTERN_ID'))
	define('_AM_PATTERN_ID', "%<td([^>]*(?:id=([\"'])([^\"']+)\2)[^>]*|)>\n(.*?)\n</td>%s");

function am_insert_head_css($flux) {
//	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/qr.css').'" type="text/css" media="all" />';
	return $flux;
}

function am_header_prive($flux) {
//	$flux .= '<script src="'.find_in_path('js/qr.js').'" type="text/javascript"></script>';
	return $flux;
}

$GLOBALS['am_listes'] = array(
	'prive/objets/liste/articles',
);

function am_recuperer_fond($flux) {
	include_spip('inc/filtres');

	if (in_array($flux['args']['fond'], $GLOBALS['am_listes'])) {
		$fond = $flux['args']['fond'];
		$texte = $flux['data']['texte'];
		$objet = array_pop(explode('/', $fond));

		// Ajout d'une colonne en première position dans le thead pour coincider avec le tbody
		$thead = extraire_balise($texte, 'thead');
		if (preg_match(_AM_PATTERN_TR, $thead, $balises_tr)) {
			$choix = recuperer_fond('inclure/am_th_choix', $contexte);
			$texte = str_replace($balises_tr[2], $choix . $balises_tr[2], $texte);
		}

		// Ajout d'une ligne de boutons d'action en fin du thead de la table
		$contexte = array();
		$actions = recuperer_fond("inclure/am_$objet", $contexte);
		$texte = str_replace('</thead>', $actions . '</thead>', $texte);

		// Ajout pour chaque ligne du body d'une colonne en première position afin de faire
		// les choix des objets.
		// TODO : le problème est de récupérer l'id de l'objet pour le passer au fond
		$tbody = extraire_balise($texte, 'tbody');
		if (preg_match_all(_AM_PATTERN_TR, $tbody, $balises_tr)) {
			foreach($balises_tr[2] as $_balise) {
				// Trouver la valeur de l'id de l'objet en cours
				if (preg_match(_AM_PATTERN_ID, $_balise, $matches)){
					// Insérer la colonne de choix
					$contexte = array('nom' => "ids_$objet", 'valeur' => 1);
					$choix = recuperer_fond('inclure/am_td_choix', $contexte);
					$texte = str_replace($_balise, $choix . $_balise, $texte);
				}
			}
		}

		// Inclure le texte de la table dans un formulaire pour choisir les objets
		$contexte = array('liste' => $texte);
		$choix = recuperer_fond('formulaires/actionner', $contexte);


		$flux['data']['texte'] = $texte;
	}

	return $flux;
}
?>
