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

function exec_atelier_svn_dist() {
	exec_atelier_svn_args(intval(_request('id_projet')),
				_request('opendir'));
}

function exec_atelier_svn_args($id_projet='',$opendir='') {
	$projet_select = charger_fonction('projet_select','inc');
	$row = $projet_select($id_projet);
	atelier_svn($id_projet,$row,$opendir);
}

function atelier_svn($id_projet,$row,$opendir='') {
	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');
	include_spip('inc/atelier_svn');

	$nom_page = atelier_debut_page(_T('atelier:page_svn'),'atelier_svn');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche();
		atelier_cadre_raccourcis(array(
			'<a href="'.generer_url_ecrire('projets','id_projet='.$row['id_projet']).'">'._T('atelier:revenir_projet').'</a>'
		));
		atelier_cadre_infos();
	atelier_fin_gauche();
	atelier_debut_droite();

		if ($id_projet) {
//			echo debut_cadre_trait_couleur('',true);

			echo '<p>'._T('atelier:explication_status_svn').'</p>';
			cadre_atelier(' svn status -u -v ' . $row['prefixe'],atelier_status_svn(array('nom' =>$row['prefixe'])));

			if (atelier_verifier_subversion()) {
				echo '<p>'._T('atelier:update_svn').'</p>';

				$projet_svn = charger_fonction('atelier_svn','inc');
				echo $projet_svn('update',array(
					'nom' => $row['prefixe'],
					'id_projet' => $id_projet
					));

                                echo '<p>'._T('atelier:commit_svn').'</p>';

                                echo $projet_svn('commit', array(
                                        'nom' => $row['prefixe'],
                                        'id_projet' => $id_projet
                                        ));

			}
			else {
				echo '<p>'._T('atelier:installer_svn').'</p>';
			}
		}
		else {
			echo debut_cadre_trait_couleur('',true);
			if (atelier_verifier_subversion()) {
				echo '<p>'._T('atelier:projet_svn').'</p>';
	
				$projet_svn = charger_fonction('atelier_svn','inc');
				echo $projet_svn('checkout');
			}
			else {
				echo '<p>'._T('atelier:installer_svn').'</p>';
			}
			echo fin_cadre_trait_couleur(true);
		}

	atelier_fin_droite();
	atelier_fin_page();  
}

?>
