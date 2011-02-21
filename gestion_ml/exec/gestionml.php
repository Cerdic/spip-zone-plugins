<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/gestionml_api');

function exec_gestionml(){
	// si on est admin
	if(!$GLOBALS['connect_statut'] == "0minirezo") {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	} 
	
	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('gestionml:titre_cadre'));

	echo debut_gauche('', true);
	echo debut_boite_info(true);
	echo _T("gestionml:boite_info");
	echo fin_boite_info(true);

	
	echo creer_colonne_droite('',true);

	echo debut_droite('',true);
	echo recuperer_fond('prive/gestionml');

	echo fin_gauche().fin_page();
	
}

?>