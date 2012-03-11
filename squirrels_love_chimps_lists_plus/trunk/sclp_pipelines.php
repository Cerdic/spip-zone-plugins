<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


// Ajoute le foormulaire abonnement à la ficher auteurs
function sclp_affiche_milieu($flux){
	switch($flux['args']['exec']) {
		
		case 'auteur_infos':
			include_spip('inc/filtres_images');	
			$image=extraire_attribut(image_reduire(find_in_path('images/letter_64.png'),24),'src');		
			$contenu= recuperer_fond('prive/squelettes/affiche_milieu/auteur_listes',$flux['args']);
			$flux["data"] .= cadre_depliable($image,strtoupper(_T('sclp:gerer_abonnements_listes')),'auto',$contenu,'gerer_abonnements_listes','e');    
			break;
	}
	return $flux;

}
	
	
// ajouter les objets à l'api de configuration
function sclp_squirrel_chimp_definitions($flux){
	

	$flux['data']['sclp']=$valeurs;
	
	//On rajoute un champ à la config
	$flux['data']['squirrel_chimp_lists']['config'][100]='editer_champs';

	
	//$flux['data']['sclp']['config'][6]='sync_auteurs';
	
	
	return $flux;

	}

// Actualisation des listes à partir du formulaire editer_auteur
function sclp_lists_formulaire_traiter($flux)
{

	// on recupere d'abord le nom du formulaire .
	$formulaire = $flux['args']['form'];
	spip_log(__LINE__,'squirrel_chimp');


	return $flux ;
}

// Ajouter un traitement au formulaire de configuration , partie listes
function sclp_squirrel_chimp_lists_config_traiter($flux){
	
	include_spip('squirrel_chimp_lists_fonctions');
	
	$mailinglists=_request('mailinglists');
	
	foreach($mailinglists AS $id_liste=>$id_liste_mailchimp){
	
		$valeurs = array(	
			'id_liste_mailchimp'=>$id_liste_mailchimp,	
			'maj'=>$date	
			);	
		sql_updateq('spip_listes',$valeurs,'id_liste='.$id_liste);
	}
	
	return $flux;
	
	}
?>
