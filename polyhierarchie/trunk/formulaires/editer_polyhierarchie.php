<?php
/*
 * Plugin Polyhierarchie
 * (c) 2009-2010 Cedric Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_editer_polyhierarchie_charger($objet, $id_objet, $retour=''){
	
	$valeurs['objet'] = $objet;
	$valeurs['id_objet'] = intval($id_objet);
	
	$table_objet = table_objet_sql($objet);
	$id_table_objet = id_table_objet($objet);
	
	if ($objet=='article')
		$id_parent = sql_getfetsel('id_rubrique',$table_objet,$id_table_objet.'='.intval($id_objet));
		
	if ($objet=='rubrique')
		$id_parent = sql_getfetsel('id_parent',$table_objet,$id_table_objet.'='.intval($id_objet));

	// On ne fait rien si l'id_parent principal est incoherent (exemple : compat pages uniques)
	if ($id_parent < 0)
		$valeurs['editable'] = false;
	
	$valeur['id_parent'] = $id_parent;

	// on met en tete l'id_parent principal
	// pour unifier la saisie
	$valeurs['parents'] = array("rubrique|".$id_parent);

	include_spip('inc/polyhier');
	$parents = polyhier_get_parents($id_objet,$objet,$serveur='');
	foreach($parents as $p)
		$valeurs['parents'][] = "rubrique|$p";
	
	return $valeurs;
}

function formulaires_editer_polyhierarchie_verifier($objet, $id_objet, $retour=''){
	
	$erreurs = array();

	if ($objet = _request('_polyhier')
		AND in_array($objet,array('article','rubrique'))){
		// On ne fait rien si l'id_parent principal est incoherent (exemple : compat pages uniques)
		if (_request('id_parent') < 0) return $erreurs;
		
		$id_table_objet = id_table_objet($objet);

		// on verifie qu'au moins un parent est present si c'est un article
		if (!count(_request('parents')) AND $objet=='article'){
			$erreurs['parents'] = _T('polyhier:parent_obligatoire');
			set_request('parents',array()); // eviter de revenir au choix initial
		}
		// sinon, c'est ok, on rebascule le premier parent[] dans id_parent
		// ou on est a la racine..
		else {
			$id_parent = _request('parents');
			$id_parent = explode('|',is_array($id_parent)?reset($id_parent):"rubrique|0");
			set_request('id_parent',intval(end($id_parent)));
		}

	}
	return $erreurs;
}

function formulaires_editer_polyhierarchie_traiter($objet, $id_objet, $retour=''){

	$message = array('editable'=>true, 'message_ok'=>'');
	
	$serveur = '';
	$id_table_objet = id_table_objet($objet);
	$id_parents = _request('parents');
	$id_parent = _request('id_parent');
	if (!$id_parents)
		$id_parents = array();

	$ids = array();
	foreach($id_parents as $sel){
		$sel = explode("|",$sel);
		if (reset($sel)=='rubrique')
			$ids[] = intval(end($sel));
	}
	$id_parents = array_diff($ids,array($id_parent));

	include_spip('inc/polyhier');
	polyhier_set_parents($id_objet,$objet,$id_parents,$serveur);
	
	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='$id_table_objet/$id_table_objet'");
	
	if ($retour) {
		include_spip('inc/headers');
		$message .= redirige_formulaire($retour);
	}

	return $message;
}

?>
