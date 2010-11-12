<?php
/**
* Plugin Notation 
* par JEM (jean-marc.viglino@ign.fr) / b_b / Matthieu Marcillaud
* 
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
*  
**/
if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Fonction pour commencer l'affichage de la page dans exec/
 * a virer en passant la conf depuis l'onglet CFG ?
 */
function notation_commencer_page(){

	$commencer_page = charger_fonction("commencer_page","inc");
	echo $commencer_page(_T('notation:notation'), "naviguer", "notation");

	include_spip('inc/autoriser');
	if (!autoriser('configurer')){
		// Pas d'acces
		echo debut_gauche("",true);
		echo debut_droite("",true);
		echo gros_titre("Plugin "._T('notation:notation'), "", false);
		return true;
	}

	// Informations
	echo debut_gauche("",true);	
	echo debut_droite("",true);

	// Afficher les onglets
	echo gros_titre("Plugin "._T('notation:notation'), "", false);

	return true;
}

/**
 * Retourne la configuration de la ponderation (defaut : 30)
 * @return int : valeur de ponderation
 */
function notation_get_ponderation(){
	static $ponderation="";
	if (!$ponderation) {
		include_spip('inc/config'); // lire_config
		$ponderation = lire_config('notation/ponderation',30);
		$ponderation = intval($ponderation);
		if ($ponderation < 1) $ponderation = 1;
	}
	return $ponderation;
}


/**
 * Nombre d'etoile a afficher en fonction de la configuration
 * du plugin. Varie de 1 a 10. Defaut 5.
 * 
 * @return int : nombre d'etoiles a afficher
 */ 
function notation_get_nb_notes(){
	static $nb = "";
	if (!$nb) {
		include_spip('inc/config'); // lire_config
		$nb = intval(lire_config('notation/nombre', 5));
		if ($nb < 1) $nb = 5;
		if ($nb > 10) $nb = 10;
	}
	return $nb;
}

/**
 *  Calcule de la note ponderee
 * 
 * @param float $note : note moyenne obtenue
 * @param int $nb : nombre de votes 
 * @return int : note ponderee en fonction de la configuration du plugin
 */
function notation_ponderee($note, $nb){
   $note_ponderee = round($note*(1-exp(-5*$nb/notation_get_ponderation())),2);
   return $note_ponderee;
}


function insert_notation(){
	return sql_insertq("spip_notations", array(
			"objet" => "",
			"id_objet" => 0,
			"id_auteur" => 0,
			"ip" => "",
			"note" => 0
			));
}

function modifier_notation($id_notation,$c=array()) {
	// pipeline pre edition
	sql_updateq('spip_notations',$c,'id_notation='.sql_quote($id_notation));
	// pipeline post edition
	return true;

}

function supprimer_notation($id_notation) {
	// pipeline pre edition
	sql_delete('spip_notations','id_notation='.sql_quote($id_notation));
	// pipeline post edition
	return true;
}


// je me demande vraiment si tout cela est utile...
// vu que tout peut etre calcule en requete depuis spip_notations
// a peu de choses pres (!)
function notation_recalculer_total($objet,$id_objet){

	list($total, $note, $note_ponderee) = notation_calculer_total($objet, $id_objet);

	$objet = objet_type($objet);

	// Mise a jour ou insertion ?
	if (!sql_countsel("spip_notations_objets", array(
				"objet=" . sql_quote($objet),
				"id_objet=" . sql_quote($id_objet),
				))) {
		// Remplir la table de notation des objets
		sql_insertq("spip_notations_objets", array(
			"objet" => $objet,
			"id_objet" => $id_objet,
			"note" => $note,
			"note_ponderee" => $note_ponderee,
			"nombre_votes" => $total
			));
		include_spip('inc/invalideur');
		suivre_invalideur("notation/$objet/$id_objet");

	} else {
		$anc_note_ponderee = sql_getfetsel('note_ponderee','spip_notations_objets',array(
				"objet=" . sql_quote($objet),
				"id_objet=" . sql_quote($id_objet)
			));
		// Mettre ajour dans les autres cas
		sql_updateq("spip_notations_objets", array(
			"note" => $note,
			"note_ponderee" => $note_ponderee,
			"nombre_votes" => $total),
			array(
				"objet=" . sql_quote($objet),
				"id_objet=" . sql_quote($id_objet)
			));
		// on optimise en n'invalidant que si la notre ponderee change (sinon ca ne se verra pas)
		if (round($anc_note_ponderee)!=$note_ponderee){
			include_spip('inc/invalideur');
			suivre_invalideur("notation/$objet/$id_objet");
		}
	}
}


function notation_calculer_total($objet, $id_objet){

	$ponderation = notation_get_ponderation();

	// Calculer les moyennes
	// cf critere {notation}
	$select = array(
		'notations.objet',
		'notations.id_objet',
		'COUNT(notations.note) AS nombre_votes',
		'ROUND(AVG(notations.note),2) AS moyenne',
		// *1.0 pour forcer une division reelle sinon 4/3=1 (sql server, sqlite...)
		'ROUND(AVG(notations.note)*(1-EXP(-5*COUNT(notations.note)*1.0/'.$ponderation.')),2) AS moyenne_ponderee'
	);
	if (!$row = sql_fetsel(
			$select,
			"spip_notations AS notations",
			array(
				"notations.objet=". sql_quote(objet_type($objet)),
				"notations.id_objet=" . sql_quote($id_objet)
			),'notations.id_objet')) {
		return array(0,0,0);
	} else {
		return array($row['nombre_votes'], $row['moyenne'], $row['moyenne_ponderee']);
	}
}

?>
