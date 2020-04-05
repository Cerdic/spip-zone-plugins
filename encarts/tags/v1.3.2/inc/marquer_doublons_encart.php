<?php
/*
 * (issu du plugin mediatheque)
 * (c) 2009 cedric
 * Distribue sous licence GPL
 * modifie par marcimat.
 */

// On liste tous les champs susceptibles de contenir des encarts si on veut que ces derniers soient lies a l objet lorsqu on y fait reference par encartXX
// la dist ne regarde que chapo et texte, on laisse comme ca, mais ca permet d etendre a descriptif ou toto depuis d autre plugin comme agenda ou grappe
$GLOBALS['encarts_liste_champs'][] = 'texte';
$GLOBALS['encarts_liste_champs'][] = 'chapo';
 

function inc_marquer_doublons_encart_dist($champs,$id,$type,$id_table_objet,$table_objet,$spip_table_objet, $desc=array(), $serveur=''){
	$champs_selection=array();
		foreach ($GLOBALS['encarts_liste_champs'] as $champs_choisis) {
			if ( isset($champs[$champs_choisis]) )
			array_push($champs_selection,$champs_choisis);
		}
	if (count($champs_selection) == 0)
		return;
	if (!$desc){
		$trouver_table = charger_fonction('trouver_table', 'base');
		$desc = $trouver_table($table_objet, $serveur);
	}
	$load = "";
	// charger le champ manquant en cas de modif partielle de l	'objet
	// seulement si le champ existe dans la table demande
	
		foreach ($champs_selection as $champs_a_parcourir) {
			if (isset($desc['field'][$champs_a_parcourir])) {
			$load = $champs_a_parcourir;
			$champs_a_traiter .= $champs[$champs_a_parcourir];
			}
		}	

	if ($load){
		$champs[$load] = "";
		$row = sql_fetsel($load, $spip_table_objet, "$id_table_objet=".sql_quote($id));
		if ($row AND isset($row[$load]))
			$champs[$load] = $row[$load];
	}
	include_spip('inc/texte');
	include_spip('base/abstract_sql');
	$GLOBALS['doublons_encarts_inclus'] = array();
	traiter_modeles($champs_a_traiter, array('encarts'=>array('encart'))); // detecter les doublons
	sql_updateq("spip_encarts_liens", array("vu" => 'non'), "id_objet=$id AND objet=".sql_quote($type));
	if (count($GLOBALS['doublons_encarts_inclus'])){
		// on repasse par une requete sur spip_encarts pour verifier que les encarts existent bien !
		$in_liste = sql_in('id_encart',
			$GLOBALS['doublons_encarts_inclus']);
		$res = sql_select("id_encart", "spip_encarts", $in_liste);
		while ($row = sql_fetch($res)) {
			// Creer le lien s'il n'existe pas deja
			sql_insertq("spip_encarts_liens", array('id_objet'=>$id, 'objet'=>$type, 'id_encart' => $row['id_encart'], 'vu' => 'oui'));
			sql_updateq("spip_encarts_liens", array("vu" => 'oui'), "id_objet=$id AND objet=".sql_quote($type)." AND id_encart=" . $row['id_encart']);
		}
	}
}

?>
