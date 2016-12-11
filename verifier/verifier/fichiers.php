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
 *   - mime au choix 'image','tous_spip','specifique'
 *   - mime_specifique (si l'option 'mime_specifique' est choisi ci-dessus)
 *   - taille_max (en Kio)
 *   - largeur_max (en px)
 *   - hauteur_max (en px)
 * @param array|string &$erreurs_par_fichier
 *   Si on vérifier un upload multiple, un tableau, passé par référence, qui contient le détail des erreurs fichier de $_FILES['fichier'] par fichier
 *   Si on vérifie un upload unique, une chaîne qui contiendra l'erreur du fichier.  
 * @return string
 */
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
		if ($erreur=verifier_fichier_mime($valeur,$cle,$options)){// On commence par vérifier le type
			if (!is_array($erreurs_par_fichier)){
				$erreurs_par_fichier = $erreur;
				return $erreur;
			}
			else{
				$erreurs_par_fichier[$cle] = $erreur;
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
 */
function verifier_fichier_mime($valeur,$cle,$options){
	if ($options['mime'] == 'specifique'){
		if (!in_array($valeur['type'][$cle],$options['mime_specifique'])){
			return _T('verifier:erreur_type_non_autorise',array('name'=>$valeur['name'][$cle]));
		}	
	}	
	return '';
}
