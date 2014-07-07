<?php
/**
 * Mettre à jour les liens des albums pour un objet
 *
 * @note
 * Pompé sur inc/marquer_doublons_doc.php du plugin Médias
 *
 * @plugin     Albums
 * @copyright  2014
 * @author     Romy Tetue, Charles Razack
 * @licence    GPL
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// On liste tous les champs susceptibles de contenir des albums si on veut que ces derniers soient liés a l'objet lorsqu on y fait reference par <albumXX>
// la dist ne regarde que chapo et texte, on laisse comme ça, mais ca permet d'étendre à descriptif ou toto depuis d'autres plugins
$GLOBALS['albums_liste_champs'][] = 'texte';
$GLOBALS['albums_liste_champs'][] = 'chapo';

/**
 * Mettre à jour les liens des albums pour un objet
 *
 * @param $champs              Couples Champs / valeur de l'objet
 * @param $id                  Identifiant de l'objet
 * @param $type                Type d'objet (article)
 * @param $id_table_objet      clé primaire de l'objet (id_article)
 * @param $table_objet         Nom de l'objet (articles)
 * @param $table_objet_sql     Nom de la table de l'objet (spip_articles)
 * @param $desc                Description de la table de l'objet
 * @param $serveur             Nom du connecteur
 * @return void
 */
function inc_marquer_doublons_album_dist($champs,$id,$type,$id_table_objet,$table_objet,$table_objet_sql,$desc=array(),$serveur=''){
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
		$row = sql_fetsel($load, $table_objet_sql, "$id_table_objet=".sql_quote($id));
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
	traiter_modeles($champs_a_traiter,array('albums'=>$modeles),'','',null,$env); // détecter les doublons
	objet_qualifier_liens(array('album'=>'*'),array($type=>$id),array('vu'=>'non'));
	if (count($GLOBALS['doublons_albums_inclus'])){
		// on repasse par une requete sur spip_albums pour verifier que les albums existent bien
		$in_liste = sql_in('id_album',$GLOBALS['doublons_albums_inclus']);
		$res = sql_allfetsel("id_album", "spip_albums", $in_liste);
		$res = array_map('reset',$res);
		// créer le lien s'il n'existe pas deja
		objet_associer(array('album'=>$res),array($type=>$id),array('vu'=>'oui'));
		objet_qualifier_liens(array('album'=>$res),array($type=>$id),array('vu'=>'oui'));
	}
}

?>
