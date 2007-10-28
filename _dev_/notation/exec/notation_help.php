<?php
/**
* Plugin Notation
* par JEM (jean-marc.viglino@ign.fr)
* 
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Affichage de l'aide
*  
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/vieilles_defs');
include_spip('inc/presentation');
include_spip('inc/notation_menu');

function exec_notation_help()
{	// Afficher les menus
	if (ecrire_menu('help'))
	{	global $couleur_foncee, $couleur_claire;
		//
		// Le code de la page
		//
		echo "<br/>";
		debut_cadre_trait_couleur('../'._DIR_PLUGIN_NOTATION.'/img_pack/notation_help.png');
		//debut_boite_info();
		gros_titre(_T('notation:aide'));
		debut_cadre_enfonce();
			include (_DIR_PLUGIN_NOTATION.'documentation.html');
		fin_cadre_enfonce();
		fin_cadre_trait_couleur();


		// Fin de la page
 		echo fin_gauche(), fin_page();
	}
}

?>