<?php
/**
 * Plugin Tickets
 * Licence GPL (c) 2008-2013
 *
 * @package SPIP\Tickets\Options
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Activer le plugin no_spam sur les tickets
 */
$GLOBALS['formulaires_no_spam'][] = 'editer_ticket';
// Liste des pages de configuration dans l'ordre de presentation
define('_TICKETS_PAGES_CONFIG', 'general:autorisations');

/**
 * Récupérer dans la base la liste des mots-clés liés au ticket
 * http://marcimat.magraine.net/Crayon-de-mots-pour-un-article
 */
function valeur_champ_mots_ticket($table, $id, $champ) {
	return valeur_champ_mots_objet($table, $id, $champ);
}

/**
 * Récupérer dans la base la liste des mots-clés liés au ticket pour
 * groupe de mots donné
 * http://marcimat.magraine.net/Crayon-de-mots-pour-un-article
 */
function valeur_champ_groupemots_ticket($table, $id, $champ) {
	return valeur_champ_mots_objet($table, $id, $champ);
}

/**
 * Récupérer dans la base la liste des mots-clés liés à l'objet
 * http://marcimat.magraine.net/Crayon-de-mots-pour-un-article
 */
function valeur_champ_mots_objet($table, $ids, $champ) {
	list($id_objet, $id_groupe) = explode('-', $ids);
	list(, $objet) = explode('_', $champ);

	$where = array(
		"m.id_mot = ml.id_mot",
		"ml.id_objet=".sql_quote($id_objet),
		"ml.objet=".sql_quote($objet)
	);
	if ($id_groupe > 0)
		$where[] = "m.id_groupe=" . sql_quote($id_groupe);

	$valeurs = sql_allfetsel("m.id_mot", "spip_mots AS m, spip_mots_liens AS ml", $where);
	$valeurs = array_map('array_shift', $valeurs);

	return $valeurs;
}

/**
 * Modifier dans la base la liste des mots-clés liés au ticket
 * http://marcimat.magraine.net/Crayon-de-mots-pour-un-article
 */
function mots_ticket_revision($id, $colonnes, $type_objet) {
	return mots_objet_revision($id, $colonnes, $type_objet, 'mots_ticket');
}

/**
 * Modifier dans la base la liste des mots-clés liés au ticket pour un
 * groupe de mots donné
 * http://marcimat.magraine.net/Crayon-de-mots-pour-un-article
 */
function groupemots_ticket_revision($id, $colonnes, $type_objet) {
	return mots_objet_revision($id, $colonnes, $type_objet, 'groupemots_ticket');
}

/**
 * Modifier dans la base la liste des mots-clés liés à l'objet
 * http://marcimat.magraine.net/Crayon-de-mots-pour-un-article
 */
function mots_objet_revision($ids, $colonnes, $type_objet, $champ = '') {
	if (!$champ) return false;

	list($id_objet, $id_groupe) = explode('-', $ids);
	list(, $type_liaison) = explode('_', $champ);

	/* On vérifie qu'on a le droit d'associer des mots l'objet
	 * il serait mieux de le vérifier pour chaque groupe de mots (voir
	 * les options de la fonction autoriser_associermots_dist)
	 */
	if (!autoriser('associermots',$type_liaison,$id_objet)) return false;

	// actuellement en bdd
	$old = valeur_champ_mots_objet($type_objet, $ids, $champ);
	// ceux qu'on veut maintenant (on vérifie que ce sont des indices)
	$new = array_filter(explode(',', $colonnes[$champ]),'is_numeric');
	// les mots à supprimer
	$del = array_diff($old, $new);
	// les mots à ajouter
	$add = array_diff($new, $old);

	include_spip('action/editer_liens');
	if ($del) {
		objet_dissocier(array('mot'=>$del), array($type_liaison => $id_objet));
	}
	if ($add) {
		objet_associer(array('mot'=>$add), array($type_liaison => $id_objet));
	}

	return true;
}

function ticket_id_assigne_revision($id, $colval = array(), $type = ''){
	$a = crayons_update($id, $colval, $type);

	if ($notifications = charger_fonction('notifications', 'inc')) {
		foreach ($colval as $col => $val) {
			if ($col=="id_assigne") {
				$notifications('assignerticket', $id, array('id_auteur' => $val));
			}
		}
	}
	
	return $a;
}


