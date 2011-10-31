<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
include_spip('inc/autoriser');

function exec_voir_groupe() {
		
	$id_groupe = intval(_request('id'));

	if (!autoriser('associer', 'groupes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page('') ;
	
		association_onglets(_T('asso:groupe_dp').' '.sql_getfetsel('nom', 'spip_asso_groupes', 'id_groupe='.$id_groupe));
		
		echo debut_gauche("", true);
		
		$res = association_icone(_T('asso:bouton_retour'), str_replace('&', '&amp;', $_SERVER['HTTP_REFERER']), "retour-24.png");
		$res.= association_icone(_T('asso:editer_groupe'),  generer_url_ecrire('edit_groupe', 'id='.$id_groupe), 'edit.gif');
		echo bloc_des_raccourcis($res);

		
		echo debut_droite("",true);

		echo recuperer_fond("prive/contenu/voir_groupe", array ('id_groupe' => $id_groupe));
		echo recuperer_fond('prive/contenu/voir_membres_groupe', array('id_groupe' => $id_groupe));
		echo fin_page_association();
	}
}
?>
