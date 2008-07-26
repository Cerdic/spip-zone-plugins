<?php

/*
 *  Plugin Atelier pour SPIP
 *  Copyright (C) 2008  Polez Kévin
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function atelier_explorer($prefixe,$id_projet,$type,$opendir,$nom_page) {
	// navigation dans les fichiers du répertoire

	global $repertoire_squelettes_alternatifs; // plugin switcher

	if ($type == 'plugin') $dir = _DIR_PLUGINS.$prefixe;
	else  $dir = '../'.$repertoire_squelettes_alternatifs.'/'.$prefixe;

	if ($opendir) $dir .= '/' .$opendir;

	if ($dh = opendir($dir)) {

		$lignes = array();
		while (($file = readdir($dh)) !== false) {
			if ($opendir) $chemin = $opendir .'/'. $file;
			else $chemin = $file;
			if ($file == '..') { // descendre dans l'arborescence
				if ($dir != _DIR_PLUGINS.$prefixe) { // si on est pas a la racine
					$arbo = explode('/',$chemin);
					array_pop($arbo);array_pop($arbo);
					$chemin = '';
					foreach($arbo as $a) $chemin .= $a . '/';
					$chemin = substr($chemin, 0, -1);
					$lignes[] = 
					'<a href="'.generer_url_ecrire($nom_page,'opendir='.$chemin.'&id_projet='.$id_projet).'"><b>['.$file .']</b></a>';
				}
			}
			else if ($file != '.') {

				if (@filetype($dir .'/'. $file) == 'dir')
					$lignes[] = 
					'<a href="'.generer_url_ecrire($nom_page,'opendir='.$chemin.'&id_projet='.$id_projet).'"><b>['.$file .']</b></a>';
				else {
					$l  = '<a href="'.generer_url_ecrire('atelier_edit_fichier','fichier='.$chemin.'&id_projet='.$id_projet).'">'.$file .'</a>';
					preg_match('#(.*).php#',$file,$match);
					if ($opendir =='exec') $l .= ' <span style="margin-top:-13px;float:right;"><a href="'.generer_url_ecrire($match[1]).'">Visualiser la feuille</a></span>';
					$lignes[] = $l;

				}
			}
		}
		array_multisort($lignes,SORT_STRING);
		arsort($lignes);
		$titre = $dir;
		include_spip('inc/atelier_svn');
		if (atelier_verifier_projet_svn($prefixe))
			$titre = $dir . ' [COPIE DE TRAVAIL]';
		
		$id = 'e'.substr(md5(_DIR_PLUGINS.$prefixe.'/'.$dir),0,8);
		atelier_cadre_depliable($titre,"Explorateur",$lignes,$id);

		closedir($dh);
	}
}

?>