/*
 * Migrer 7 champs de la table spip_tickets vers des groupes de mots-clés
 * 
 * Le résultat de la migration est stocké dans une meta au cas où :
 * 
 * tickets/migration_180/champs/severite/id_groupe (int)
 *                                      /erreur_groupe (str)
 *                                      /valeurs/1/id_mot (int)
 *                                                /erreur_mot (str)
 */
function migrer_champs_vers_mots_cles() {
	include_spip('inc/config');
	include_spip('action/editer_groupe_mots');
	include_spip('action/editer_mot');
	$trouver_table = charger_fonction('trouver_table','base');
	$desc = $trouver_table(table_objet_sql('ticket'));
	if (!$desc OR !array_key_exists('field',$desc))
		return;
	else
		$field = $desc['field'];

	$a_migrer = array(
		'severite'=>array('champs_groupe'=>array('titre'=>_T('tickets:champ_severite_th'),'unseul'=>'oui','obligatoire'=>'oui')),
		'tracker'=>array('champs_groupe'=>array('titre'=>_T('tickets:champ_type_th'),'unseul'=>'oui','obligatoire'=>'oui')),
		'navigateur'=>array('champs_groupe'=>array('titre'=>_T('tickets:champ_navigateur'),'unseul'=>'non','obligatoire'=>'non')),
		'projet'=>array('champs_groupe'=>array('titre'=>_T('tickets:champ_projet_th'),'unseul'=>'oui','obligatoire'=>'non'),'meta'=>'tickets/general/projets'),
		'composant'=>array('champs_groupe'=>array('titre'=>_T('tickets:champ_composant_th'),'unseul'=>'oui','obligatoire'=>'non'),'meta'=>'tickets/general/composants'),
		'version'=>array('champs_groupe'=>array('titre'=>_T('tickets:champ_version_th'),'unseul'=>'oui','obligatoire'=>'non'),'meta'=>'tickets/general/versions'),
		'jalon'=>array('champs_groupe'=>array('titre'=>_T('tickets:champ_jalon_th'),'unseul'=>'oui','obligatoire'=>'non'),'meta'=>'tickets/general/jalons')
		);

	// pour chaque champ
	spip_log("**** migration 1.8.0 - début ****", "tickets");
	foreach ($a_migrer as $k=>$v) {
		// est-ce que la colonne existe encore ?
		if (!array_key_exists($k,$field))
			continue;

		// récupérer la liste de valeurs du champ (base de données ou code)
		$valeurs = array();
		$f = 'tickets_liste_'.$k;
		if (function_exists($f))
			$valeurs = $f();
		if (count($valeurs)) {
			// si non vide, créer le groupe de mots
			// on regarde dans la meta si on a déjà migré ce champ
			$meta = 'tickets/migration_180/champs/'.$k.'/id_groupe';
			$meta_err = 'tickets/migration_180/champs/'.$k.'/erreur_groupe';
			if (!intval($id_groupe = lire_config($meta))) {
				$v['champs_groupe'] = array_merge($v['champs_groupe'], array('tables_liees'=>'tickets','comite'=>'non','forum'=>'non','minirezo'=>'oui'));
				$id_groupe = groupemots_inserer();
				if ($id_groupe>0 AND $err = groupemots_modifier($id_groupe, $v['champs_groupe'])) {
					ecrire_config($meta_err,'Erreur - '.$err);
					continue;
				}
				ecrire_config($meta,intval($id_groupe));
				spip_log(" champ '".$k."' - création du groupe id_groupe = ".$id_groupe, "tickets");
			} else {
				spip_log(" champ '".$k."' - groupe id_groupe = ".$id_groupe." déjà créé", "tickets");
			}
			// créer un mot-clé pour chaque valeur
			foreach ($valeurs as $kv=>$vv) {
				// on regarde si on a déjà migré ce mot
				$meta = 'tickets/migration_180/champs/'.$k.'/valeurs/'.$kv.'/id_mot';
				$meta_err = 'tickets/migration_180/champs/'.$k.'/valeurs/'.$kv.'/erreur_mot';
				if (!intval($id_mot = lire_config($meta))) {
					$id_mot = mot_inserer($id_groupe);
					if ($id_mot>0 AND $err = mot_modifier($id_mot, array('titre'=>$vv))) {
						ecrire_config($meta_err,'Erreur - '.$err);
						continue;
					}
					ecrire_config($meta,intval($id_mot));
					spip_log("   valeur '".$kv."' - création du mot id_mot = ".$id_mot, "tickets");
				} else {
					spip_log("   valeur '".$kv."' - mot id_mot = ".$id_mot." déjà créé", "tickets");
				}
				// lier les tickets correspondant à ce mot-clé
				$ids_tickets = array_map('array_shift',sql_allfetsel(id_table_objet('ticket'), table_objet_sql('ticket'), $k."='".$kv."'"));
				$nb_lies = mot_associer($id_mot, array('ticket'=>$ids_tickets));
				spip_log('     '.$nb_lies.' tickets liés','tickets');
			}
		}
		// supprimer la configuration du champ dans spip_meta
		if (array_key_exists('meta',$v)) {
			effacer_config($v['meta']);
			spip_log('   valeurs du champs "'.$k.'" supprimées dans la meta "'.$v['meta'].'"','tickets');
		}
		// supprimer la colonne de la table spip_tickets
		sql_alter("TABLE ".table_objet_sql('ticket')." DROP ".$k);
		spip_log('   colonne "'.$k.'" supprimée','tickets');
	}
	spip_log("**** migration 1.8.0 - fin ****", "tickets");
}

