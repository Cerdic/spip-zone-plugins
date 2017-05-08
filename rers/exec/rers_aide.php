<?php
// rers page d'aide aux adhérents
//  INUTILE depuis son remplacement en sept 2009 par un fichier PDF.  !!!!!!!!

// doc : https://programmer.spip.net/Contenu-d-un-fichier-exec

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

function exec_rers_aide() {
	// pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'nom'),'data'=>''));
	// entetes
	$commencer_page = charger_fonction('commencer_page', 'inc');
	// titre, partie, sous_partie (pour le menu)
	echo $commencer_page("aide RERS", "editer", "editer");
	// titre
	echo "<br /><br /><br />\n"; // outch ! aie aie aie ! au secours !
	echo gros_titre(_T('rers_aide_titre'),'', false);
	
	// colonne gauche
	echo debut_gauche('', true);
	echo icone_horizontale("Envoyer un message prive au Webmestre ", generer_action_auteur("editer_message","normal/$rers_auteur_webmestre"),"message.gif","", false);
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'nom'),'data'=>''));
	
	// colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'nom'),'data'=>''));
	
	// centre
	echo debut_droite('', true);
	// contenu
	// ...


// AIDE RERS
	$rers_auteur_webmestre = lire_config('rers/rers_auteur_webmestre');
	echo _T('rers_aide_contenu');


	// ...
	// fin contenu
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'nom'),'data'=>''));
	echo fin_gauche(), fin_page();

}

?>
