<?php
/******************************************************************************************
 * Dépublication permet de dépublier un article à une date donnée.						  *
 * Copyright (C) 2005-2010 Nouveaux Territoires support<at>nouveauxterritoires.fr		  *
 * http://www.nouveauxterritoires.fr							    					  *
 *                                                                                        *
 * Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes *
 * de la Licence Publique Générale GNU publiée par la Free Software Foundation            *
 * (version 3).                                                                           *
 *                                                                                        *
 * Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       *
 * ni explicite ni implicite, y compris les garanties de commercialisation ou             *
 * d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  *
 * pour plus de détails.                                                                  *
 *                                                                                        *
 * Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    *
 * en même temps que ce programme ; si ce n'est pas le cas,								  * 
 * regardez http://www.gnu.org/licenses/ 												  *
 * ou écrivez à la	 																	  *
 * Free Software Foundation,                                                              *
 * Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   *
 ******************************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_depublication_list_auteurs_dist() {
	global $connect_statut, $connect_toutes_rubriques, $couleur_claire, $spip_lang_right, $changer_config;
	
	if ($connect_statut == "0minirezo" ) {
		if ($changer_config == 'oui') {
			appliquer_modifs();
		}
		lire_metas();
	}

	$commencer_page = charger_fonction('commencer_page', 'inc');
	$out = $commencer_page(_T('depublication:page_zones_acces'), "configuration");
	$out .= barre_onglets("depublication_list", "depublication");
	
	$contexte = array();
	foreach($_GET as $key=>$val)
		$contexte[$key] = $val;
	
	
	$out .= debut_grand_cadre(true);
	$out .=  recuperer_fond("prive/navigation/depublication_auteurs",$contexte);
	$out .=  recuperer_fond("prive/contenu/depublication_auteurs",$contexte);
	
	$out .= debut_gauche("list_depublication",true);
	$out .= debut_droite('list_depublication',true);
	
	
	
	$out .= fin_gauche('list_depublication',true);
	
	$out .= fin_page();

	echo $out;
}
?>