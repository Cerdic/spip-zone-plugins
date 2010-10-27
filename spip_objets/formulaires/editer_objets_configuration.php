<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip('inc/meta');
include_spip('base/create');
include_spip('base/serial');
include_spip('base/auxiliaires');


function formulaires_editer_objets_configuration_charger_dist(){
	
$valeurs = array('nom_objet'=>'');

	//foreach($valeurs as $key=>$value)
	//	$valeurs[$key] = _request($key);

		return $valeurs;
}

function formulaires_editer_objets_configuration_verifier_dist(){
	
	if(strlen(_request('nom_objet'))<3){
		//les nouveaux objets doivent avoir plus de 3 caracteres, pas trop limitant non plus  
		$erreurs['message_erreur']="Vous devez gérer un nom d'objet supérieur à 3 caractères";
	}
	
	return $erreurs;
}

function formulaires_editer_objets_configuration_traiter_dist(){
	
	//try {
		
		//on va enregistrer le nom de l'objet dans les metas + la création des tables idoines appel d'une fonction
		$objets_installes=liste_objets_meta();
		$nom_objet=trim(_request('nom_objet'));
		//TODO faire de la gestion d'erreur pour ne pas traiter des tables qui existeraient dans SPIP
		$objets_installes[]=$nom_objet;
				
		ecrire_meta("objets_installes",serialize($objets_installes));

		/*include_spip('base/objets');
   	objets_declarer_tables_principales();
   	objets_declarer_tables_auxiliaires();*/
		
		//il faut mettre a jour les tables_principales et auxiliaires
		

		global $tables_principales, $tables_auxiliaires;
		base_serial($tables_principales);
		base_auxiliaires($tables_auxiliaires);
		
		//Mise à jour de la base avec la nouvelle table 
		creer_base();
		
		//TODO : gestion des erreurs a ce niveau, si les tables n'ont pas été créees...
		$message['message_ok']="Le nouvel objet a bien été crée";
		
	//} catch (Exception $e) {
	// on va logger le message de l'erreur
	//	spip_log('debug_objets',$e->getMessage());
	//}
	
		return $message;
	
}

?>