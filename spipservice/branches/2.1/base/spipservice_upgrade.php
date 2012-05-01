<?php

/*______________________________________________________________________________
 | Plugin SpipService 1.0 pour Spip 2.1                                           \
 | Copyright 2012 Sebastien Chandonay - Studio Lambda                            \
 |                                                                                |
 | SpipService est un logiciel libre : vous pouvez le redistribuer ou le          |
 | modifier selon les termes de la GNU General Public Licence tels que            |
 | publiés par la Free Software Foundation : à votre choix, soit la               |
 | version 3 de la licence, soit une version ultérieure quelle qu'elle            |
 | soit.                                                                          |
 |                                                                                |
 | SpipService est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE     |
 | GARANTIE ; sans même la garantie implicite de QUALITÉ MARCHANDE ou             |
 | D'ADÉQUATION À UNE UTILISATION PARTICULIÈRE. Pour plus de détails,             |
 | reportez-vous à la GNU General Public License.                                 |
 |                                                                                |
 | Vous devez avoir reçu une copie de la GNU General Public License               |
 | avec SpipService. Si ce n'est pas le cas, consultez                            |
 | <http://www.gnu.org/licenses/>                                                 |
 ________________________________________________________________________________*/

if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip('inc/meta');
//include_spip('inc/utils');
include_spip('base/create');



/** INSTALLATION OU MISE e JOUR DES TABLES SUPPLeMENTAIRES **/
//
// Notes :  
//	- variable $spipservice_base_version : version actuelle de la base
//	- variable $version_cible : nouvelle version de la base, indiquee dans le champ 'version' de plugin.xml

function spipservice_upgrade($spipservice_base_version, $version_cible){

	$current_version = 0.0;
	
	// Si la version cible est differente de la version actuelle, alors on a des choses e faire.
	
    // si la variable vu_base_version est n'est pas renseignee
    // OU si current_version est different de version_cible
    if ((!isset($GLOBALS['meta'][$spipservice_base_version])) 
		|| (($current_version = $GLOBALS['meta'][$spipservice_base_version])!=$version_cible)){	

		// Cas d'une premiere installation (aucune base preexistante)
		if ($current_version==0.0){
			spip_log("- Premiere installation du plugin SpipService", "spipservice");
			// On indique où se situent les references de la base
			include_spip('base/spipservice_pipelines');
			// On cree la  base (fonction spip)
			creer_base();
			// On met e jour la valeur de la version de la base du plugin installe
			ecrire_meta($spipservice_base_version, $current_version=$version_cible, 'non');
			spip_log("- Operation terminee : base creee (version $version_cible).", "spipservice");
		}

		// Si la version courante est inferieure e la version 0.2
		if ($current_version<0.2){
			
			//spip_log("- Mise e jour vers la version 0.2 de la base", "spipservice");
			//sql_alter("TABLE spipservice CHANGE date_redac date text NOT NULL");
			// -- Le travail est termine, la base est e jour. Reste e mettre e jour la valeur de la version de la base
			////ecrire_meta($spipservice_base_version,$current_version=$version_cible, 'non');
			//spip_log("- Operation terminee : mise e jour de la base vers la version 0.2", "spipservice");
		}

	}

}




/** DeSINSTALLATION DES TABLES SUPPLeMENTAIRES **/
//
// Note :
//	- /!\ Supprimer toutes les tables supplementaires, et les informations contenues. Aucun retour en arriere possible !
//	- Concerne la desinstallation proprement dite, et non une simple desactivation.
//	- Variable $nom_meta_base_version : indique la version actuelle de la base

function spipservice_vider_tables($spipservice_base_version) {
	
	spip_log("- Desinstallation definitive de la base", "spipservice");

	// On supprime les tables supplementaires crees avec le plugin
	
	spip_log("---> Desinstallation definitive de la table 'spip_spipservice'", "spipservice");
	sql_drop_table("spip_spipservice");
	
	
	// Puis on supprime les informations meta liees au plugin
	effacer_meta($spipservice_base_version);

	spip_log("- Operation terminee : desinstallation de la base.", "spipservice");

}

?>
