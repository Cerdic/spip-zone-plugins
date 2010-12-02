<?php 



function statut_auteurs_traduire_statut_auteur($data){
	// Cette fonction permet dans ?exec=auteur_infos de traduire correctement le nouveau statut
	$s=array();
	$statuts=statut_auteurs_get_statuts();
	foreach ($statuts as $statut=>$libelle)
		$s[$statut]=_T("statut_auteurs:".$statut);

	return array_merge($data,$s);
}


?>