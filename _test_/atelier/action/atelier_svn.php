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


function action_atelier_svn_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	if (!$id_auteur) redirige_par_entete('./');

	$rapport = '';

	$nom = _request('nom');
	$type = _request('type');
	$etat = _request('etat');
	$creer_projet = _request('creer_projet');

	if (_request('checkout_projet')) {
		atelier_checkout_projet($nom,$type,$etat,$creer_projet,&$rapport);
		$redirect = parametre_url(urldecode(generer_url_ecrire('atelier')),
				'rapport', $rapport, '&');
	}
	else if (_request('update_projet')) {
		atelier_update_projet($nom,&$rapport);
		$id_projet = $arg;
		$redirect = parametre_url(urldecode(generer_url_ecrire('projets',"id_projet=$id_projet")),
				'rapport', $rapport, '&');
	}
        else if (_request('commit_projet')) {
                $commentaire = _request('commentaire');
                $id_projet = $arg;
		$user = _request('user');
		$pass = _request('pass');
                atelier_commit_projet($nom, $commentaire, $user, $pass, &$rapport);
		$redirect = parametre_url(urldecode(generer_url_ecrire('projets',"id_projet=$id_projet")),
				'rapport', $rapport, '&');
        }
	else if(_request('add_projet')) {
		include_spip('inc/atelier_svn');
		$output = atelier_status_svn(array('nom' =>$nom));
		$fichiers = atelier_recuperer_fichier_add($output);
                $id_projet = $arg;

		foreach($fichiers as $fichier) {
			$n = preg_replace('/\//','_',$fichier);
			$n = preg_replace('/\./','_',$n);

			if (_request('fichier_'.$n) == 'yes') atelier_add_fichier($nom,$fichier,&$rapport);
		}
		$redirect = parametre_url(urldecode(generer_url_ecrire('projets',"id_projet=$id_projet")),
				'rapport', $rapport, '&');	
	}

	include_spip('inc/headers');
	redirige_par_entete($redirect);
}

function atelier_add_fichier($nom,$fichier,&$rapport) {
	$rapport .= _T('atelier:commande'). 'svn add '.$fichier.'<br />';
	exec('cd '._DIR_PLUGINS.';svn add '.$fichier.' 2>&1',&$output,&$return_var);
	$rapport .= _T('atelier:code_retour').$return_var.'<hr />';
	switch ($return_var) {
		case 0 : foreach ($output as $ligne) $rapport .= '   '.$ligne.'<br />';	break;
		case 1 : foreach ($output as $ligne) $rapport .= '   '.$ligne.'<br />'; $rapport .= _T('atelier:erreur_commande');break; // erreur commande
		default : $rapport .= _T('atelier:erreur_svn_pas_installe'); break; // pas de svn !
	}
}

function atelier_commit_projet($nom, $commentaire, $user, $pass, &$rapport) {
        $rapport .= _T('atelier:commande'). 'svn commit --encoding "UTF-8" -m "'.$commentaire.'"<br />';
	if ($commentaire != '') {
		include_spip('inc/filtres');
		exec('cd '._DIR_PLUGINS.$nom.';svn commit --username "'.$user.'" --password "'.$pass.'" --encoding "UTF-8" -m "'.$commentaire.'" 2>&1',&$output,&$return_var);

		$rapport .= _T('atelier:code_retour').$return_var.'<hr />';

		switch ($return_var) {
			case 0 : foreach ($output as $ligne) $rapport .= '   '.$ligne.'<br />';	break;
			case 1 : foreach ($output as $ligne) $rapport .= '   '.$ligne.'<br />'; $rapport .= _T('atelier:erreur_commande');break; // erreur commande
			default : $rapport .= _T('atelier:erreur_svn_pas_installe'); break; // pas de svn !
		}
	}
	else $rapport .= _T('atelier:commit_sans_commentaire') . '<br />';
}

function atelier_status_projet($nom) {
	exec('cd '._DIR_PLUGINS.';svn status -u -v '.$nom.' 2>&1',&$output,&$return_var);
	return $output;
}

function atelier_update_projet($nom,&$rapport) {
	$rapport .= _T('atelier:commande'). 'svn update<br />';
	exec('cd '._DIR_PLUGINS.$nom.';svn update',&$output,&$return_var);
	$rapport .= _T('atelier:code_retour').$return_var.'<hr />';
	
	switch ($return_var) {
		case 0 : foreach ($output as $ligne) $rapport .= '   '.$ligne.'<br />';	break;
		case 1 : $rapport .= _T('atelier:erreur_commande');break; // erreur commande
		default : $rapport .= _T('atelier:erreur_svn_pas_installe'); break; // pas de svn !
	}
}

function atelier_checkout_projet($nom,$type,$etat,$creer_projet,&$rapport) {

	include_spip('inc/atelier_plugins');
	if (!$nom) {
		$rapport .= _T('atelier:erreur_nom_manquant');
		return;
	}

	if (atelier_verifier_repertoire_plugin($nom)) {
		$rapport .= _T('atelier:erreur_deja_present');
		return;
	}

	$url_svn = 'svn://zone.spip.org/spip-zone/_'.$type.'s_/_'.$etat.'_/'.$nom;
	$return_var = 0;
	$rapport .= _T('atelier:commande'). 'svn checkout '.$url_svn . '<br />';
	exec('cd '._DIR_PLUGINS.';svn checkout '.$url_svn.' 2>&1',&$output,&$return_var);
	$rapport .= _T('atelier:code_retour').$return_var.'<hr />';
	foreach ($output as $ligne) $rapport .= '   '.$ligne.'<br />';

	switch ($return_var) {
		case 0 : // ok

			if ($creer_projet=='oui') atelier_svn_creer_projet($nom,$type,&$rapport);
			break;
		case 1 : $rapport .= _T('atelier:erreur_commande');break; // erreur commande
		default : $rapport .= _T('atelier:erreur_svn_pas_installe'); break; // pas de svn !
	}
}

function atelier_svn_creer_projet($nom,$type,&$rapport) {
	include_spip('action/editer_projet');
	$id_projet = insert_projet();
	$c = array(	'titre' => $nom,
			'descriptif' => _T('atelier:projet_importer_svn'),
			'type' => $type,
			'prefixe' => $nom
			);
	revision_projet($id_projet, $c);
	$rapport .= _T('atelier:projet_ajoute').'<br />';
}
?>
