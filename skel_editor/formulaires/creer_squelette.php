<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

include_spip('inc/skeleditor');

function formulaires_creer_squelette_charger_dist($path_base){
	$valeurs = array('nom'=>'');
	$valeurs['editable'] = autoriser('creerdans','squelette',$path_base);

	return $valeurs;
}

function formulaires_creer_squelette_verifier_dist($path_base){
	$erreurs = array();

	$nom = _request('nom');
	if (strpos($nom,'../')!==FALSE){
		$erreurs['nom'] = _T('skeleditor:erreur_sansgene'); // fichier existe deja
	}
	elseif (file_exists($path_base.$nom))
		$erreurs['nom'] = _T('skeleditor:erreur_overwrite'); // fichier existe deja
	else{
		$chemin = $path_base;
		$sous = explode('/',$nom);
		$filename = array_pop($sous);

		if (!preg_match(",("._SE_EXTENSIONS.")$,", $filename)
			OR preg_match(",("._SE_EXTENSIONS_IMG.")$,", $filename))
			$erreurs['nom'] = _T('skeleditor:erreur_type_interdit');

		else {
			$chemin_ok = "";
			while($chemin AND count($sous) AND $s = array_shift($sous)){
				$chemin_ok = $chemin;
				$chemin = sous_repertoire($chemin, $s);
			}
			if (!$chemin)
				$erreurs['nom'] = _T('skeleditor:erreur_creation_sous_dossier',array('dir'=>joli_repertoire("$chemin_ok/$s")));
		}
	}
	return $erreurs;
}


function formulaires_creer_squelette_traiter_dist($path_base){
	$res = array();

	$nom = _request('nom');
	if (ecrire_fichier($path_base.$nom, ""))
		$res = array('message_ok'=>_T('ok'),'redirect'=>parametre_url(self(),'f',$path_base.$nom));
	else
		$res['message_erreur'] = _T('skeleditor:erreur_ecriture_fichier');

	return $res;
}

?>