function nettoyer_migration_champs_vers_mots_cles() {
	include_spip('inc/autoriser');
	include_spip('action/editer_mot');

	$c = lire_config('tickets/migration_180/champs', array());
	$supprimer_groupe_mots = charger_fonction('supprimer_groupe_mots','action');

	spip_log("**** nettoyage de la migration 1.8.0 - début ****", "tickets");
	foreach ($c as $k=>$v) {
		$meta = 'tickets/migration_180/champs/'.$k.'/valeurs';
		if (is_array($valeurs = lire_config($meta))) {
			foreach ($valeurs as $kv=>$vv) {
				$meta = 'tickets/migration_180/champs/'.$k.'/valeurs/'.$kv.'/id_mot';
				if (intval($id_mot = lire_config($meta))) {
					spip_log("   valeur '".$kv."' - suppression du mot id_mot=".$id_mot,"tickets");
					mot_supprimer($id_mot);
					effacer_config('tickets/migration_180/champs/'.$kv.'/valeurs/'.$kv);
				}
			}
		}
		$meta = 'tickets/migration_180/champs/'.$k.'/id_groupe';
		if (intval($id_groupe = lire_config($meta))) {
			spip_log(" champ '".$k."' - suppression de groupe id_groupe=".$id_groupe,"tickets");
			$supprimer_groupe_mots($id_groupe);
		}
		effacer_config('tickets/migration_180/champs/'.$k);
	}
	effacer_config('tickets/migration_180/champs');
	effacer_config('tickets/migration_180');
	spip_log("**** nettoyage de la migration 1.8.0 - fin ****", "tickets");
}

function tickets_liste_projet(){
	return tickets_liste_champ('_TICKETS_LISTE_PROJETS','tickets/general/projets');
}
function tickets_liste_composant(){
	return tickets_liste_champ('_TICKETS_LISTE_COMPOSANTS','tickets/general/composants');
}
function tickets_liste_version(){
	return tickets_liste_champ('_TICKETS_LISTE_VERSIONS','tickets/general/versions');
}
function tickets_liste_jalon(){
	return tickets_liste_champ('_TICKETS_LISTE_JALONS','tickets/general/jalons');
}
function tickets_liste_champ($constante,$meta){
	$liste = array();
	if (defined($constante) OR lire_config($meta)) {
		if (defined($constante))
			$liste = explode(":", constant($constante));
		else
			$liste = explode(":", lire_config($meta));

		$liste = array_filter(array_map('trim',$liste));
		$liste = array_combine($liste, $liste);
	}
	return $liste;
}

?>
