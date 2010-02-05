<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

include_spip('inc/skeleditor');

function formulaires_editer_squelette_charger_dist($fichier){

	if (!$fichier OR !file_exists($fichier)) return false; // rien a editer

	$path = pathinfo($fichier);
	if (!preg_match(",("._SE_EXTENSIONS.")$,ims",$fichier))
		return false; // interdit de toucher a ce type de fichier ni meme de le voir

	$valeurs = array('fichier'=>$fichier);

	list($valeurs['texte'],$valeurs['type'],$ctrl) = skeleditor_get_file_content_type_ctrl($fichier);
	$valeurs['_hidden'] = "<input type='hidden' name='ctrl_md5' value='$ctrl' />"; // un hash pour eviter les problemes de modif concourantes

	$valeurs['date'] = filemtime($fichier);
	$valeurs['size'] = filesize($fichier);
	
	include_spip('inc/autoriser');
	$valeurs['editable'] = autoriser('modifier','squelette',$fichier);

	return $valeurs;
}

function formulaires_editer_squelette_verifier_dist($fichier){
	$erreurs = array();
	
	if (!file_exists($fichier))
		$erreurs['texte'] = _T('skeleditor:erreur_fichier_supprime'); // fichier supprime entre temps
	else{
		if (!autoriser('modifier','squelette',$fichier)){
			$erreurs['texte'] = _T('skeleditor:erreur_fichier_modif_interdite');
		}
		else {
			list($content,$type,$ctrl) = skeleditor_get_file_content_type_ctrl($fichier);
			if ($ctrl!=_request('ctrl_md5')){
				// fichier modifie entre temps
				$erreurs['texte'] = _T('skeleditor:erreur_fichier_modif_coucourante');
				if ($type=='txt')
					$erreurs['texte'] .=
						"<textarea readonly='readonly' cols='80' rows='30'>$content</textarea>"
						._T('skeleditor:erreur_fichier_modif_coucourante_votre_version');
			}
		}
	}

	return $erreurs;
}


function formulaires_editer_squelette_traiter_dist($fichier){
	$res = array();
	list($content,$type,$ctrl) = skeleditor_get_file_content_type_ctrl($fichier);
	if ($type=='txt'){
		if (ecrire_fichier($fichier,_request('texte')))
			$res['message_ok'] = _T('skeleditor:fichier_enregistre');
		else
			$res['message_erreur'] = _T('skeleditor:erreur_ecriture_fichier');
	}

	return $res;
}
?>