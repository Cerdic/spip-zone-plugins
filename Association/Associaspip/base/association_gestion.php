<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James                     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

global $association_tables_principales, $association_tables_auxiliaires;
include_spip('base/association');
include_spip('base/abstract_sql');

// A chaque modif de la base SQL ou ses conventions (raccourcis etc)
// le fichier plugin.xml doit indiquer le numero de depot qui l'implemente sur
// http://zone.spip.org/trac/spip-zone/timeline
// Ce numero est fourni automatiquement par la fonction spip_plugin_install
// lors de l'appel des fonctions de ce fichier.

// desinstatllation

function association_vider_tables($nom_meta, $table){
	global $association_tables_principales, $association_tables_auxiliaires;
	effacer_meta($nom_meta, $table);
	foreach($association_tables_principales as $nom => $desc)
		sql_drop_table($nom);
	foreach($association_tables_auxiliaires as $nom => $desc)
		sql_drop_table($nom);
	spip_log("$table $nom_meta desinstalle");
}

// MAJ des tables de la base SQL
// Retourne 0 si ok, le dernier numero de MAJ ok sinon

function association_upgrade($meta, $courante, $table='meta')
{
  // Compatibilite: le nom de la meta donnant le numero de version
  // n'etait pas std puis est parti dans une autre table puis encore une autre

	if (!isset($GLOBALS['association_metas']['base_version'])) {
		lire_metas('asso_metas');
		if (isset($GLOBALS['asso_metas']['base_version'])) {
			$n = $GLOBALS['asso_metas']['base_version'];
		} elseif (isset($GLOBALS['meta']['association_base_version'])) {
			$n = $GLOBALS['meta']['association_base_version'];
		} else $n = 0;
		$GLOBALS['association_metas']['base_version'] = $n;
	} else $n = $GLOBALS['association_metas']['base_version'];
	effacer_meta('association_base_version');
	spip_log("association upgrade: $table $meta = $n =>> $courante");
	if (!$n) {
		include_spip('base/create');
		alterer_base($GLOBALS['association_tables_principales'],
			     $GLOBALS['association_tables_auxiliaires']);
		ecrire_meta($meta, $courante, NULL, $table);
		return 0; // Reussite (supposee !)
	} else {
	// compatibilite avec les numeros de version non entiers
		$installee = ($n > 1) ? $n : ($n * 100);
		$GLOBALS['association_maj_erreur'] = 0;
		if ($courante > $installee) {
			include_spip('base/upgrade');
			$n = maj_while($installee, $courante, $GLOBALS['association_maj'], $meta, $table);
			$n = $n ? $n[0] : $GLOBALS['association_maj_erreur'];
			// signaler que les dernieres MAJ sont a refaire
			if ($n) ecrire_meta($meta, $n-1, $table);
		}
		return $GLOBALS['association_maj_erreur'];
	}
}

$GLOBALS['association_maj'][21] = array(array('sql_alter',"TABLE spip_asso_adherents ADD publication text NOT NULL AFTER secteur"));

$GLOBALS['association_maj'][30] = array(
	array('sql_drop_table', "spip_asso_bienfaiteurs"),
	array('sql_drop_table', "spip_asso_financiers")
					);

$GLOBALS['association_maj'][40] = array(
	array('sql_alter',"TABLE `spip_asso_comptes` ADD `valide` TEXT NOT NULL AFTER `id_journal` "));
		
$GLOBALS['association_maj'][50] = array(
	array('sql_alter',"TABLE spip_asso_activites ADD membres TEXT NOT NULL AFTER accompagne, ADD non_membres TEXT NOT NULL AFTER membres "));
		
$GLOBALS['association_maj'][60] = array(array('sql_drop_table', "spip_asso_profil"));
		
$GLOBALS['association_maj'][61] = array(
	array('spip_query',"RENAME TABLE spip_asso_banques TO spip_asso_plan"),
	array('sql_drop_table',"spip_asso_livres")
					);
