<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('public/assembler');

function exec_favoris_dist(){
	$contexte = Array();
	$contexte = calculer_contexte();
	
	// si pas autorise : message d'erreur
	if (!autoriser('modifier', 'article')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	

	// entetes
	$commencer_page = charger_fonction('commencer_page', 'inc');


	// titre, partie, sous_partie (pour le menu)
	echo $commencer_page(_T('plugin:f'), "editer", "editer");
	
// 	// titre
// 	echo recuperer_fond('prive/generale/categorie_fil_ariane',$contexte,Array("ajax"=>true));
// 	//echo gros_titre(_T('echoppe:gerer_echoppe'),'', false);
	
	// colonne gauche
	echo debut_gauche('', true);
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'boutique'),'data'=>''));
			echo debut_boite_info(true);
			
				echo '<div class="infos"><h4>'._T('so:favoris').'</h4>';			
					echo	'<img src="'.chemin('imgs/emblem-favorite.png').'" alt="'._T('so:favoris').'" align="absmiddle" />';
					echo '</div>';

			echo fin_boite_info(true);

	
	// colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'boutique'),'data'=>''));
	
	// centre
	echo debut_droite('', true);
	// contenu
	// ...
	$voir = _request('voir');
	if($voir)$voir = '_'.$voir;
		echo recuperer_fond('prive/selection_interface',$contexte);

	// ...
	// fin contenu
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'boutique'),'data'=>''));
	
	echo fin_gauche(), fin_page();

}
?>
