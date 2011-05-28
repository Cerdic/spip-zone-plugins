<?php
/**
 * Plugin tradrub
 * Licence GPL (c) 2008-2010 Stephane Laurent (Bill), Matthieu Marcillaud
 * 
 */

function formulaires_definir_traduction_charger_dist($objet, $id_objet=0) {
	// pas d'element, pas de traduction
	if (!$objet or !($id_objet = intval($id_objet))) {
		return false;
	}

	$table = table_objet_sql($objet);
	$_id_objet = id_table_objet($objet);
	$id_trad = sql_getfetsel('id_trad', $table, "$_id_objet = ". sql_quote($id_objet));
	
	$env = array(
		'objet' => $objet,
		'table' => $table,
		'_id_objet' => $_id_objet,
		'id_objet' => $id_objet,
		'id_trad' => $id_trad,
	);
	
	if ($id_trad and ($id_trad != $id_objet)) {
		$env['ids_trad_selecteur'] = "$objet|$id_trad";
	}
	
	return $env;
}


function formulaires_definir_traduction_traiter_dist($objet, $id_objet=0) {

	if (!$id_objet = intval($id_objet)) {
		spip_log("Incomprehensible : definir_traduction_traiter n'a pas de id_objet : $id_objet !");
		return array(
			'message_erreur'=>"Identifiant de cette rubrique non transmis dans le traitement !!",
			'editable'=>true
		);
	}
	$table = table_objet_sql($objet);
	$_id_objet = id_table_objet($objet);
	include_spip('spip_bonux_fonctions');
	$id_trad = array_shift(picker_selected(_request('ids_trad_selecteur'), $objet));
	include_spip('inc/modifier');
	// deux cas deja ici :
	// id_trad > 0, on lie une rubrique
	// id_trad = 0 : on delie
	
	$id_trad_old = sql_getfetsel('id_trad', $table, "$_id_objet = " . sql_quote($id_objet));
	if ($id_trad_old != $id_trad) {
		include_spip('inc/modifier');
		modifier_contenu($objet, $id_objet, array('invalideur' => "id='$objet/$id_objet'"), array(
			'id_trad' => $id_trad
		));

		if ($id_trad) {
			// indiquer que l'element d'origine possede des traductions...
			if (!$id_trad_source = sql_getfetsel('id_trad', $table, 'id_rubrique=' . sql_quote('id_trad') )) {	
				modifier_contenu($objet, $id_trad, array('invalideur' => "id='$objet/$id_trad'"), array(
					'id_trad' => $id_trad
				));
			}
		}
		
		// si la deliaison ou reliaison fait qu'il ne reste plus que la source
		// dans le groupe de traduction on lui remet l'id_trad a 0
		if ($id_trad_old) {
			if (1 == $nb_dans_groupe = sql_countsel($table, array('id_trad = ' . sql_quote($id_trad_old)))) {
				modifier_contenu($objet, $id_trad_old, array('invalideur' => "id='$objet/$id_trad_old'"), array(
					'id_trad' => 0
				));				
			}
		}
		
	}
	return array(
		'message_ok' => _T('tradrub:enregistrement_de_la_traduction_ok'),
		'editable' => true,
	);
}

?>
