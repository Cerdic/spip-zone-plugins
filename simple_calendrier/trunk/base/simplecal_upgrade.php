<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip('inc/meta');
//include_spip('inc/utils');
include_spip('base/create');



// INSTALLATION OU MISE À JOUR DES TABLES SUPPLÉMENTAIRES
//
// Notes :  
//	- variable $simplecal_base_version : version actuelle de la base
//	- variable $version_cible : nouvelle version de la base, indiquée dans le champ 'version' de plugin.xml

function simplecal_upgrade($simplecal_base_version, $version_cible){

    $current_version = 0.0;
    
    // Si la version cible est différente de la version actuelle, alors on a des choses à faire.
    
    // si la variable vu_base_version est n'est pas renseignée OU si current_version est différent de version_cible
    if ((!isset($GLOBALS['meta'][$simplecal_base_version])) 
        || (($current_version = $GLOBALS['meta'][$simplecal_base_version])!=$version_cible)){	

        // Cas d'une première installation (aucune base préexistante)
        if ($current_version == 0.0){
            spip_log("- Première installation du plugin Simple Calendrier", "simplecal");
            // On indique où se situent les références de la base
            include_spip('base/simplecal_pipelines');
            // On crée la  base (fonction spip)
            creer_base();
            // On met à jour la valeur de la version de la base du plugin installé
            ecrire_meta($simplecal_base_version, $current_version=$version_cible, 'non');
            
            // Parmetres par defaut de la page de configuration
            ecrire_meta('simplecal_autorisation_redac', 'non');
            ecrire_meta('simplecal_rubrique', 'non');
            ecrire_meta('simplecal_refobj', 'non');
            ecrire_meta('simplecal_themeprive', 'base');
            ecrire_meta('simplecal_themepublic', 'base');
            spip_log("- Opération terminée : base créée (version $version_cible).", "simplecal");
        }

        // Si la version courante est inférieure à la version 1.1
        if ($current_version < 1.1){
            spip_log("- Mise à jour MDD du plugin simple-calendrier vers la version 1.1", "simplecal");
            
            echo "Simple calendrier - m.a.j MDD v1.1<br />";
            sql_alter("TABLE spip_evenements ADD id_secteur bigint(21) NOT NULL DEFAULT '0' AFTER id_evenement");
            sql_alter("TABLE spip_evenements ADD id_rubrique bigint(21) NOT NULL DEFAULT '0' AFTER id_secteur");
            sql_alter("TABLE spip_evenements ADD INDEX id_secteur (id_secteur)");
            sql_alter("TABLE spip_evenements ADD INDEX id_rubrique (id_rubrique)");
            // ---
            sql_alter("TABLE spip_evenements DROP INDEX id_auteur");
            sql_alter("TABLE spip_evenements DROP id_auteur");
            // ---
            sql_alter("TABLE spip_evenements CHANGE id_objet id_objet BIGINT(21) NOT NULL DEFAULT '0'");
            
            // -- Mise à jour de la version de la base
            ecrire_meta($simplecal_base_version, $current_version=1.1);
            spip_log("- Opération terminée : MDD du plugin simple-calendrier : v1.0 -> v1.1", "simplecal");
        }
        
        // Si la version courante est inférieure à la version 1.2
        if ($current_version < 1.2){
            spip_log("- Mise à jour MDD du plugin simple-calendrier vers la version 1.2", "simplecal");
            
            echo "Simple calendrier - m.a.j MDD v1.2<br />";
            sql_alter("TABLE spip_evenements CHANGE maj maj timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
            
            // -- Mise à jour de la version de la base
            ecrire_meta($simplecal_base_version, $current_version=1.2);
            spip_log("- Opération terminée : MDD du plugin simple-calendrier : v1.1 -> v1.2", "simplecal");
        }
        
        // Si la version courante est inférieure à la version 1.3
        if ($current_version < 1.3){
            spip_log("- Mise à jour MDD du plugin simple-calendrier vers la version 1.3", "simplecal");
            
            echo "Simple calendrier - m.a.j MDD v1.3<br />";
            effacer_meta('simplecal_themeprive');
            
            // -- Mise à jour de la version de la base
            ecrire_meta($simplecal_base_version, $current_version=1.3);
            spip_log("- Opération terminée : MDD du plugin simple-calendrier : v1.2 -> v1.3", "simplecal");
        }
    }

}


// Désinstallation des tables qui avaient été installées par le plugin
function simplecal_vider_tables($simplecal_base_version) {
    spip_log("- Désinstallation définitive des tables liées au plugin Simple-calendrier", "simplecal");

    // On supprime les tables supplémentaires crées avec le plugin
    sql_drop_table("spip_mots_evenements");
    sql_drop_table("spip_auteurs_evenements");
    sql_drop_table("spip_evenements");
    
    // Puis on supprime les informations meta liées au plugin
    effacer_meta($simplecal_base_version);
    effacer_meta('simplecal_autorisation_redac');
    effacer_meta('simplecal_rubrique');
    effacer_meta('simplecal_refobj');
    effacer_meta('simplecal_themepublic');

    spip_log("- Opération terminée : désinstallation du plugin Simple-calendrier.", "simplecal");
}

?>