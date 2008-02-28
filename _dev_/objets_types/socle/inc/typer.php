<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');	//pour *_cadre_*() et *_block_*()
include_spip('inc/actions');		//pour ajax_*()
include_spip('public/parametrer');	//quete_parent() pour calculer_hierarchie()
include_spip('public/composer');	//pour calculer_hierarchie() (heritage)
include_spip('inc/rubriques');		//pour calcul_branche() (propagation)

// $id: identifiant de l'objet a typer
// $flag: true si editable
// $type: le type a affecter a l'objet
// $objet: 'rubrique' ou 'article'
// $script: page de provenance et surtout de retour
// $id_parent : identifiant du parent de l'objet (pour recherche dans la hierarchie)
function inc_typer_dist($id, $flag, $type, $objet, $script, $id_parent) {
	global $spip_lang_left, $spip_lang_right, $types;
	
	//merge si la rubrique est un secteur
	if($objet == 'rubrique' AND ($id_parent == 0))
		$types['rubrique'] = array_merge($types['rubrique'], $types['secteur']);

	//heritage du type du conteneur le plus proche de l'objet
	if($objet == 'rubrique' OR $objet == 'article') {
		$type_herite = '';
		$hierarchie = calculer_hierarchie($id_parent);
		$resultat = ($hierarchie != 0) ?
			sql_select(_TYPE, 'spip_rubriques', _TYPE.'!=\'rubrique\' AND id_rubrique IN ('.$hierarchie.')') :
			'';
		while($row = sql_fetch($resultat)) $type_herite = $row[_TYPE];
		if($type_herite AND isset($types[$objet][$type_herite]))
			if(is_array($types[$objet][$type_herite])) {
				$types[$objet] = array_merge($types[$objet], $types[$objet][$type_herite]);
			} else {
				$types[$objet] = explode(',', $types[$objet][$type_herite]);
			}
	}
	
	if($flag AND isset($types[$objet]) AND is_array($types[$objet]) AND count($types[$objet])>1) {

		$liste = lister_types($objet, $type, $types);

		$bouton = _T('types:titre_typer_'.$objet);	
		if ($type != $objet)
			$bouton .= "&nbsp; (".afficher_nom_type($type, $objet).")";

		$masque = '<label for="type">'._T('types:choisir_type_'.$objet).'</label>'."\n" . $liste . "\n";

		$res = debut_cadre_enfonce(find_in_path('images/types-24.gif'), true, '',
			bouton_block_depliable($bouton,$flag === 'ajax','type'));
		$res .= debut_block_depliable($flag === 'ajax','type');
		$res .= ajax_action_post("typer", 
			"$id/$objet",
			$script,
			"id_$objet=$id",
			$masque,
			_T('bouton_changer'),
		       " class='fondo visible_au_chargement' id='valider_type'", "",
			"&id=$id&objet=$objet");
		$res .= fin_block();
		$res .= fin_cadre_enfonce(true);
	}

	return ajax_action_greffe("typer", $id, $res);
}

function afficher_nom_type($type, $objet) {
	return _T(($type == $objet) ? 'types:normal_'.$objet : $type.':'.$type);
}

function lister_types($objet, $type_courant, $types) {
	$liste = '';
	foreach(array_unique($types[$objet]) as $type)
		$liste .= '<option value="' . $type . '"' .
			($type == $type_courant ? 'selected="selected"': '').'>' .
			afficher_nom_type($type, $objet) .
			'</option>'."\n";
	return '<select class="fondl" onchange="findObj_forcer(\'valider_type\').style.visibility=\'visible\';" name="type">'."\n" .
		$liste .
		'</select>';
}

function propager_type($id, $type) {
	global $types;
	$branche = calcul_branche($id);
	//propager dans les rubriques mais la rubrique modifiee est deja modifiee
	$branche_rub = preg_replace('|^'.$id.',?|','',$branche);
	$type_rub = isset($types['rubrique'][$type]) ?
		$types['rubrique'][$type] :
		$type;
	$type_art = isset($types['article'][$type]) ?
		$types['article'][$type] :
		($type == 'rubrique' ? 'article' : 'article_'.$type);
	spip_log('--propagation de types:');
	spip_log($type_rub.' pour les rubriques '.$branche_rub);
	spip_log($type_art.' pour les articles des rubriques '.$branche);
	spip_log('--fin propagation');
	if($branche_rub) sql_updateq(
		table_objet_sql('rubrique'), 
		array(_TYPE => $type_rub),
		id_table_objet('rubrique').' IN ('.$branche_rub.')'
	);
	if($branche) sql_updateq(
		table_objet_sql('article'), 
		array(_TYPE => $type_art),
		'id_rubrique IN ('.$branche.')'
	);
}

?>
