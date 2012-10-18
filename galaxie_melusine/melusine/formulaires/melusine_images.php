<?php 
include_spip('inc/config');
function formulaires_melusine_images_charger(){
	$valeurs = array('pos1','pos2','pos3','pos4','pos5','pos6');
	
	return $valeurs;
}

function formulaires_melusine_images_verifier(){
	$erreurs = array();
	// verifier que les champs obligatoires sont bien la :
	//foreach(array('pos1','pos2','pos3','pos4','pos5') as $obligatoire)
	//	if (!_request($obligatoire)) $erreurs[$obligatoire] = 'Ce champ est obligatoire';
	
	// verifier que si un email a été saisi, il est bien valide :
	//include_spip('inc/filtres');
	//if (_request('email') AND !email_valide(_request('email')))
	//	$erreurs['email'] = 'Cet email n\'est pas valide';

	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
	return $erreurs;
}




function formulaires_melusine_images_traiter(){
	$images=array("image2","image3","image4","image5","image6","image8","image9","image10","image11");
	foreach($images as $image){
		$req=_request($image);
		$file=$req."file";
			
		if($_FILES[$file]['tmp_name']){
			$chemin='melusine_images/'.$image;
			$nom_fichier= $_FILES[$file]['tmp_name'];
			if(strpos($_SERVER['REQUEST_URI'],"/ecrire/")){$vers="../";}
			else{$vers="";};
			$chemin_destination_boutons=$vers."IMG/config/config_images";
			$chemin_destination_config=$vers."IMG/config";
			$nom_destination=$vers.'IMG/config/config_images/'.$_FILES[$file]['name'];
			$nom_destination0='IMG/config/config_images/'.$_FILES[$file]['name'];
			if(!is_dir("$chemin_destination_boutons")){
				if(!is_dir($chemin_destination_config)){
					mkdir($chemin_destination_config,0777);
				}
				mkdir($chemin_destination_boutons,0777);
			};
			move_uploaded_file($nom_fichier, $nom_destination); 
			ecrire_config($chemin,$nom_destination0);

		}
	}
return array('message_ok'=>'Image enregistr&eacute;e');	
	

	
	
	
	
}



?>