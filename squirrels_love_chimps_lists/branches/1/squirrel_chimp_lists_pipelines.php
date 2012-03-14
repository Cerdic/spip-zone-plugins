<?php

if (!defined("_ECRIRE_INC_VERSION")) return;



// ajouter les objets à l'api de configuration
function squirrel_chimp_lists_squirrel_chimp_definitions($flux){
	
	$valeurs=array(
			'config'=>array(
				0=>'mailinglists',
				1=>'ml_act_ajout',
				2=>'ml_opt_in',
				3=>'ml_act_enleve',
				4=>'ml_act_actualise',
				5=>'mapping',
				
				),
			'fichier_langue'=>'scl'
			);
		
	$flux['data']['squirrel_chimp_lists']=$valeurs;
	

	$flux['data']['squirrel_chimp_lists']['config'][6]='sync_auteurs';
	
	return $flux;

	}

// Actualisation des listes à partir du formulaire editer_auteur
function squirrel_chimp_lists_formulaire_traiter($flux)
{

	// on recupere d'abord le nom du formulaire .
	$formulaire = $flux['args']['form'];


	//dans notre cas c'est le formulaire editer_auteur qui nous interesse
	if ($formulaire=="editer_auteur"){
		spip_log('actualisation profil','squirrel_chimp');
		$traitement=charger_fonction('editer_auteur_traiter_listes','inc');
		$flux=$traitement($flux);
	}

	return $flux ;
	
}
?>
