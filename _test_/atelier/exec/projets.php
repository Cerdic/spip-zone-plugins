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

function exec_projets_dist() {

	exec_projets_args(intval(_request('id_projet')));
}

function exec_projets_args($id_projet) {
	$projet_select = charger_fonction('projet_select','inc');
	$row = $projet_select($id_projet);

	projets($id_projet,$row);
}

function projets($id_projet,$row) {

	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');
	include_spip('inc/plugin');

	$nom_page = atelier_debut_page(_T('atelier:titre_projets'),'projets');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche($nom_page);

		atelier_cadre_raccourcis();
		cadre_atelier(_T('atelier:action'),array(
			'<a href="'.generer_url_ecrire('projets_edit','id_projet='.$id_projet).'">'._T('atelier:modifier_projet').'</a>',
			'<a href="'.generer_url_ecrire('taches_edit','new=oui&id_projet='.$id_projet).'">'._T('atelier:ajouter_tache').'</a>',
			'<a href="'.generer_url_ecrire('atelier_plugin_xml','id_projet='.$id_projet).'">'._T('atelier:plugin_xml').'</a>'
		));
		$cfg = plugin_get_infos('cfg');
		if (!isset($cfg['erreur'][0])){
			cadre_atelier(_T('atelier:cfg'),array(
			'<a href="'.generer_url_ecrire('atelier_ajouter_cfg','id_projet='.$id_projet).'">'._T('atelier:ajouter_page_cfg').'</a>',
			'<a href="'.generer_url_ecrire('atelier_voir_cfg','id_projet='.$id_projet).'">'._T('atelier:voir_variables_cfg').'</a>'
			));
		}

		atelier_cadre_infos();

	atelier_debut_droite($nom_page);

		echo debut_cadre_trait_couleur('',true);

		echo gros_titre($row['id_projet'].' - '. $row['titre'],'',false);
		echo '<b>'._T('atelier:texte_prefixe') .' : </b>'.$row['prefixe'] .'<br />';
		echo '<b>'._T('atelier:texte_type').' : </b>'.$row['type'].'<br /><br />';;
		
		echo debut_cadre_couleur('',true);

		echo '<b>'._T('atelier:texte_descriptif').' :</b><br />'
			.propre($row['descriptif']);

		echo fin_cadre_couleur(true);

		echo liste_taches_ouvertes($row['id_projet']);
		echo "generer TODO.txt";
		echo liste_taches_fermees($row['id_projet']);
		echo liste_taches($row['id_projet']);


		echo fin_cadre_trait_couleur(true);

	

		echo debut_cadre_trait_couleur('',true,'',_T('atelier:repertoire')." "._DIR_PLUGINS.$row['prefixe']);

		if ($row['type'] == 'plugin') {
			include_spip('inc/atelier_plugins');
			$verifier_droits = charger_fonction('atelier_plugins','inc');
			if ($verifier_droits('verifier_droits')) {

				$verifier_repertoire = charger_fonction('atelier_plugins','inc');
				if (!$verifier_repertoire('verifier_repertoire',$row['prefixe'])) {
					echo '<p>'._T('atelier:repertoire_inexistant').'</p>';
					$creer_repertoire = charger_fonction('atelier_plugins','inc');
					echo $creer_repertoire('creer_repertoire',$row['id_projet']);
			
				}
				else {
					// navigation dans les fichiers du répertoire
					$dir = _DIR_PLUGINS.$row['prefixe'];
					if ($dh = opendir($dir)) {
						echo "<ul>";
						while (($file = readdir($dh)) !== false) {
							echo "<li> $file -- " . @filetype($dir .'/'. $file) . "</li>";
						}
						echo "</ul>";
						closedir($dh);
					}
				}
			}
			else echo '<p>'._T('atelier:droit_insuffisant').'</p>';

		}
		echo fin_cadre_trait_couleur(true);

	atelier_fin_gauche();
	atelier_fin_page();
}

function liste_taches($id_projet) {
	include_spip('inc/afficher_objets');

	$afficher_projets = charger_fonction('afficher_objets','inc');

	$titre = _T('atelier:liste_taches');
	$requete = array('SELECT' => 'taches.id_tache, taches.titre ',
			 'FROM' => "spip_taches as taches",
			 'WHERE' => "taches.id_projet=$id_projet");
	return $afficher_projets('tache',$titre,$requete);
}

function liste_taches_ouvertes($id_projet) {
	include_spip('inc/afficher_objets');

	$afficher_projets = charger_fonction('afficher_objets','inc');

	$titre = _T('atelier:liste_taches_ouvertes');
	$requete = array('SELECT' => 'taches.id_tache, taches.titre ',
			 'FROM' => "spip_taches as taches",
			 'WHERE' => "taches.id_projet=$id_projet AND taches.etat='ouverte'");
	return $afficher_projets('tache',$titre,$requete);
}

function liste_taches_fermees($id_projet) {
	include_spip('inc/afficher_objets');

	$afficher_projets = charger_fonction('afficher_objets','inc');

	$titre = _T('atelier:liste_taches_fermees');
	$requete = array('SELECT' => 'taches.id_tache, taches.titre ',
			 'FROM' => "spip_taches as taches",
			 'WHERE' => "taches.id_projet=$id_projet AND taches.etat='fermee'");
	return $afficher_projets('tache',$titre,$requete);
}


?>
