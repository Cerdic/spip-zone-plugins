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

function action_atelier_objets_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	if (!$id_auteur) redirige_par_entete('./');

	global $repertoire_squelettes_alternatifs; // plugin switcher

	$id_projet = $arg;
	$r = sql_fetsel('prefixe, type','spip_projets',"id_projet=$id_projet");
	$prefixe = $r['prefixe'];
	$type = _request('type');
	$nom = _request('nom');
	$rapport = '';

	switch($type) {
		case 'html' : 
			if ($r['type'] == "plugin") $fichier = _DIR_PLUGINS . $prefixe .'/'.$nom.'.html';
			else $fichier = './' . $repertoire_squelettes_alternatifs . '/'. $prefixe .'/'.$nom.'.html';
			break;
		case 'table' :
			$chemin = _DIR_PLUGINS . $prefixe . '/base';
			$fichier = $chemin.'/'.$prefixe.'_base.php';
			if (!file_exists($chemin)) {
				$rapport .= 'cr&eacute;ation du r&eacute;pertoire base.<br />';
				exec('cd '.escapeshellarg(_DIR_PLUGINS . $prefixe).';mkdir base',&$output,&$return_var);
				$rapport .= _T('atelier:code_retour').$return_var.'<br />';
				foreach ($output as $ligne) $rapport .= '   '.$ligne.'<br />';
			}
			lire_fichier(_DIR_PLUGINS .'atelier/gabarits/base.txt',&$contenu);
			$contenu = preg_replace('#\[nom_table\]#',$nom,$contenu);
			if (file_exists($fichier)) { // le fichier existe, il faut le lire et pas le créer
			}
			else {
			
			}
		default : 
			if ($r['type'] == "plugin") {
				$fichier = _DIR_PLUGINS . $prefixe .'/'. $type .'/'.$prefixe.'_'.$nom.'.php';
				if (!file_exists(_DIR_PLUGINS . $prefixe .'/'. $type)) { // si le repertoire $type n'existe pas, le créer
					$rapport .= 'cr&eacute;ation du r&eacute;pertoire '. $type . '<br />';
					exec('cd '.escapeshellarg(_DIR_PLUGINS . $prefixe).';mkdir '.escapeshellarg($type),&$output,&$return_var);
					$rapport .= _T('atelier:code_retour').$return_var.'<br />';
					foreach ($output as $ligne) $rapport .= '   '.$ligne.'<br />';
				}

			}
			else $fichier = './' . $repertoire_squelettes_alternatifs . '/'. $prefixe .'/'. $type .'/'.$prefixe.'_'.$nom.'.php';
			break;
	}


	$contenu = '';
	$output = array();

	// recupere le gabarit
	lire_fichier(_DIR_PLUGINS .'atelier/gabarits/'.$type.'.txt',&$contenu);

	$contenu = preg_replace('#\[nom_objet\]#',$prefixe.'_'.$nom,$contenu);
	$contenu = preg_replace('#\[prefixe\]#',$prefixe,$contenu);

	ecrire_fichier($fichier,$contenu);

	$rapport .= 'cr&eacute;ation du fichier '.$fichier.'<br />';
	if ($type=="exec") $rapport .= 'url d\'appel : exec='.$prefixe.'_'.$nom.'<br />';
	if ($type=="html") $rapport .= 'url d\'appel : spip.php?page='.$nom.'<br />';

	$redirect = parametre_url(urldecode(generer_url_ecrire('projets',"id_projet=$id_projet")),
				'rapport', $rapport, '&') . $err;

	include_spip('inc/headers');
	redirige_par_entete($redirect);
}

?>
