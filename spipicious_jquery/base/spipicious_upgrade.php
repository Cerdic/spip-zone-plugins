<?php

/**
 * spip.icio.us
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 * Erational
 *
 * © 2007-2011 - Distribue sous licence GNU/GPL
 *
 */

$GLOBALS['spipicious_base_version'] = 0.6;

function spipicious_upgrade(){
	$version_base = $GLOBALS['spipicious_base_version'];
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta']['spipicious_base_version']) )
			|| (($current_version = $GLOBALS['meta']['spipicious_base_version'])!=$version_base)){
		include_spip('base/spipicious');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta('spipicious_base_version',$current_version=$version_base,'non');

			/**
			 * On crée un groupe de mots dédié qui servira à la configuration
			 * On active les mots clés dans le site si ce n'est déjà fait
			 * On active une configuration du plugin par défaut
			 */
			$titre_groupe = '- Tags -';
			$id_groupe = sql_getfetsel('id_groupe','spip_groupes_mots','titre='.sql_quote($titre_groupe));
			if(!$id_groupe){
				$id_groupe = sql_insertq('spip_groupes_mots',array('titre' => $titre_groupe));
			}
			if($GLOBALS['meta']['config_precise_groupes'] == 'non'){
				ecrire_meta('config_precise_groupes','oui','oui');
			}
			if($GLOBALS['meta']['articles_mots'] == 'non'){
				ecrire_meta('articles_mots','oui','oui');
			}
			$config_spipicious = array('people' => array('0minirezo'),'groupe_mot' => $id_groupe);
			ecrire_meta('spipicious',serialize($config_spipicious),'oui');
			echo _T('spipicious:message_installation_activation');
		}
		if($current_version<0.2){
			sql_alter("TABLE `spip_spipicious` ADD PRIMARY KEY (`id_mot`) ");
			sql_alter("TABLE `spip_spipicious` ADD KEY (`id_auteur`) ");
			sql_alter("TABLE `spip_spipicious` ADD maj timestamp AFTER position ");
			echo _T('spipicious:message_upgrade_database',array('version'=>0.2));
			ecrire_meta('spipicious_base_version',$current_version=0.2,'non');
		}
		if($current_version<0.3){
			sql_alter("TABLE `spip_spipicious` ADD id_rubrique bigint(21) NOT NULL AFTER`id_article` ");
			sql_alter("TABLE `spip_spipicious` ADD id_document bigint(21) NOT NULL AFTER`id_rubrique` ");
			echo _T('spipicious:message_upgrade_database',array('version'=>0.3));
			ecrire_meta('spipicious_base_version',$current_version=0.3,'non');
		}
		if($current_version<0.4){
			$desc = sql_showtable("spip_spipicious", true);
			if (isset($desc['PRIMARY KEY']['id_mot'])){
				sql_alter("TABLE `spip_spipicious` DROP PRIMARY KEY (`id_mot`) ");
				sql_alter("TABLE `spip_spipicious` ADD KEY (`id_mot`) ");
			}
			$desc_mots_docs = sql_showtable("spip_mots_documents", true);
			if(!isset($desc_mots_docs['field']['id_mot'])){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				echo "Creation de la table spip_mots_documents<br/>";
			}
			echo _T('spipicious:message_upgrade_database',array('version'=>0.4));
			ecrire_meta('spipicious_base_version',$current_version=0.4,'non');
		}
		if($current_version<0.5){
			sql_alter("TABLE `spip_spipicious` ADD id_syndic bigint(21) NOT NULL AFTER`id_document` ");
			sql_alter("TABLE `spip_spipicious` ADD id_evenement bigint(21) NOT NULL AFTER`id_syndic` ");
			echo _T('spipicious:message_upgrade_database',array('version'=>0.5));
			ecrire_meta('spipicious_base_version',$current_version=0.5,'non');
		}
		if($current_version<0.6){
			sql_alter("TABLE `spip_spipicious` ADD id_objet bigint(21) NOT NULL AFTER `id_auteur` ");
			sql_alter("TABLE `spip_spipicious` ADD objet VARCHAR (25) DEFAULT '' NOT NULL AFTER `id_objet` ");
			spipicious_id_objet_objet_upgrade();
			sql_alter("TABLE `spip_spipicious` DROP PRIMARY KEY");
			sql_alter("TABLE `spip_spipicious` ADD PRIMARY KEY (`id_mot`,`id_auteur`,`id_objet`,`objet`)");
			sql_alter("TABLE `spip_spipicious` DROP COLUMN `id_article`");
			sql_alter("TABLE `spip_spipicious` DROP COLUMN `id_document`");
			sql_alter("TABLE `spip_spipicious` DROP COLUMN `id_rubrique`");
			sql_alter("TABLE `spip_spipicious` DROP COLUMN `id_syndic`");
			sql_alter("TABLE `spip_spipicious` DROP COLUMN `id_evenement`");
			echo _T('spipicious:message_upgrade_database',array('version'=>0.6));
			ecrire_meta('spipicious_base_version',$current_version=0.6,'non');
		}

		ecrire_metas();
	}
}

function spipicious_vider_tables() {
	sql_drop_table("spip_spipicious");
	effacer_meta('spipicious_base_version');
	effacer_meta('spipicious');
	ecrire_metas();
}

function spipicious_install($action){
	$version_base = $GLOBALS['spipicious_base_version'];
	switch ($action){
		case 'test':
			return (isset($GLOBALS['meta']['spipicious_base_version']) AND ($GLOBALS['meta']['spipicious_base_version']>=$version_base));
			break;
		case 'install':
			spipicious_upgrade();
			break;
		case 'uninstall':
			spipicious_vider_tables();
			break;
	}
}

/**
 * Reunir en un seul champs id_objet/objet
 */
function spipicious_id_objet_objet_upgrade () {
	// Recopier les donnees avec le coupe id_objet / objet
	foreach (array('article', 'rubrique', 'document', 'evenement', 'syndic') as $liste => $l) {
		spip_log($l);
		$s = sql_select('*', 'spip_spipicious','id_'.$l.' > 0');
		while ($t = sql_fetch($s)) {
			$t['id_objet'] = $t["id_$l"];
			$t['objet'] = $l;
			sql_updateq('spip_spipicious',$t,'id_'.$l.' = '.intval($t['id_'.$l]).' AND id_mot='.intval($t['id_mot'].' AND id_auteur='.intval($t{'id_auteur'})));
		}
	}
}
?>