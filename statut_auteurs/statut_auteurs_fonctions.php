<?php 

//permettre l'inclusion pour appel fonction traduire_statut_auteur
if(_request('exec')=='statut_auteurs' )
	include_spip('inc/instituer_auteur');

	
	
	
function statut_auteurs_autre_statut(){
	
	$autre_statut=statut_auteurs_get_statuts();
	$html="";
	foreach ($autre_statut as $statut=>$libelle){ //on n'utilise pas directement le libelle pour permettre des traductions sur les statuts
		$html.="<input type='radio' name='statut' value='".$statut."' />"._T("statut_auteurs:".$statut);
	}
	return $html;
}

function statut_auteur_get_libelle($code){
	
	return _T('statut_auteurs:'.$code);
	
}



?>