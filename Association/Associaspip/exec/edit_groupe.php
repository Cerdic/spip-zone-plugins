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

function exec_edit_groupe() {
		
	$id_groupe = intval(_request('id'));

	if (!autoriser('associer', 'groupes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page('') ;
	
		association_onglets(_T('asso:titre_editer_groupe'));
		
		echo debut_gauche("", true);
		
		echo debut_boite_info(true);
		echo fin_boite_info(true);	
		
		echo association_retour(generer_url_ecrire('groupes'));
		
		echo debut_droite("",true);

		echo recuperer_fond("prive/editer/editer_asso_groupes", array (
			'id' => $id_groupe
		));
		echo fin_page_association();
	}
}
?>
