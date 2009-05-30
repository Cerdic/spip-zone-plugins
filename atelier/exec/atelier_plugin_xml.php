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

function exec_atelier_plugin_xml_dist() {
	exec_atelier_plugin_xml_args(intval(_request('id_projet')));
}

function exec_atelier_plugin_xml_args($id_projet) {

	
	$projet_select = charger_fonction('projet_select','inc');
	$row = $projet_select($id_projet);
	if (!$row) {
		include_spip('inc/minipres');
		echo minipres(_T('atelier:aucun_projet'));
		exit;
	}

	// si le projet n'est pas un plugin : dehors
	if ($row['type'] !=  "plugin")  {
		include_spip('inc/minipres');
		echo minipres(_T('atelier:que_pour_les_plugins'));
		exit;
	}

	// si le fichier n'existe pas : le generer
	$fichier = _DIR_PLUGINS.$row['prefixe'].'/plugin.xml';
	if (!@file_exists($fichier)) {
		$plugin_xml='';
		lire_fichier(_DIR_PLUGINS.'atelier/gabarits/plugin.txt',&$plugin_xml);

		$plugin_xml = preg_replace('#\[description_projet\]#',$row['descriptif'],$plugin_xml); // attetion formater le descriptif (accents)
		$plugin_xml = preg_replace('#\[nom_projet\]#',$row['titre'],$plugin_xml);
		$plugin_xml = preg_replace('#\[auteur_projet\]#','',$plugin_xml);
		$plugin_xml = preg_replace('#\[version_projet\]#','0.1',$plugin_xml);
		$plugin_xml = preg_replace('#\[etat_projet\]#','dev',$plugin_xml);
		$plugin_xml = preg_replace('#\[lien_projet\]#','',$plugin_xml);
		$plugin_xml = preg_replace('#\[options_projet\]#','',$plugin_xml);
		$plugin_xml = preg_replace('#\[fonctions_projet\]#','',$plugin_xml);
		$plugin_xml = preg_replace('#\[prefixe_projet\]#',$row['prefixe'],$plugin_xml);
		$plugin_xml = preg_replace('#\[necessite\]#','',$plugin_xml);
		$plugin_xml = preg_replace('#\[pipelines\]#','',$plugin_xml);
		$plugin_xml = preg_replace('#\[install_projet\]#','',$plugin_xml);
		$plugin_xml = preg_replace('#\[icon_projet\]#','',$plugin_xml);

		ecrire_fichier(_DIR_PLUGINS.$row['prefixe'].'/plugin.xml',$plugin_xml);
	}
	// si le fichier existe : le lire et mettre toutes les données dans un tableau

	include_spip('inc/xml');
	$arbre = spip_xml_load($fichier);
	atelier_plugin_xml($id_projet, $arbre);
}


function atelier_plugin_xml($id_projet,$arbre) {

	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');

	$nom_page = atelier_debut_page(_T('atelier:titre_plugin_xml'),'atelier_plugin_xml');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche();

		atelier_cadre_raccourcis(array(
			'<a href="'.generer_url_ecrire('projets','id_projet='.$id_projet).'">'._T('atelier:revenir_projet').'</a>'
		));
		atelier_cadre_infos();
	atelier_fin_gauche();
	atelier_debut_droite();

	
		echo debut_cadre_trait_couleur('',true);
		if (is_array($arbre['plugin'][0])) {
			echo 'Nom : ' .$arbre['plugin'][0]['nom'][0] . '<br />';
			echo 'Auteur : ' .$arbre['plugin'][0]['auteur'][0] . '<br />';
			echo 'Version : ' .$arbre['plugin'][0]['version'][0] . '<br />';
			echo 'Description : ' .$arbre['plugin'][0]['description'][0] . '<br />';
			echo 'Etat : ' .$arbre['plugin'][0]['etat'][0] . '<br />';
			echo 'Lien : ' .$arbre['plugin'][0]['lien'][0] . '<br />';
			echo 'Options : ' .$arbre['plugin'][0]['options'][0] . '<br />';
			echo 'Fonctions : ' .$arbre['plugin'][0]['fonctions'][0] . '<br />';
			echo 'Prefixe : ' .$arbre['plugin'][0]['prefix'][0] . '<br />';
		

			$keys = $arbre['plugin'][0];
			if ($keys) {
				$dependances = array();
				foreach ($keys as $key => $value) {
					if (preg_match("#necessite\ id='(.*)'\ version='\[(.*);\]'#",$key,$match))
						$dependances[] = array('id' => $match[1],'version' => $match[2]);
				}
				foreach ($dependances as $necessite) {
					echo 'Dependances : <i>' .$necessite['id']. '</i><b> version </b><i>'. $necessite['version'].'</i><br />';
				}
			}

			if ($arbre['plugin'][0]['pipeline']){
				foreach ($arbre['plugin'][0]['pipeline'] as $pipe) {
					echo 'Pipeline : <i>'.$pipe['nom'][0].'</i><b> inclu dans </b><i>'. $pipe['inclure'][0].'</i><br />';
				}
			}
		}

		echo fin_cadre_trait_couleur(true);

		echo debut_cadre_couleur('',true);
			echo _T('atelier:modifier_plugin_xml') .'&nbsp;:<br />'.gros_titre('Plugin.xml','',false);
			$editer_plugin_xml = charger_fonction('atelier_plugin_xml','inc');
			echo $editer_plugin_xml($id_projet,$arbre,$dependances);
		echo fin_cadre_couleur(true);

	atelier_fin_droite();
	atelier_fin_page();

}

?>
