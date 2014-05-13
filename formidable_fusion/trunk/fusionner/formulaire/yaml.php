<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function fusionner_formulaire_yaml_fusionner_dist($fichier,$id_formulaire){
	$yaml = '';
	lire_fichier($fichier, $yaml);
	// lors de l'import, si on a bien recupere une chaine on tente de la decoder
	if ($yaml){
		include_spip('inc/yaml');
		$formulaire = yaml_decode($yaml);
		// Si le decodage marche on importe le contenu
		if (is_array($formulaire)){
			include_spip('action/editer_formulaire');
			include_spip('base/abstract_sql');
	
			//On recupere tous les champs du formulaire de base choisi depuis sa page
			if ($id_formulaire > 0){
				$formulaire_from = sql_fetsel(
					'*',
					'spip_formulaires',
					'id_formulaire = '.$id_formulaire
				);
				
				//pouvoir importer même sur un formulaire vide
				if($formulaire_from['saisies'])
				$formulaire_from['saisies'] = unserialize($formulaire_from['saisies']);	
				else $formulaire_from['saisies']=array();
			}
			
			//fusionne uniquement les saisies du formulaire importé
			//car les traitements sont laissés au formulaire d'origine
			if (is_array($formulaire['saisies']) && is_array($formulaire_from['saisies'])){
					$formulaire_from['saisies'] = array_merge($formulaire_from['saisies'],$formulaire['saisies']);
					$formulaire_from['saisies'] = serialize($formulaire_from['saisies']);	
					
				//si aucun traitement dans le formulaire de base (formulaire_from) reprendre ceux du formulaire importé
				if(empty($formulaire_from['traitements'])){
					$formulaire_from['traitements']=serialize($formulaire['traitements']);	
				}	

				session_set("constructeur_formulaire_formidable_$id_formulaire");
				$erreur = formulaire_modifier($id_formulaire, $formulaire_from);
			}

		}
		
	}
	
	if ($id_formulaire && !$erreur){
		return $id_formulaire;
	}
	else{
		return _T('formidable:erreur_importer_yaml').' : '.$erreur;
	}
}

?>