$GLOBALS['association_maj'][62] = array(array('sql_alter',"TABLE spip_asso_plan ADD actif TEXT NOT NULL AFTER commentaires"));

$GLOBALS['association_maj'][63] = array(array('sql_alter',"TABLE spip_asso_ventes ADD id_acheteur BIGINT(20) NOT NULL AFTER acheteur"));
		
function association_maj_64(){

	if (_ASSOCIATION_AUTEURS_ELARGIS == 'spip_auteurs_elargis') {
		sql_alter("TABLE spip_auteurs_elargis ADD validite date NOT NULL default '0000-00-00'");
		sql_alter("TABLE spip_auteurs_elargis ADD montant float NOT NULL default '0'");
		sql_alter("TABLE spip_auteurs_elargis ADD date date NOT NULL default '0000-00-00' ");
	} else {
		if (_ASSOCIATION_INSCRIPTION2) {
			if (!$GLOBALS['association_maj_erreur']) $GLOBALS['association_maj_erreur'] = 64;
			return;
		}
		// Simulation provisoire
		@sql_alter("TABLE spip_asso_adherents ADD commentaire text NOT NULL default ''");
		@sql_alter("TABLE spip_asso_adherents ADD statut_interne text NOT NULL default '' ");
		@sql_alter("TABLE spip_asso_adherents CHANGE COLUMN nom nom_famille text DEFAULT '' NOT NULL");
	}
}

$GLOBALS['association_maj'][64] = array(array('association_maj_64'));

// Recopie des metas geree par CFG dans la table asso_meta
// Il faut charger a la main ses fichiers puisque plugin.xml ne le demande plus

function association_maj_38192()
{
	global $association_tables_auxiliaires;

	if (sql_create('spip_asso_metas', 
		$association_tables_auxiliaires['spip_asso_metas']['field'],
		$association_tables_auxiliaires['spip_asso_metas']['key'],
		false, false)) {
		include _DIR_PLUGINS . 'cfg/inc/cfg.php';
		if (is_array($c = lire_config('association'))) {
			foreach($c as $k => $v) {
				ecrire_meta($k, $v, 'oui', 'association_metas');
			}
			// effacer les vieilles meta
			effacer_meta('association');
			effacer_meta('asso_base_version');
			effacer_meta('association_base_version');
		}
	} else spip_log("maj_38190: echec de  la creation de spip_asso_metas");
}

$GLOBALS['association_maj'][38192] = array(array('association_maj_38192'));

$GLOBALS['association_maj'][38258] = array(array('sql_create','spip_asso_membres',
		$association_tables_principales['spip_asso_membres']['field'],
	      $association_tables_principales['spip_asso_membres']['key'])
					);
$GLOBALS['association_maj'][38578] = array(
	array('spip_query', 'rename table spip_asso_metas TO spip_association_metas'));

function association_maj_42024()
{
	sql_alter("TABLE spip_asso_comptes ADD vu BOOLEAN default 0");
	sql_update('spip_asso_comptes', array('vu' => 1), "valide='oui'");
	sql_alter("TABLE spip_asso_comptes DROP valide");
}

$GLOBALS['association_maj'][42024] = array(array('association_maj_42024'));

function association_maj_43909()
{
	global $association_tables_principales;

	sql_alter("TABLE spip_asso_plan ADD destination ENUM('credit','debit') NOT NULL default 'credit'");
	sql_create('spip_asso_destination', 
		$association_tables_principales['spip_asso_destination']['field'],
		$association_tables_principales['spip_asso_destination']['key']);
	sql_create('spip_asso_destination_op', 
		$association_tables_principales['spip_asso_destination_op']['field'],
		$association_tables_principales['spip_asso_destination_op']['key']);
}

$GLOBALS['association_maj'][43909] = array(array('association_maj_43909'));

?>
