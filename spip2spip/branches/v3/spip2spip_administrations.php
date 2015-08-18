<?php
/**
 * Plugin Spip2spip
 * 
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function spip2spip_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
 
  $maj['create'] = array(
	   array('maj_tables', array('spip_spip2spips')),
     array('sql_alter','TABLE spip_articles ADD s2s_url VARCHAR(255) DEFAULT \'\' NOT NULL'),
	   array('sql_alter','TABLE spip_articles ADD s2s_url_trad VARCHAR(255) DEFAULT \'\' NOT NULL'),
     array('spip2spip_create'),     
	);
  
  // pour la migration venant de SPIP 2 : renommer champs id et le nom de la table
  $maj['1.1'] = array( 		
    array('sql_alter',"TABLE spip_spip2spip CHANGE `id` `id_spip2spip` BIGINT( 21 ) NOT NULL AUTO_INCREMENT"),
    array('sql_alter',"TABLE spip_spip2spip CHANGE `last_syndic` `maj` TIMESTAMP"),
    array('sql_alter',"TABLE spip_spip2spip RENAME spip_spip2spips"),        
	); 
 
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


// creation des mots-clés de travail "- spip2spip - "
function  spip2spip_create() { 
  	sql_insertq("spip_groupes_mots", array(
                                         'titre' => '- spip2spip -' , 
                                         'descriptif' => _T('spip2spip:install_spip2spip_4'),
                                         'texte' =>  _T('spip2spip:install_spip2spip_5'),
                                         'unseul' => 'non',
                                         'obligatoire' => 'non',
                                         'tables_liees' => 'articles,rubriques',
                                         'minirezo' => 'oui',
                                         'comite' => 'oui' ,
                                         'forum' => 'non' ,
                                         'maj' => 'NOW()'));
}

/**
 * Fonction de désinstallation du plugin.
**/
function spip2spip_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_spip2spips");
  
  # Nettoyer les colonnes en extra
  sql_alter("TABLE spip_articles DROP COLUMN s2s_url");
	sql_alter("TABLE spip_articles DROP COLUMN s2s_url_trad");	


	effacer_meta($nom_meta_base_version);
}

?>