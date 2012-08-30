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


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip ('inc/navigation_modules');

function prefixPlugin_insert_head($flux){
          $flux .= "<!-- un commentaire pour rien ! -->\n";
          return $flux;
}


function exec_edit_compte()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_compte = intval(_request('id'));
		onglets_association('titre_onglet_comptes');
		// INTRO : resume compte
		echo association_totauxinfos_intro('', 'compte', $id_compte, $infos );
		// datation et raccourcis
		icones_association('');
		debut_cadre_association('compts.gif', 'modification_des_comptes');
		echo recuperer_fond('prive/editer/editer_asso_comptes', array (
			'id_compte' => $id_compte
		));
		fin_page_association();
	}
}

?>