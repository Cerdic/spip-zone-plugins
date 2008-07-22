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

	$id_projet = $arg;
	$r = sql_fetsel('prefixe','spip_projets',"id_projet=$id_projet");
	$prefixe = $r['prefixe'];
	$type = _request('type');
	$nom = _request('nom');

	$fichier = _DIR_PLUGINS . $prefixe .'/'. $type .'/'.$prefixe.'_'.$nom.'.php';
	$rapport = '';
	$contenu = '';
	$output = array();

	// recupere le gabarit
	lire_fichier(_DIR_PLUGINS .'atelier/gabarits/'.$type.'.txt',&$contenu);

	$contenu = preg_replace('#\[nom_objet\]#',$prefixe.'_'.$nom,$contenu);
	$contenu = preg_replace('#\[prefixe\]#',$prefixe,$contenu);

	// si le repertoire $type n'existe pas, le créer
	if (!file_exists(_DIR_PLUGINS . $prefixe .'/'. $type)) {
		$rapport .= 'cr&eacute;ation du r&eacute;pertoire '. $type . '<br />';
		exec('cd '._DIR_PLUGINS . $prefixe.';mkdir '.$type,&$output,&$return_var);
		$rapport .= _T('atelier:code_retour').$return_var.'<br />';
		foreach ($output as $ligne) $rapport .= '   '.$ligne.'<br />';
	}

	ecrire_fichier($fichier,$contenu);

	$rapport .= 'cr&eacute;ation du fichier '.$fichier.'<br />';
	if ($type=="exec") $rapport .= 'url d\'appel : exec='.$prefixe.'_'.$nom.'<br />';

	$redirect = parametre_url(urldecode(generer_url_ecrire('projets',"id_projet=$id_projet")),
				'rapport', $rapport, '&') . $err;

	include_spip('inc/headers');
	redirige_par_entete($redirect);
}

?>
