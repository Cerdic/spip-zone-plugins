<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

// Fonction generale d'execution (exec_*_dist) de la page 'exec/*.php'
// Pour un exemple type : http://programmer.spip.org/Contenu-d-un-fichier-exec
function exec_veille_dist(){
	// -- Si pas autorise : message d'erreur
	if (!autoriser('voir', 'veille')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	// -- Pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'veille'),'data'=>''));

	// -- Entetes
	$commencer_page = charger_fonction('commencer_page', 'inc');
	// titre, partie, sous_partie (pour le menu)
	echo $commencer_page(_T('Veille'), "editer", "editer");
	
	/**
	// -- Afficher ici le titre de la page
	echo "<br /><br /><br />\n"; // outch ! aie aie aie ! au secours !
	echo gros_titre(_T('Veille'),'', false); **/
	
	// -- Colonne gauche
	echo debut_gauche('', true);
	// on ajoute un cadre d'information contextuel
	echo cadre_veille_infos();
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'veille'),'data'=>''));
	
	// -- Colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'veille'),'data'=>''));
	
	// -- Centre
	echo debut_droite('', true);
	
	// -- Contenu
	// ...
	echo "afficher ici ce que l'on souhaite !";
	// ...
	
	// -- Fin contenu
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'veille'),'data'=>''));
	echo fin_gauche(), fin_page();
}
?>
