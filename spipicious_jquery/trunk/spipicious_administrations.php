<?php
/**
 * SPIP.icio.us
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Erational (http://www.erational.org)
 *
 * © 2007-2013 - Distribue sous licence GNU/GPL
 *
 * @package SPIP\SPIPicious\Installation
 */

/**
 * Installation/maj de la table spip_spipicious
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function spipicious_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_spipicious')),
		array('spipicious_creer_config','')
	);
	$maj['0.2'] = array(
		array('sql_alter',"TABLE `spip_spipicious` ADD PRIMARY KEY (`id_mot`) "),
		array('sql_alter',"TABLE `spip_spipicious` ADD KEY (`id_auteur`) "),
		array('sql_alter',"TABLE `spip_spipicious` ADD maj timestamp AFTER position "),
	);
	$maj['0.3'] = array(
		array('sql_alter',"TABLE `spip_spipicious` ADD id_rubrique bigint(21) NOT NULL AFTER`id_article` "),
		array('sql_alter',"TABLE `spip_spipicious` ADD id_document bigint(21) NOT NULL AFTER`id_rubrique` "),
	);
	$maj['0.5'] = array(
		array('sql_alter',"TABLE `spip_spipicious` ADD id_syndic bigint(21) NOT NULL AFTER`id_document`  "),
		array('sql_alter',"TABLE `spip_spipicious` ADD id_evenement bigint(21) NOT NULL AFTER`id_syndic` "),
	);
	$maj['0.6'] = array(
		array('sql_alter',"TABLE `spip_spipicious` ADD id_objet bigint(21) NOT NULL AFTER `id_auteur` "),
		array('sql_alter',"TABLE `spip_spipicious` ADD objet VARCHAR (25) DEFAULT '' NOT NULL AFTER `id_objet` "),
		array('spipicious_id_objet_objet_upgrade',array()),
		array('sql_alter',"TABLE `spip_spipicious` DROP PRIMARY KEY"),
		array('sql_alter',"TABLE `spip_spipicious` ADD PRIMARY KEY (`id_mot`,`id_auteur`,`id_objet`,`objet`)"),
		array('sql_alter',"TABLE `spip_spipicious` DROP COLUMN `id_article`"),
		array('sql_alter',"TABLE `spip_spipicious` DROP COLUMN `id_document`"),
		array('sql_alter',"TABLE `spip_spipicious` DROP COLUMN `id_rubrique`"),
		array('sql_alter',"TABLE `spip_spipicious` DROP COLUMN `id_syndic`"),
		array('sql_alter',"TABLE `spip_spipicious` DROP COLUMN `id_evenement`"),
	);
	
	$maj['0.6.1'] = array(
		array('spipicious_update_conf_mot',array()),
	);
	
	$maj['0.6.2'] = array(
		array('maj_tables',array('spip_spipicious')),
		array('spipicious_update_statuts_mots',array()),
	);

	$maj['0.6.3'] = array(
		array('sql_alter',"TABLE spip_spipicious ADD INDEX id_mot (id_mot)"),
		array('sql_alter',"TABLE spip_spipicious ADD INDEX id_objet (id_objet)"),
		array('sql_alter',"TABLE spip_spipicious ADD INDEX objet (objet)")
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Désinstallation/suppression de la table spip_spipicious
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 */
function spipicious_vider_tables($nom_meta_version_base) {
	sql_drop_table("spip_spipicious");
	effacer_meta($nom_meta_version_base);
	effacer_meta('spipicious');
}

/**
 * Fonction de création de configuration automatique
 * 
 * Crée un groupe de mots intitulé "- Tags -"
 * Active la configuration précise des groupes de mots si elle ne l'est pas
 * Active les mots sur les articles s'ils ne le sont pas
 * Crée la configuration de base avec le groupe de mot et les administrateurs pouvant ajouter des mots
 * 
 * @return void
 */
function spipicious_creer_config(){
	if(!($id_groupe = sql_getfetsel('id_groupe','spip_groupes_mots','titre='.sql_quote('- Tags -'))))
		$id_groupe = sql_insertq('spip_groupes_mots',array('titre' => '- Tags -','tables_liees' => 'articles','minirezo'=>'oui','comite'=>'oui','forum'=>'oui'));

	if($GLOBALS['meta']['config_precise_groupes'] == 'non')
		ecrire_meta('config_precise_groupes','oui','oui');

	if($GLOBALS['meta']['articles_mots'] == 'non')
		ecrire_meta('articles_mots','oui','oui');

	ecrire_meta('spipicious',serialize(array('people' => array('0minirezo'),'groupe_mot' => $id_groupe)),'oui');
}

/**
 * Change la configuration du groupe de mot si déjà configuré
 * 
 * @return void
 */
function spipicious_update_conf_mot(){
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	$id_groupe = lire_config('spipicious/groupe_mot');
	if(intval($id_groupe) > 0)
		sql_updateq('spip_groupes_mots',array('tables_liees' => 'articles','minirezo'=>'oui','comite'=>'oui','forum'=>'oui'),'id_groupe='.$id_groupe);
}

/**
 * Fonction d'upgrade : vérifier les status des spipicious
 * 
 * @return void
 */
function spipicious_update_statuts_mots(){
	$spipicious = sql_select('*','spip_spipicious');
	while($iter = sql_fetch($spipicious)){
		$table = table_objet_sql($iter['objet']);
		$id_table_objet = id_table_objet($iter['objet']);
		$objet = sql_fetsel('*',$table,$id_table_objet.'='.intval($iter['id_objet']));
		if(isset($objet['statut']) && $objet['statut'] != 'publie'){
			sql_updateq('spip_spipicious',array('statut'=>'prop'),'id_objet='.intval($iter['id_objet'].' AND objet='.sql_quote($iter['objet'])));
		}else if(!is_array($objet)){
			sql_updateq('spip_spipicious',array('statut'=>'prop'),'id_objet='.intval($iter['id_objet'].' AND objet='.sql_quote($iter['objet'])));
		}
	}	
}

/**
 * Fonction d'upgrade : réunir en un seul champs id_objet/objet
 * 
 * @return void
 */
function spipicious_id_objet_objet_upgrade () {
	// Recopier les donnees avec le coupe id_objet / objet
	foreach (array('article', 'rubrique', 'document', 'evenement', 'syndic') as $liste => $l) {
		$s = sql_select('*', 'spip_spipicious','id_'.$l.' > 0');
		while ($t = sql_fetch($s)) {
			$t['id_objet'] = $t["id_$l"];
			$t['objet'] = $l;
			sql_updateq('spip_spipicious',$t,'id_'.$l.' = '.intval($t['id_'.$l]).' AND id_mot='.intval($t['id_mot'].' AND id_auteur='.intval($t{'id_auteur'})));
		}
	}
}
?>