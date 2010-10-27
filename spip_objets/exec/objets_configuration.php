<?php 



if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/actions');


function exec_objets_configuration_dist(){
	
	
	
	
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	pipeline('exec_init',array('args'=>array('exec'=>'objets_configuration'),'data'=>''));
	
	if ($id_version) $nom_objet.= ' ('._T('objets:version')." $id_version)";

	echo $commencer_page(_T('objets:titre_page_objets_configuration', array('nom_objet' => $nom_objet)), "naviguer", "articles", $id_rubrique);

	echo debut_grand_cadre(true);
	//echo afficher_hierarchie($id_rubrique,'',$id_article,'article');
	echo fin_grand_cadre(true);

	echo debut_gauche("",true);

	

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'objets_configuration'),'data'=>''));
	echo creer_colonne_droite("",true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'objets_configuration'),'data'=>''));
	echo debut_droite("",true);
	
	
	
	// on va faire un recuperer fond du squelette contenant le CVT
	//on est obligés de passer par le calcul des objets installes ici , c'est plus joli qu'un #EVAL{$GLOBALS...} dans le squelette
	$objets_installes=liste_objets_meta();
	$contexte=array('existant'=>$objets_installes);
	
	$milieu = recuperer_fond("prive/editer/objets_configuration", $contexte);
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'objets_configuration'),'data'=>$milieu));
	
	
	
	
}

?>