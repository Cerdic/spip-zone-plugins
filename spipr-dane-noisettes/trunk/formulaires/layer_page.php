<?php
/**
  Plugin SPIPr-Dane-Config
  Fichier #FORMULAIRE_LAYER_PAGE
  * formulaire de configuration du modele de page 
  * param string : bloc - nom de la page a configurer 
  (c) 2019 Dominique Lepaisant
  Distribue sous licence GPL3
*/
include_spip('inc/config');
include_spip('inc/yaml');

function formulaires_layer_page_charger_dist( $bloc ) {
// on charge les saisies et les champs
// la liste des modeles de page est dans le fichier
// yaml/liste-modeles.yaml
  $valeurs = array(
	'compo' => $bloc, 
	'modeles' => is_file(find_in_path('yaml/liste-modeles.yaml')) ? yaml_decode_file(find_in_path('yaml/liste-modeles.yaml'))
      : array("content-aside_extra"=>"3","content_aside_extra"=>"1"),
	'layer' =>  !is_null(lire_config('sdn/'.$bloc.'/layer') )? lire_config('sdn/'.$bloc.'/layer') : 'content-aside_-extra', 
    'largeur_content' => '',
 );
  return $valeurs;
}

function formulaires_layer_page_verifier_dist( $bloc ) {
	$erreurs = array();
	// Controle du layer
/*    $modeles = yaml_decode_file(find_in_path('yaml/liste-modeles.yaml'));
    if (!in_array($modeles, _request("layer"))) 
        $erreurs["layer"]="Modèle non reconnu !";
*/    
	return $erreurs;
}

function formulaires_layer_page_traiter_dist( $bloc ) {
  // Traitement des données reçues du formulaire, 
	if (!_request('_cfg_delete')){
		if (  _request('layer')!='' ){
			ecrire_config('sdn/'.$bloc.'/layer',  _request('layer'));
			ecrire_config('sdn/'.$bloc.'/largeur_content',  _request('largeur_content'));
			if(is_null(lire_config('sdn/'.$bloc.'/layer'))) {
				$errs = 'La configuration des  colonnes n\'a pas été enregistrée.';
			}
			else {
				$oks = 'La configuration des  colonnes a été enregistrée';
			}
		}
	}
	else 
	{
		$oks = 'La configuration des colonnes a été suprimée'; 
		effacer_config('sdn/'.$bloc.'/layer');
		return array('message_ok'=>$oks);
	}
  // S'il y a des erreurs, elles sont retournées au formulaire
  if( isset($errs) )
    return array('message_erreur'=>$errs);

  // Sinon, le message de confirmation est envoyé
  else 
  	return array('message_ok'=>$oks);
}
?>
