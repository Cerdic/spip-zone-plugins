<?php
function rubrique_a_linscription_autoriser(){}

// Selon la config, autoriser ou pas l'inscription direct d'administrateur. Attention ca suppose qu'on utilise pas de formulaire d'inscription autre que ceux qui font des admins restreints, sinon ca devient dangereux
function autoriser_0minirezo_inscrireauteur($faire, $quoi, $id, $qui, $opt){
	spip_log("sncf",$id);
	$rubrique_a_linscription_statut = lire_config("rubrique_a_linscription/statut");
	return $rubrique_a_linscription_statut==$quoi;
}

function autoriser_rubrique_a_linscription($faire, $type, $id, $qui,  $opt){
	if ($faire	== 'configurer'){
		include_spip('inc/autoriser');
		return autoriser_configurer_dist($faire,$type,$id,$qui,$opt);	
	}
}

function autoriser_rubrique_creerarticledans($faire, $type, $id, $qui, $opt) {
	
	include_spip('inc/autoriser');
	$espace_prive_creer 	= lire_config('rubrique_a_linscription/espace_prive_creer');
	$resultat = sql_fetsel('rubrique_a_linscription','spip_auteurs','id_auteur='.$qui['id_auteur']);
	if (!$espace_prive_creer or is_null($resultat['rubrique_a_linscription'])){
		return $id AND autoriser('voir','rubrique',$id);
	}
	else {
		
		if ($resultat['rubrique_a_linscription'] == $id){
			return true;	
		}
		else{
			return false;
		}
	}
}

function autoriser_voir($faire, $type, $id, $qui, $opt) {

	include_spip('inc/autoriser');

	$espace_prive_voir = lire_config('rubrique_a_linscription/espace_prive_voir');
	$resultat = sql_fetsel('rubrique_a_linscription,statut','spip_auteurs','id_auteur='.$qui['id_auteur']);

	
	if (!$espace_prive_voir or is_null($resultat['rubrique_a_linscription'])){
		return autoriser_voir_dist($faire, $type, $id, $qui, $opt);
	}
	
	$liste_rubriques_auteur = remonter_hierarchie_rubriques(liste_rubriques_auteur($qui['id_auteur']));
	
	
	if ($type == 'document')
		return autoriser_document_voir_dist($faire, $type, $id, $qui, $opt);
	if ($type == 'groupemots') {
		$acces = sql_fetsel("comite,forum", "spip_groupes_mots", "id_groupe=".intval($id));
		if ($qui['statut']=='1comite' AND ($acces['comite'] == 'oui' OR $acces['forum'] == 'oui'))
			return true;
		if ($qui['statut']=='6forum' AND $acces['forum'] == 'oui')
			return true;
		return false;
	}
	if ($type == 'rubrique'){
		return in_array($id,$liste_rubriques_auteur);
	}
	if ($type == 'article' or $type == 'breve'){
		$rubrique_objet = sql_getfetsel('id_rubrique','spip_'.$type.'s','id_'.$type.'='.$id);
		
		if (in_array($rubrique_objet,$liste_rubriques_auteur)){
			
			return autoriser_voir_dist($faire, $type, $id, $qui, $opt);
		}
		else{
			return false;	
		}
	}
	return autoriser_voir_dist($faire, $type, $id, $qui, $opt);
	
	
}
function remonter_hierarchie_rubriques($rubriques){
	$toutes_rubriques	= array();
	include_spip('base/abstract');
	foreach ($rubriques as $id_rubrique){
		$toutes_rubriques[]	= $id_rubrique;
		while ($id_rubrique = sql_getfetsel("id_parent","spip_rubriques","id_rubrique=" . $id_rubrique,"","","", "", $connect)){
			settype($id_rubrique,'int')	;
		 	$toutes_rubriques[] = $id_rubrique ;
		 }	 
	}
	return $toutes_rubriques;
}
