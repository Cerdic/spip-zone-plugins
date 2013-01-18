<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

function action_skeleditor_new_from_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();

	// $arg est le fichier que l'on veut personaliser
	if (strncmp($arg,_DIR_RACINE,strlen(_DIR_RACINE)!==0))
		$arg = _DIR_RACINE.$arg;

	include_spip('inc/skeleditor');
	$file = skeleditor_nom_copie($arg);
	if ($file){
		include_spip('inc/skeleditor');
		$path_base = skeleditor_path_editable();
		list($chemin,) = skeleditor_cree_chemin($path_base, $file);
		if ($chemin){
			$file = basename($file);

			if (!file_exists($chemin . $file)) {
				lire_fichier($arg, $contenu);
				ecrire_fichier($chemin . $file, skeleditor_commente_copie($arg,$contenu));
			}

			if (file_exists($f=$chemin.$file))
				$GLOBALS['redirect'] = parametre_url(_request('redirect'),'f',$f);
		}
	}

}

?>