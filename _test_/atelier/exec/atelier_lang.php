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

function exec_atelier_lang_dist() {
	exec_atelier_lang_args(	intval(_request('id_projet')),
				_request('fichier')
	);
}

function exec_atelier_lang_args($id_projet,$fichier='') {
	$projet_select = charger_fonction('projet_select','inc');
	$row = $projet_select($id_projet);
	atelier_lang($id_projet,$row,$fichier);
}

function atelier_lang($id_projet,$row,$fichier='') {
	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');
	include_spip('inc/atelier_lang');

	$nom_page = atelier_debut_page(_T('atelier:page_lang'),'atelier_lang');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche($nom_page);

		atelier_cadre_raccourcis(array(
			'<a href="'.generer_url_ecrire('projets','id_projet='.$row['id_projet']).'">'._T('atelier:revenir_projet').'</a>'
		));

		// on liste les fichiers lang
		$dir = _DIR_PLUGINS.$row['prefixe'].'/lang';

		if ($dh = opendir($dir)) {
			$fichiers = array();
			while (($file = readdir($dh)) !== false) {
				if (($file != '.') && ($file != '..'))
					$fichiers[] = '<a href="'.generer_url_ecrire('atelier_lang',"id_projet=$id_projet&fichier=$file").'">'.$file.'</a>';
			}
			closedir($dh);
			if (count($fichiers) > 0) cadre_atelier(_T('atelier:contenu_repertoire_lang'),$fichiers);
		}

		atelier_cadre_infos();

	atelier_debut_droite($nom_page);

		echo debut_cadre_trait_couleur('',true);

		$atelier_lang = charger_fonction('atelier_lang','inc');

		if (!$atelier_lang('verifier_repertoire',array('prefixe' => $row['prefixe']))) {
			echo '<p>'._T('atelier:explication_rep_lang').'</p>';
			echo $atelier_lang('creer_repertoire',array('id_projet' =>$id_projet));
		}
		else {
			echo debut_cadre_couleur('',true);
			echo '<p>'._T('atelier:explication_creer_fichier_lang').'</p>';
			echo $atelier_lang('creer_fichier',array('id_projet' =>$id_projet));
			echo fin_cadre_couleur(true);
		}

		if ($fichier) {
			echo gros_titre($fichier,'',false);
			$module = $row['prefixe'];
			$lang = '';
			if ($atelier_lang('verifier_fichier',array('module' => $module,'fichier' => $fichier))) {
				echo '<br />';
				echo debut_cadre_couleur('',true);
				echo '<p>'._T('atelier:explication_ajouter_lang').'</p>';
				echo $atelier_lang('ajout',array('id_projet' => $id_projet,'module' => $module,'lang' => $lang));
				echo fin_cadre_couleur(true);

				echo debut_cadre_couleur('',true);
				echo $atelier_lang('edit',array('module' => $module,'lang' => $lang));
				echo fin_cadre_couleur(true);
			}
		}

		echo fin_cadre_trait_couleur(true);
	atelier_fin_gauche();
	atelier_fin_page();

	
}

?>
