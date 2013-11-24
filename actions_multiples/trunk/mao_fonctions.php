<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


if (!defined('_MAO_PATTERN_TR'))
	define('_MAO_PATTERN_TR', '%(<tr[^>]*>)(.*?)</tr>%s');

if (!defined('_MAO_PATTERN_TD_ID'))
	define('_MAO_PATTERN_TD_ID', "%<td([^>]*(?:class=([\"'])@classe@\\2)[^>]*|)>(.*?)</td>%Umis");

if (!defined('_MAO_PATTERN_A_ID'))
	define('_MAO_PATTERN_A_ID', "%<a\b[^>]*>(.*)</a\b>%Umis");


function mao_lister_actions_multiples($objet='') {
	static $actions = null;

	if (is_null($actions)) {
		$actions = array(
			'prive/objets/liste/articles' => array(
				'objet' => 'articles',
				'nb_colonnes' => 5,
				'colonne_id' => 'id',
			),
			'prive/objets/liste/rubriques' => array(
				'objet' => 'rubriques',
				'nb_colonnes' => 5,
				'colonne_id' => 'id',
			),
			'prive/objets/liste/auteurs' => array(
				'objet' => 'auteurs',
				'nb_colonnes' => 5,
				'colonne_id' => 'id',
			),
		);
		$actions = pipeline('declarer_actions_multiples', $actions);
	}

	if ($objet)
		return isset($actions[$objet]) ? $actions[$objet] : array();
	else
		return $actions;

}

function mao_actionner($texte, $fond, $objet) {
	$actions = mao_lister_actions_multiples();

	// Ajout d'une colonne en première position dans le thead pour coincider avec le tbody
	$contexte = array();
	$thead = extraire_balise($texte, 'thead');
	if (preg_match(_MAO_PATTERN_TR, $thead, $balises_head_tr)) {
		$choix = recuperer_fond('prive/squelettes/inclure/mao_th_choix', $contexte);
		$texte = str_replace($balises_head_tr[2], "\n\t\t\t${choix}{$balises_head_tr[2]}", $texte);
	}

	// Ajout d'une ligne de boutons d'action en fin du thead de la table
	$contexte = array('colspan' => $actions[$fond]['nb_colonnes'] + 1);
	$tr_actions = recuperer_fond("prive/squelettes/inclure/mao_${objet}", $contexte);
	$texte = str_replace('</thead>', "${tr_actions}\n\t</thead>", $texte);

	// Ajout pour chaque ligne du body d'une colonne en première position afin de faire
	// les choix des objets.
	$tbody = extraire_balise($texte, 'tbody');
	$regexp_id = str_replace('@classe@', $actions[$fond]['colonne_id'], _MAO_PATTERN_TD_ID);
	if (preg_match_all(_MAO_PATTERN_TR, $tbody, $balises_tr)) {
		foreach($balises_tr[2] as $_balise) {
			if (preg_match($regexp_id, $_balise, $td_id)){
				// Extraire l'id directement ou dans un <a>
				$id = 0;
				if (!$id = intval(trim($td_id[3]))) {
					if (preg_match(_MAO_PATTERN_A_ID, $td_id[3], $a_id))
						$id = intval(trim($a_id[1]));
				}
				// Insérer la colonne de choix
				$contexte = array('nom' => "ids_$objet", 'valeur' => $id);
				$choix = recuperer_fond('prive/squelettes/inclure/mao_td_choix', $contexte);
				$texte = str_replace($_balise, $choix . $_balise, $texte);
			}
		}
	}

	return $texte;
}

?>