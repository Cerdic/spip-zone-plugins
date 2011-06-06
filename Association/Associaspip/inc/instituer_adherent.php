<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/texte');
include_spip('inc/layer');
include_spip('inc/presentation');
include_spip('inc/autoriser');



function inc_instituer_adherent_dist($statut) {

	$hstatut = htmlentities($statut);
	foreach ($GLOBALS['association_liste_des_statuts'] as $var) {
		$nom = htmlentities($var);
		$menu .= mySel($nom, $hstatut, _T('asso:adherent_entete_statut_'.$var), ''	 );
	}
	
	$statut_rubrique = str_replace(',', '|', _STATUT_AUTEUR_RUBRIQUE);
	return '<select name="statut_interne" id="statut_interne" size="1" class="formo">'
	.$menu."</select>\n";

}




?>
