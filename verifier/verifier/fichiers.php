<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Vérifier une saisie d'envoi de fichiers
 *
 * @param array $valeur
 *   Le sous tableau de $_FILES à vérifier, $_FILES['logo'] par exemple
 *   Doit être un champ avec un ou plusieurs upload
 * @param array $options
 *   Options à vérifier :
 *   - mime au choix 'image_web','tout_mime','specifique'
 *   - mime_specifique (si l'option 'mime_specifique' est choisi ci-dessus)
 *   - taille_max (en Kio)
 *   - largeur_max (en px)
 *   - hauteur_max (en px)
 * @param array|string &$erreurs_par_fichier
 *   Si on vérifier un upload multiple, un tableau, passé par référence, qui contient le détail des erreurs fichier de $_FILES['fichier'] par fichier
 *   Si on vérifie un upload unique, une chaîne qui contiendra l'erreur du fichier.  
 * @return string
**/
function verifier_fichiers_dist($valeur, $options, &$erreurs_par_fichier) {
	if (!is_array($valeur['tmp_name'])){//si on reçoit une info de type fichier unique, on bascule comme si on était fichier multiple
		$old_valeur = $valeur;
		$valeur = array();
		foreach ($old_valeur as $propriete=>$val){
			$valeur[$propriete][0] = $val;
		}
	}
	
	foreach ($valeur['tmp_name'] as $cle=>$tmp_name){//On parcourt tous les fichiers
		if ($valeur['error'][$cle]!=0){//On vérifie uniquement les fichiers bien expediés
			continue;	
		}
		foreach (array('mime','taille') as $verification){ // On va vérifier d'hivers choses, dans un certain ordre, en confiant cela à des fonctions homonymes
			$fonction_verification = "verifier_fichier_$verification";
			if ($erreur = $fonction_verification($valeur,$cle,$options)) {
				if (!is_array($erreurs_par_fichier)) {
					$erreurs_par_fichier = $erreur;
					return $erreur;
				} else{
					$erreurs_par_fichier[$cle] = $erreur;
				}
			}
		}
	}
	if (!empty($erreurs_par_fichier)){
		return implode($erreurs_par_fichier,"<br />"); 
	}
	return '';
}

/**
 * Vérifier le mime type d'une saisie d'envoi de fichiers
 *
 * @param array $valeur
 *   Le sous tableau de $_FILES à vérifier, $_FILES['logo'] par exemple
 *   Doit être un champ plusieurs uploads
 * @param int $cle
 *   La clé du tableau qu'on vérifie
 * @param array $options
 *   Les options tels que passés à verifier_fichiers()
 * @return string
**/
function verifier_fichier_mime($valeur,$cle,$options){
	if ($options['mime'] == 'specifique'){
		if (!in_array($valeur['type'][$cle],$options['mime_specifique'])){
			return _T('verifier:erreur_type_non_autorise',array('name'=>$valeur['name'][$cle]));
		}	
	} elseif ($options['mime'] == 'tout_mime') {
		$res = sql_select('mime_type','spip_types_documents','mime_type='.sql_quote($valeur['type'][$cle]));
		if (sql_count($res) == 0) {
			return _T('verifier:erreur_type_non_autorise',array('name'=>$valeur['name'][$cle]));
		}
	} elseif ($options['mime'] == 'image_web') {
		if (!in_array($valeur['type'][$cle],array('image/gif','image/jpeg','image/png'))) {
			return _T('verifier:erreur_type_image',array('name'=>$valeur['name'][$cle]));
		}
	}
	return '';
}


/**
 * Vérifier la taille d'une saisie d'envoi de fichiers
 * La taille est vérifiée en fonction du paramètre passé en option, sinon en fonction d'une constante:
 *	- _IMG_MAX_SIZE si jpg/png/gif
 *	- _DOC_MAX_SIZE si pas jpg/png/gif ou si _IMG_MAX_SIZE n'est pas définie
 * @param array $valeur
 *   Le sous tableau de $_FILES à vérifier, $_FILES['logo'] par exemple
 *   Doit être un champ plusieurs uploads
 * @param int $cle
 *   La clé du tableau qu'on vérifie
 * @param array $options
 *   Les options tels que passés à verifier_fichiers()
 * @return string
**/
function verifier_fichier_taille($valeur,$cle,$options){
	$taille = $valeur['size'][$cle];
	$mime = $valeur['type'][$cle];

	// On commence par déterminer la taille max
	if (isset($options['taille_max'])) {
		$taille_max = $options['taille_max'];
	} elseif (in_array($mime, array('image/gif','image/jpeg','image/png')) and defined('_IMG_MAX_SIZE')) {
		$taille_max = _IMG_MAX_SIZE;
	} elseif (defined('_DOC_MAX_SIZE')) {
		$taille_max = _DOC_MAX_SIZE;
	}

	$taille_max = intval($taille_max); // précaution
		
	//Si la taille max est déterminée, on vérifie que le fichier ne dépasse pas cette taille
	if ($taille_max) {
		$taille_max = 1024 * $taille_max; // passage de l'expression en kibioctets à une expression en octets 
		if ($taille > $taille_max) {
			return _T('verifier:erreur_taille_fichier', array(
				'name'       => $valeur['name'][$cle],
				'taille_max' => taille_en_octets($taille_max),
				'taille'     => taille_en_octets($taille)
			));
		}
	}
	return '';
}
