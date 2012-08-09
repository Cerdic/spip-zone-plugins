<?php
/**
 * Plugin Albums
 * Licence GNU/GPL
 * Base sur inc/marquer_doublons_doc.php du plugin 'medias'
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// On liste tous les champs susceptibles de contenir des albums
// la dist ne regarde que chapo et texte, on laisse comme ca, mais ca permet d'etendre a descriptif ou toto depuis d'autres plugins
$GLOBALS['albums_liste_champs'][] = 'texte';
$GLOBALS['albums_liste_champs'][] = 'chapo';
 
function inc_marquer_doublons_album_dist($champs,$id,$type,$id_table_objet,$table_objet,$spip_table_objet, $desc=array(), $serveur=''){
	$champs_selection=array();

	foreach ($GLOBALS['albums_liste_champs'] as $champs_choisis) {
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
	// charger le champ manquant en cas de modif partielle de l'objet
	// seulement si le champ existe dans la table demande

	$champs_a_traiter = "";
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
	include_spip('action/editer_liens');
	include_spip('base/objets');
	$modeles = lister_tables_objets_sql('spip_albums');
	$modeles = $modeles['modeles'];
	$GLOBALS['doublons_albums_inclus'] = array();
	$env = array(
		'objet' => $type,
		'id_objet' => $id,
		$id_table_objet => $id
	);
	traiter_modeles($champs_a_traiter,array('albums'=>$modeles),'','',null,$env); // detecter les doublons
	objet_qualifier_liens(array('album'=>'*'),array($type=>$id),array('vu'=>'non'));
	if (count($GLOBALS['doublons_albums_inclus'])){
		// on repasse par une requete sur spip_albums pour verifier que les albums existent bien !
		$in_liste = sql_in('id_album',$GLOBALS['doublons_albums_inclus']);
		$res = sql_allfetsel("id_album", "spip_albums", $in_liste);
		$res = array_map('reset',$res);
		// Creer le lien s'il n'existe pas deja
		objet_associer(array('album'=>$res),array($type=>$id),array('vu'=>'oui'));
		objet_qualifier_liens(array('album'=>$res),array($type=>$id),array('vu'=>'oui'));
	}
}

?>
