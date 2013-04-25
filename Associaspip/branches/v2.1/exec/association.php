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
include_spip ('inc/voir_adherent'); // pour voir_adherent_infos

function exec_association() {

	include_spip('inc/autoriser');
	if (!autoriser('associer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:association')) ;

		association_onglets();

		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo propre(_T('asso:info_doc'));
		echo fin_boite_info(true);

		$res=association_icone(_T('asso:profil_de_lassociation'),  '?exec=configurer_association', 'assoc_qui.png');
		$res.=association_icone(_T('asso:categories_de_cotisations'),  generer_url_ecrire("categories"), 'cotisation.png',  '');
		$res.=association_icone(_T('asso:plan_comptable'),  generer_url_ecrire('plan_comptable'), 'plan_compte.png',  '');
		if ($GLOBALS['association_metas']['destinations']=="on") $res.=association_icone(_T('asso:destination_comptable'),  generer_url_ecrire('destination_comptable'), 'plan_compte.png',  '');

		echo bloc_des_raccourcis($res);
		echo debut_droite("",true);
		echo debut_cadre_formulaire("",true);
		echo recuperer_fond('modeles/asso_profil');
		echo fin_cadre_formulaire(true);

		/* Provisoirement supprimé en attendant 1.9.3*/

		echo '<br />';
		echo gros_titre(_T('asso:votre_equipe'),'',false);
		echo '<br />';

		echo debut_cadre_relief('', true);
		echo recuperer_fond('modeles/asso_membres_fonctions');
		echo fin_cadre_relief(true);
		echo fin_page_association();

		//Petite routine pour mettre à jour les statuts de cotisation "échu"
		sql_updateq('spip_asso_membres',
			array("statut_interne"=> 'echu'),
			"statut_interne = 'ok' AND validite < CURRENT_DATE() ");
	}
}

?>