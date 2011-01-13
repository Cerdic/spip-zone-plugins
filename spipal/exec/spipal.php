<?php
/***************************************************************************\
 *  SPIPAL, Utilitaire de paiement en ligne pour SPIP                      *
 *                                                                         *
 *  Copyright (c) 2007 Thierry Schmit                                      *
 *  Copyright (c) 2011 Emmanuel Saint-James                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

function exec_spipal(){
    
 	if (!autoriser('payer_en_ligne', 'versement')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
    
	$titre = _T('spipal:admin_titre_editer');
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page($titre, "", "");

	echo debut_gauche('',true);
        echo debut_boite_info(true);
        echo _T('spipal:admin_texte_intro');
        echo fin_boite_info(true);
    
	$res= icone_horizontale(_T('configuration'),  generer_url_ecrire('configurer_spipal'), _DIR_IMG_PACK. 'cfg-16.png', '', false);
	echo bloc_des_raccourcis($res);

	echo debut_droite('',true);
	echo gros_titre(_T('spipal:admin_titre_editer'), '', '', true);

        $form = charger_fonction('recuperer_liste_versements', 'inc');
        echo $form();
        
	echo fin_gauche(true), fin_page(true);
	}
}

?>
