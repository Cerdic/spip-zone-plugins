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

function exec_edit_categorie(){

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:categories_de_cotisations')) ;
		association_onglets();

		echo debut_gauche('',true);

		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo fin_boite_info(true);

		echo association_retour();

		echo debut_droite('',true);

		echo recuperer_fond('prive/editer/editer_asso_categories', array (
			'id_categorie' => $id_categorie
		));

		echo fin_page_association();
	}
}

?>