<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_plugonet_verifier_dist(){
	global $spip_lang_right;
	// si pas autorise : message d'erreur
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

	// pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'plugonet'),'data'=>''));

	// entetes
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('plugonet:titre_page_navigateur'), "naviguer", "plugonet");
	echo "<br />\n";
	echo "<br />\n";
	
	// titre
	echo gros_titre(_T('plugonet:titre_page'),'', false);
	
	// barre d'onglets
	echo barre_onglets("plugonet", "plugonet_verifier");
	
	// colonne gauche
	echo debut_gauche('', true);
	// -- Boite d'infos : aucune
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'plugonet_verifier'),'data'=>''));
	
	// colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'plugonet_verifier'),'data'=>''));
	
	// centre
	echo debut_droite('', true);

	// contenu
 	echo recuperer_fond('prive/contenu/plugonet_verifier',  array());

	// fin contenu
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'plugonet_verifier'),'data'=>''));

	echo fin_gauche(), fin_page();
	}
}

?>
