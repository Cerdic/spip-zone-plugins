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


include_spip ('inc/navigation_modules');
include_spip ('inc/mail');
//include_spip ('inc/charsets');

function exec_action_relances()
{
	if (!autoriser('associer', 'comptes')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		association_onglets(_T('asso:titre_onglet_membres'));
		// notice ?
		echo _T('asso:aide_relances'); //!\ il faut en rajouter
		echo association_date_du_jour();
		echo fin_boite_info(true);
		echo association_retour();
		debut_cadre_association('ico_panier.png', 'relance_de_cotisations');
		echo recuperer_fond('prive/editer/relance_adherents');
		fin_page_association();
	}
}

?>