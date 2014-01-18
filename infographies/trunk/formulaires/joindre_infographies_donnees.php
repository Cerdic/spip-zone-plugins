<?php
/**
 * Terraeco Infographies
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2013 - Distribué sous licence GNU/GPL
 *
 * Formulaire d'édition d'infographies
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_joindre_infographies_donnees_charger_dist($id_infographies_data, $retour='', $config_fonc='infographies_edit_config'){
	$valeurs = array('editable'=>true);
	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_joindre_infographies_donnees_identifier_dist($id_infographies_data, $retour='', $config_fonc='infographies_edit_config'){
	return serialize(array(intval($id_infographies_data)));
}

// Choix par defaut des options de presentation
function infographies_edit_config($row){
	return array();
}

function formulaires_joindre_infographies_donnees_verifier_dist($id_infographies_data, $retour='', $config_fonc='infographies_edit_config'){
	$erreurs = array();
	$post = isset($_FILES) ? $_FILES : $GLOBALS['HTTP_POST_FILES'];
	if (is_array($post)){
		$files = array();
		foreach ($post as $file) {
			if (!($file['error'] == 4)){
				if($file['type'] == 'text/csv')
					$files[]=$file;
				else 
					$erreurs['fichier'] = 'Erreur upload uniquement CSV';
			}
		}
		if (!count($files) && !isset($erreurs['fichier']))
			$erreurs['fichier'] = 'Erreur upload';
	}else
		$erreurs['fichier'] = 'Erreur upload';

	if(!isset($erreurs['fichier'])){
		$tmp_name    = $files[0]['tmp_name'];
		$destination = _DIR_TMP.basename($tmp_name);
		$resultat    = move_uploaded_file($tmp_name,$destination);
		if (!$resultat)
			$erreurs['fichier'] = 'Erreur upload';
		else {
			$fichiercsv = fopen($destination, "r");
			$i=1;
			while (($data= fgetcsv($fichiercsv,"~")) !== FALSE){
				$data = array_filter($data,'strlen');
				$nombre_elements = count($data);
				if($nombre_elements > 3){
					fclose($fichiercsv);
					unlink($destination);
					$erreurs['fichier'] = "Trop de colonnes à la ligne $i";
					return $erreurs;
				}
				if($nombre_elements < 2){
					fclose($fichiercsv);
					unlink($destination);
					$erreurs['fichier'] = "Pas assez de colonnes à la ligne $i";
					return $erreurs;
				}
				$i++;
			}
			fclose($fichiercsv);
		}
	}
	if(!count($erreurs))
		set_request('path',$destination);
	return $erreurs;
}

// http://doc.spip.org/@inc_joindre_infographies_donnees_dist
function formulaires_joindre_infographies_donnees_traiter_dist($id_infographies_data, $retour='', $config_fonc='infographies_edit_config'){
	$i = 0;
	if($fichier = _request('path')){
		$fichiercsv = fopen($fichier, "r");
		if($fichiercsv){
			include_spip('action/editer_objet');
			$i=1;
			$rang=1;
			while (($data = fgetcsv($fichiercsv,"~")) !== FALSE){
				if(($i == 1) && !is_numeric($data[0]) && !is_numeric($data[1])){
					$i++;
					objet_modifier('infographies_data',$id_infographies_data,array('axe_x'=>$data[0],'axe_y'=>$data[1]));
					continue;
				}
				$set = array('id_infographies_data' => $id_infographies_data,'axe_x'=>$data[0],'axe_y'=>$data[1],'commentaire'=>$data[2],'rang'=>$rang);
				$id_objet = objet_inserer('infographies_donnee');
				$err = objet_modifier('infographies_donnee', $id_objet, $set);
				$rang++;
				$i++;
			}
		}
		fclose($fichiercsv);
		unlink($fichier);
	}
	if($i > 1){
		$res['message_ok'] = "$i données insérées";
		$res['redirect'] = self();
	}
	return $res;
}

?>
