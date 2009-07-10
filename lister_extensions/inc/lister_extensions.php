<?php
	include_spip('inc/presentation');
	
    function inc_lister_extensions(){
    	$ret = "";
		if ($extensions = liste_extentions()) {
			$puce_etat = array(
				"dev"=>"<img src='". chemin_image('puce-poubelle.gif') . "' width='9' height='9' alt='"._T('plugin_etat_developpement')."' />",
				"test"=>"<img src='". chemin_image('puce-orange.gif') . "' width='9' height='9' alt='"._T('plugin_etat_test')."' />",
				"stable"=>"<img src='". chemin_image('puce-verte.gif') . "' width='9' height='9' alt='"._T('plugin_etat_stable')."' />",
				"experimental"=>"<img src='". chemin_image('puce-rouge.gif') . "' width='9' height='9' alt='"._T('plugin_etat_experimental')."' />",
				);
			
			$ret .= debut_cadre_enfonce('', '', '', _T('lister_extensions:titre_boite'));
			ksort($extensions);
			$ret .= '<dl>';
			foreach ($extensions as $extension => $donnee)
				$ret .= "<dt>".$puce_etat[$donnee['etat']]." ".$donnee['nom']." (". $donnee['version'].")</dt><dd>".joli_repertoire($donnee['dir'])."</dd>";
			$ret .= '</dl>';
			$ret .= fin_cadre_enfonce(true);
		}
		
    	return $ret;
    }
	
	function liste_extentions(){
		include_spip('inc/plugin');
		$listes = liste_plugin_files(_DIR_EXTENSIONS);
		$infos = array();
			foreach($listes as $k=>$plug) {
				// renseigner ce plugin
				$infos[$dir_type][$plug] = plugin_get_infos($plug,false,_DIR_EXTENSIONS);
				// si il n'y a pas d'erreur
				if (!isset($infos[$dir_type][$plug]['erreur'])) {
					// regarder si on a pas deja selectionne le meme plugin dans une autre version
					$version = isset($infos[$dir_type][$plug]['version'])?$infos[$dir_type][$plug]['version']:NULL;
					if (isset($liste_non_classee[$p=strtoupper($infos[$dir_type][$plug]['prefix'])])){
						// prendre le plus recent
						if (version_compare($version,$liste_non_classee[$p]['version'],'>'))
							unset($liste_non_classee[$p]);
						else{
							continue;
						}
					}
					// ok, le memoriser
					$liste_non_classee[$p] = array(
						'nom' => $infos[$dir_type][$plug]['nom'],
						'etat' => $infos[$dir_type][$plug]['etat'],
						'dir'=>$plug,
						'version'=>isset($infos[$dir_type][$plug]['version'])?$infos[$dir_type][$plug]['version']:NULL
					);
				}
			}
		return $liste_non_classee;
	}
?>