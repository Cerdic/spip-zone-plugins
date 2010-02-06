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

	// retrouver le chemin dont il vient pour extraire la racine
	// permet aussi de s'assurer que le fichier qu'on copie provient uniquement
	// du path, et pas d'autre part sur le serveur
	$file = "";
	$spip_path = creer_chemin();
	$spip_path = array_diff($spip_path, array(_DIR_RACINE));
	$spip_path[] = _DIR_RACINE;
	foreach($spip_path as $dir) {
		if (strncmp($arg,$dir,strlen($dir))==0){
			$file = substr($arg,strlen($dir));
			break;
		}
	}

	if ($file){
		include_spip('inc/skeleditor');
		$path_base = skeleditor_path_editable();
		list($chemin,) = skeleditor_cree_chemin($path_base, $file);
		if ($chemin){
			$file = basename($file);

			if (!file_exists($chemin . $file)) {
				/* preparer un commenaite */
				$comment = _T('skeleditor:copy_comment',array('date'=>date('Y-m-d H:i:s'),'nom'=>$GLOBALS['visiteur_session']['nom'],'source'=>joli_repertoire($arg)));
				$infos = pathinfo($file);
				if (in_array($infos['extension'],array('php','php3','php4','php5','php6','css','js','as')))
					$comment = "/*\n$comment\n*/\n";
				elseif (in_array($infos['extension'],array('htm','html','xml','svg','rdf')))
					$comment = "<!--\n$comment\n-->\n";
				else $comment='';
				lire_fichier($arg, $contenu);
				ecrire_fichier($chemin . $file, $comment . $contenu);
			}

			if (file_exists($f=$chemin.$file))
				$GLOBALS['redirect'] = parametre_url(_request('redirect'),'f',$f);
		}
	}

}

?>