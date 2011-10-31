<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;
	
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
	
function exec_groupes(){
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page() ;
		
		association_onglets(_T('asso:gestion_groupes'));
		
		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo _T('asso:aide_groupes');
		echo fin_boite_info(true);

		$res = association_icone(_T('asso:bouton_retour'), generer_url_ecrire('adherents'), "retour-24.png");
		$res.=association_icone(_T('asso:ajouter_un_groupe'),  generer_url_ecrire('edit_groupe'), "annonce.gif");
		echo bloc_des_raccourcis($res);	
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  '', false, "", '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ._T('asso:tous_les_groupes'));
		
		echo recuperer_fond("prive/contenu/voir_groupes", array ());
		
		echo fin_cadre_relief(true);  		
		echo fin_page_association();
	}
}
?>
