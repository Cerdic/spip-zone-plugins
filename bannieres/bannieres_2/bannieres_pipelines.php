<?php

function bannieres_objets_extensibles($objets){
		return array_merge($objets, array('banniere' => _T('bannieres:bannieres')));
}

function bannieres_encart($flux){
	// on recupère l'ID de la banniere
	$id_banniere = $flux;
	
	/*
	 * Fonction SPIP permettant de charger l'image de la banniere comme pour les autres objets SPIP.
	 * Elle charge l'image dans le dossier IMG et la renome en : banniereon1.jpg par exemple.
	 * ça buggue à la suppression :  message d'erreur de squelette, mais la banniere est bien supprimée.
	 */
	 
	$iconifier = charger_fonction('iconifier', 'inc');

	global $logo_libelles;
	$logo_libelles =  array(
		       'id_banniere' => _T('bannieres:logo_banniere')
		       );

	$image = $iconifier('id_banniere', $id_banniere, 'bannieres', false, true);
	
	// affiche le resultat obtenu
	$navigation = $image 
	. pipeline('affiche_milieu',array('args'=>array('exec'=>'bannieres','id_banniere'=>$id_banniere),'data'=>''));

	return $navigation;

}

?>
