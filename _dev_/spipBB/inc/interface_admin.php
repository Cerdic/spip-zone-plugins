<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : inc/interface_admin - outils interface d'admin#
#  Authors : Chryjs, 2007 et als                           #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
#----------------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

if (!defined("_ECRIRE_INC_VERSION")) return;
spip_log(__FILE__.' : included','spipbb');
if (defined("_INC_INTERFACE_ADMIN")) return; else define("_INC_INTERFACE_ADMIN", true);

if (!defined("_INC_SPIPBB")) include_spip('inc/spipbb');

#
# Liste de tous les menus (exec/...) d'administration ajoutes
#
global $modules;
// ce menu doit toujours etre actif
$modules['01_general']['01_configuration'] = "spipbb_admin_configuration"; 
//include_spip('inc/filtres.php');
//$svn_revision = abs(version_svn_courante(_DIR_PLUGIN_SPIPBB));
//	if ( $svn_revision AND $svn_revision>0) {
$modules['01_general']['ZZ_debug'] = "spipbb_admin_debug";
// }
if ( spipbb_is_configured() and $GLOBALS['spipbb']['configure']=='oui' ) {
	// tous ces menus necessitent que spipbb soit configure et active
	if ($GLOBALS['spipbb']['config_id_secteur'] == 'oui' AND !empty($GLOBALS['spipbb']['id_secteur'])) {
		// ces menus ont besoin d'un secteur/forums defini (a priori)
		$modules['01_general']['gestion'] = "spipbb_admin_gestion_forums";
		$modules['outils']['fromphpbb'] = "spipbb_admin_fromphpbb";
	}
	$modules['01_general']['02_etat'] = "spipbb_admin_etat";
	$modules['spam']['swconfig'] = "spipbb_admin_anti_spam_config";
	if ($GLOBALS['spipbb']['config_spam_words']=='oui') {
		// ces menus ont besoin que le spam soit active
		$modules['spam']['swwords'] = "spipbb_admin_anti_spam_words";
		$modules['spam']['swlog'] = "spipbb_admin_anti_spam_log";
		$modules['spam']['swforum'] = "spipbb_admin_anti_spam_forum";
	}
}

// ------------------------------------------------------------------------------
// [fr] Construit la liste des choix de l'admin spipbb en fonction de ce qui est
// disponible
// Pour etre affiche dans la liste, le fichier doit etre dans exec/
// s'appeler spipbb_admin_XXXX
// et contenir un element de tableau $modules[__titre_categorie__][__element__]
// ------------------------------------------------------------------------------
function spipbb_admin_gauche($rubrique_admin_courante="")
{
global $modules;
	// cette fonction pose des problemes et est realisee  en plusieurs etapes pour debogage
	spip_log('inc/spipbb.php : spipbb_admin_gauche START :'._DIR_PLUGIN_SPIPBB,'spipbb');

	$assembler = charger_fonction('assembler', 'public'); // recuperer_fond est dedans
	if (!function_exists('recuperer_fond')) include_spip('public/assembler'); // voir un charger fonction

	if (!function_exists('generer_url_ecrire')) {
		include_spip('inc/utils');
		spip_log('inc/spipbb.php : spipbb_admin_gauche generer_url_ecrire not found','spipbb');
	}
	
	ksort($modules);
	$affichage = "\n";
	while( list($cat, $action_array) = each($modules) )
	{
		$cat = _T('spipbb:admin_cat_'.$cat); // on traduit le nom de chaque categorie

		$affichage .= debut_boite_info(true). "<b>".$cat."</b>";
		ksort($action_array);
		while( list($action, $file) = each($action_array) )
		{
			$action = _T('spipbb:admin_action_'.$action) ; // on traduit le nom de chaque action(exec)
			if ( $rubrique_admin_courante <> $file ) {
				$lien = generer_url_ecrire($file) ;
				/*$affichage .= "<a href='".$lien."' class='verdana2'>
					<div style='margin-top:2px;' class='bouton36blanc'
					onMouseOver=\"changeclass(this,'bouton36gris')\"
					onMouseOut=\"changeclass(this,'bouton36blanc')\">".$action.
					"</div></a>\n";*/
			}
			else {	// pas de lien sur l'action en cours !
				$lien="0";
				/*$affichage .= "<div style='margin-top:2px;' class='bouton36blanc'
					onMouseOver=\"changeclass(this,'bouton36gris')\"
					onMouseOut=\"changeclass(this,'bouton36blanc')\">".$action.
					"</div>\n";*/
			}
			$contexte = array( 
								'lien' => $lien,
								'action' => $action,
								);
			$affichage .= recuperer_fond("prive/spipbb_bloc_admin_menu",$contexte) ;
		}
		$affichage .= fin_boite_info(true)."\n";
	}
	$affichage .= "\n";
	spip_log('inc/spipbb.php : spipbb_admin_gauche END','spipbb');

	return $affichage;
}

?>
