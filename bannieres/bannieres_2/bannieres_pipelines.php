<?php

function bannieres_objets_extensibles($objets){
		return array_merge($objets, array('banniere' => _T('bannieres:bannieres')));
}

function bannieres_encart($flux){

	$id_banniere = $flux;
	
	// bloc charger document
	$bloc_doc = afficher_documents_colonne($id_banniere, 'banniere');

	// affiche le resultat obtenu
	$navigation =
	 $bloc_doc
	. pipeline('affiche_milieu',array('args'=>array('exec'=>'bannieres','id_banniere'=>$id_banniere),'data'=>''));

	return $navigation;
}
?>
