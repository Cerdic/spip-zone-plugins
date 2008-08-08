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

function action_atelier_plugin_xml_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	if (!$id_auteur) redirige_par_entete('./');



	$id_projet = $arg;

	$nom = _request('nom');
	$auteur = _request('auteur');
	$version = _request('version');
	$description = _request('description');
	$etat = _request('etat');
	$lien = _request('lien');
	$options = _request('options');
	$fonctions = _request('fonctions');
	$prefixe = _request('prefixe');
	$new_necessite_id = _request('new_necessite_id');
	$new_necessite_version = _request('new_necessite_version');

	include_spip('inc/xml');
	$fichier = _DIR_PLUGINS.$prefixe.'/plugin.xml';
	$arbre = spip_xml_load($fichier);

	$gabarit = _DIR_PLUGINS.'atelier/gabarits/plugin.txt';
	lire_fichier($gabarit,&$plugin_xml);
	include_spip('inc/atelier_fonctions');
	$plugin_xml = preg_replace('#\[description_projet\]#',text_to_plugin($description),$plugin_xml);
	$plugin_xml = preg_replace('#\[nom_projet\]#',text_to_plugin($nom),$plugin_xml);
	$plugin_xml = preg_replace('#\[auteur_projet\]#',text_to_plugin($auteur),$plugin_xml);
	$plugin_xml = preg_replace('#\[version_projet\]#',$version,$plugin_xml);
	$plugin_xml = preg_replace('#\[etat_projet\]#',$etat,$plugin_xml);
	$plugin_xml = preg_replace('#\[lien_projet\]#',$lien,$plugin_xml);
	$plugin_xml = preg_replace('#\[options_projet\]#',$options,$plugin_xml);
	$plugin_xml = preg_replace('#\[fonctions_projet\]#',$fonctions,$plugin_xml);
	$plugin_xml = preg_replace('#\[prefixe_projet\]#',$prefixe,$plugin_xml);
	$plugin_xml = preg_replace('#\[install_projet\]#',"./",$plugin_xml);

	$keys = $arbre['plugin'][0];
	$dependances = '';
	foreach ($keys as $key => $value) {
		if (preg_match("#necessite\ id='(.*)'\ version='\[(.*);\]'#",$key,$match))
			if (_request('supprimer_dep_'.$match[1]) != 'yes')
				$dependances .= '<necessite id=\''.$match[1].'\' version=\'['.$match[2].';]\' />'."\n";
	}

	if ($new_necessite_id && $new_necessite_version)
		$plugin_xml = preg_replace('#\[necessite\]#',$dependances."<necessite id='$new_necessite_id' version='[$new_necessite_version;]' />",$plugin_xml);
	else
		$plugin_xml = preg_replace('#\[necessite\]#',$dependances,$plugin_xml);



	$fichier = _DIR_PLUGINS.$prefixe.'/plugin.xml';
	ecrire_fichier($fichier,$plugin_xml);

        $redirect = parametre_url(urldecode(generer_url_ecrire('atelier_plugin_xml')),
				'id_projet', $id_projet, '&') . $err;

	include_spip('inc/headers');
	redirige_par_entete($redirect);

}

?>
