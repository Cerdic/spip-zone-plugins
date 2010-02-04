<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

function formulaires_editer_squelette_charger($fichier){

	if (!$fichier OR !file_exists($fichier)) return false; // rien a editer
	$valeurs = array('fichier'=>$fichier);
	lire_fichier($fichier, $valeurs['texte']);
	$valeurs['_hidden'] = "<input type='hidden' name='ctrl_md5' value='".md5($valeurs['texte'])."' />"; // un hash pour eviter les problemes de modif concourantes

	include_spip('inc/autoriser');
	$valeurs['editable'] = autoriser('modifier','squelette',$fichier);

	return $valeurs;
}

function formulaires_editer_squelette_verifier($fichier){
	$erreurs = array();
	
	if (!file_exists($fichier))
		$erreurs['texte'] = _T('skeleditor:erreur_fichier_supprime'); // fichier supprime entre temps
	else{
		if (!autoriser('modifier','squelette',$fichier)){
			$erreurs['texte'] = _T('skeleditor:erreur_fichier_modif_interdite');
		}
		else {
			lire_fichier($fichier, $content);
			$ctrl_md5 = md5($content);
			if ($ctrl_md5!=_request('ctrl_md5')){
				// fichier modifie entre temps
				$erreurs['texte'] = _T('skeleditor:erreur_fichier_modif_coucourante')
				."<textarea cols='80' rows='30'>$content</textarea>"
				._T('skeleditor:erreur_fichier_modif_coucourante_votre_version');
			}
		}
	}

	return $erreurs;
}

?>