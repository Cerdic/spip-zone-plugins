<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

include_spip('inc/skeleditor');

/**
 * Chargement du formulaire
 * 4 cas possibles :
 *  - un nouveau fichier ($fichier='')
 *  - un fichier a l'extension interdite
 *  - un fichier de squelettes/, modifiable
 *  - un fichier existant (d'un plugin par exe),
 *    qui doit etre copie dans squelettes/ a l'enregistrement
 *
 * @param string $path_base
 * @param string $fichier
 * @return array
 */
function formulaires_editer_squelette_charger_dist($path_base, $fichier){

	$valeurs = array(
		'fichier'=>$fichier,
		'date'=>'',
		'size'=>'',
		'filename'=>'',
		'path_base'=>$path_base,
		'_hidden'=>'',
		'_info_copie'=>'',
		'editable'=>true
	);

	if ($fichier){
		if (!file_exists($fichier)) {
			$valeurs['editable'] = false;
			$valeurs['message_erreur'] = _T('skeleditor:erreur_fichier_inexistant');
			$valeurs['fichier'] = '';
			return $valeurs;
		}

		include_spip('inc/autoriser');
		if (!autoriser('voir','squelette',$fichier)){
			$valeurs['editable'] = false;
			$valeurs['message_erreur'] = _T('skeleditor:erreur_type_interdit');
			$valeurs['fichier'] = '';
			return $valeurs;
		}

		$valeurs['date'] = filemtime($fichier);
		$valeurs['size'] = filesize($fichier);
		if (autoriser('modifier','squelette',$fichier)
			AND strncmp($fichier, $path_base, strlen($path_base))==0)
			$valeurs['filename'] = substr($fichier,strlen($path_base)); // pour le renommage
		elseif(strncmp($fichier, $path_base, strlen($path_base))!=0){
			// c'est une copie dans le dossier squelettes/
			$valeurs['filename'] = skeleditor_nom_copie($fichier);
			$valeurs['_hidden'] .= "<input type='hidden' name='copie_squelette' value='".$valeurs['filename']."' />";
			$valeurs['_info_copie'] = _T('skeleditor:info_copie',array('dir'=>joli_repertoire($path_base)));
		}

	}
	list($valeurs['code'],$valeurs['type'],$ctrl) = skeleditor_get_file_content_type_ctrl($fichier);

	$valeurs['_hidden'] .= "<input type='hidden' name='ctrl_md5' value='$ctrl' />"; // un hash pour eviter les problemes de modif concourantes
	

	return $valeurs;
}

function formulaires_editer_squelette_verifier_dist($path_base, $fichier){
	$erreurs = array();
	
	if ($fichier){ // cas d'une modif
		if (!file_exists($fichier))
			$erreurs['code'] = _T('skeleditor:erreur_fichier_supprime'); // fichier supprime entre temps
		else{
			if (!_request('copie_squelette') AND !autoriser('modifier','squelette',$fichier)){
				$erreurs['code'] = _T('skeleditor:erreur_fichier_modif_interdite');
			}
			else {
				list($content,$type,$ctrl) = skeleditor_get_file_content_type_ctrl($fichier);
				if ($ctrl!=_request('ctrl_md5')){
					// fichier modifie entre temps
					$erreurs['code'] = _T('skeleditor:erreur_fichier_modif_coucourante');
					if ($type=='txt')
						$erreurs['code'] .=
							"<textarea readonly='readonly' cols='80' rows='30'>$content</textarea>"
							._T('skeleditor:erreur_fichier_modif_coucourante_votre_version');
				}
				if ($filename = _request('filename') AND $path_base.$filename!=$fichier){
					if ($e=skeleditor_verifie_nouveau_nom($path_base,$filename,$type=='img'))
						$erreurs['filename'] = $e;
				}
			}
		}
	}
	else { // creation d'un fichier
		if (!$filename = _request('filename')){
			$erreurs['filename'] = _T('info_obligatoire');
		}
		else {
			if (!autoriser('creerdans','squelette',$path_base))
				$erreurs['filename'] = _T('skeleditor:erreur_sansgene');
			elseif ($e=skeleditor_verifie_nouveau_nom($path_base,$filename))
				$erreurs['filename'] = $e;
		}
	}

	return $erreurs;
}


function formulaires_editer_squelette_traiter_dist($path_base, $fichier){
	$res = array();
	if ($fichier){
		list($content,$type,$ctrl) = skeleditor_get_file_content_type_ctrl($fichier);
		if ($type=='txt'){
			$code = _request('code');
			if (_request('copie_squelette')){
				$code = skeleditor_commente_copie($fichier,$code);
				$fichier = $path_base._request('filename');
				$res['redirect'] = parametre_url(self(),'f',$fichier);
			}
			if (ecrire_fichier($fichier,$code))
				$res['message_ok'] = _T('skeleditor:fichier_enregistre');
			else
				$res['message_erreur'] = _T('skeleditor:erreur_ecriture_fichier');
		}
		elseif(_request('copie_squelette')){
			$dest = $path_base._request('filename');
			copy($fichier, $dest);
			$fichier = $dest;
			$res['redirect'] = parametre_url(self(),'f',$fichier);
		}
		if (!isset($res['message_erreur'])
			AND $filename=_request('filename')
			AND $path_base.$filename!=$fichier
			AND autoriser('modifier','squelette',$fichier)){
			if (rename($fichier, $path_base.$filename)){
				$res['redirect'] = parametre_url(self(),'f',$path_base.$filename);
			}
		}
	}
	elseif ($filename=_request('filename')) {
		if (ecrire_fichier($path_base.$filename,"")){
			$res['message_ok'] = _T('skeleditor:fichier_enregistre');
			$res['redirect'] = parametre_url(self(),'f',$path_base.$filename);
		}
	}

	return $res;
}
?>