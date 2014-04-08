<?php

/*
 *  Plugin Bouquinerie pour SPIP
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

include_spip('inc/ajouter_documents'); // pour l'ajout de documents

function action_importer_catalogue_dist() {
	global $_FILES, $HTTP_POST_FILES;

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// recuperer paramètres
	$id_catalogue = _request('id_catalogue');
	$format = _request('type');
	$import_image = _request('import_image');
	$motscles = _request('MotsCles');
	$doublons = array ('total' => _request('total'), 'titre' => _request('DoublonTitre'),'isbn' => _request('DoublonIsbn'));


	// compatibilité php < 4.1
	if (!$_FILES) $_FILES = $GLOBALS['HTTP_POST_FILES'];

	// récupération des variables
	$fichier = $_FILES['fichier']['name'];
	$size = $_FILES['fichier']['size'];
	$tmp = $_FILES['fichier']['tmp_name'];
	$type = $_FILES['fichier']['type'];
	$error = $_FILES['fichier']['error'];

	$rapport = '';

	// Intercepter une erreur a l'envoi
	if (check_upload_error($error)) {
		$rapport .= _T('bouq:erreur_upload');
	}
	else {

		// verification si extention OK
		$tableau = explode('.', $fichier);
		$type_ext = $tableau[1];

		if ($type_ext == 'ods') {
		
			
			$f = stock_fichier($tmp,$fichier);

			if ($id_catalogue == "new") { // si demande de création d'un catalogue pour l'occas
				include_spip('action/editer_catalogue');
				$id_catalogue = insert_catalogue();
				revision_catalogue($id_catalogue, array(
					'titre' => 'Nouveau catalogue',
					'descriptif'=> filtrer_entites(_T('bouq:importe_le').date("d-m-Y")._T('bouq:a'). date("H:i"))
					));
			}

			if ($format == "priceminister") {

				if (!traiter_fichier_priceminister($f['path'].'/content.xml',$id_catalogue,$doublons,$import_image,$motscles))
					$rapport .= _T('bouq:erreur_traitement_fichier');
				$rapport .= _T('bouq:importation_reussie');
			}

			if ($format == "bouquinerie") {
				if (!traiter_fichier_bouquinerie($f['path'].'/content.xml',$id_catalogue,$doublons))
					$rapport .= _T('bouq:erreur_traitement_fichier');
				$rapport .= _T('bouq:importation_reussie');
			}


			// supprimer le fichier temporaire
			efface_fichier($f);
		}
		else {
			$rapport .= _T('bouq:erreur_extension');
		}

	}

	$redirect = parametre_url(urldecode(generer_url_ecrire('admin_bouquinerie')),
				'rapport', $rapport, '&');
	redirige_par_entete($redirect);
}

//nouveau_nom stock_fichier(fichier_temporaire,nom_du_fichier);
function stock_fichier($tmp,$fichier) {
	$uid = uniqid();
	$f =_DIR_TMP .$uid.'_'.$fichier; 
	$source = deplacer_fichier_upload($tmp, $f);
	$path = _DIR_TMP.'bouq_'.$uid;

	// unzip du fichier
	@exec('unzip '.escapeshellarg($f).' -d '.escapeshellarg($path) , $r, $e);

	$fichier = array();
	$fichier['path'] = $path;
	$fichier['file'] = $f;
	return $fichier;
}

function efface_fichier($fichier) {
	@unlink($fichier['file']);
	@exec('rm -R '. escapeshellarg($fichier['path']));
	return;
}

// reçoit une structure livre de type bouquinerie
// pour l'ajouter dans la BD en tant qu'objet livre

function ajouter_livre_bouquinerie($cell, $id_catalogue, $doublons) {

	include_spip('action/editer_livre');

	if ($cell[1]['type'] == "null") return;	// on ajoute seulement si il y a un titre ...

	// critères discriminants

	if ($doublons['total'] == "oui") {
		if ($doublons['titre'] == "oui") {
			$q = sql_select('titre','spip_livres','titre = '.sql_quote($cell[1]['value']));
			while ($r = sql_fetch($q)) if ($r['titre'] == $cell[1]['value']) return;
		}

		if (($doublons['isbn'] == "oui") && ($cell[4]['value'] != '')) {
			$q = sql_select('isbn','spip_livres','isbn = '.sql_quote($cell[6]['value']));
			while ($r = sql_fetch($q)) if ($r['isbn'] == $cell[6]['value']) return;
		}
	}
	else {
		if ($doublons['titre'] == "oui") {
			$q = sql_select('titre','spip_livres','titre = '.sql_quote($cell[1]['value']).' AND id_catalogue = '.$id_catalogue);
			while ($r = sql_fetch($q)) if ($r['titre'] == $cell[1]['value']) return;
		}

		if (($doublons['isbn'] == "oui") && ($cell[4]['value'] != '')) {
			$q = sql_select('isbn','spip_livres','isbn = '.sql_quote($cell[6]['value']).' AND id_catalogue = '.$id_catalogue);
			while ($r = sql_fetch($q)) if ($r['isbn'] == $cell[6]['value']) return;
		}
	}

	$livre = array();

	$id_livre = insert_livre();


	$livre['titre'] = $cell[1]['value']; 
	$livre['auteur'] = $cell[2]['value'];
	$livre['illustrateur'] = $cell[3]['value'];  
	$livre['edition'] = $cell[4]['value']; 
	$livre['prix_vente'] = $cell[5]['value'];
	$livre['isbn'] = $cell[6]['value'];
	$livre['id_catalogue'] = $id_catalogue;
	$livre['type_import'] = "bouquinerie";
	$livre['etat_livre'] = $cell[9]['value'];
	$livre['etat_jaquette'] = $cell[10]['value'];
	$livre['format'] = $cell[11]['value'];
	$livre['reliure'] = $cell[12]['value'];
	$livre['type_livre'] = $cell[13]['value'];
	$livre['lieu_edition'] = $cell[14]['value'];
	$livre['annee_edition'] = $cell[15]['value'];
	$livre['num_edition'] = $cell[16]['value'];
	$livre['inscription'] = $cell[17]['value'];
	$livre['remarque'] = $cell[18]['value'];
	$livre['commentaire'] = $cell[19]['value'];
	$livre['prix_achat'] = $cell[20]['value'];
	$livre['lieu'] = $cell[21]['value'];
	$livre['num_facture'] = $cell[22]['value'];

	$update = array();
	foreach ($livre as $key => $val)
		$update[] = $key . '=' . sql_quote($val);

	if (!count($update)) return;

	sql_query("UPDATE spip_livres SET ".join(', ', $update)." WHERE id_livre=$id_livre");
	sql_insertq("spip_livres_catalogues",array('id_livre' => $id_livre, 'id_catalogue' => $id_catalogue));

}

// reçoit une structure livre de type priceminister
// pour l'ajouter dans la BD en tant qu'objet livre

function ajouter_livre_priceminister($cell, $id_catalogue,$doublons, $import_image,$motscles) {

	include_spip('action/editer_livre');

	if ($cell[5]['type'] == "null") return;	// on ajoute seulement si il y a un titre ...

	// critères discriminants

	if ($doublons['total'] == "oui") {
		if ($doublons['titre'] == "oui") {
			$q = sql_select('titre','spip_livres','titre = '.sql_quote($cell[5]['value']));
			while ($r = sql_fetch($q)) if ($r['titre'] == $cell[5]['value']) return;
		}

		if (($doublons['isbn'] == "oui") && ($cell[4]['value'] != '')) {
			$q = sql_select('isbn','spip_livres','isbn = '.sql_quote($cell[4]['value']));
			while ($r = sql_fetch($q)) if ($r['isbn'] == $cell[4]['value']) return;
		}
	}
	else {
		if ($doublons['titre'] == "oui") {
			$q = sql_select('titre','spip_livres','titre = '.sql_quote($cell[5]['value']).' AND id_catalogue = '.$id_catalogue);
			while ($r = sql_fetch($q)) if ($r['titre'] == $cell[5]['value']) return;
		}

		if (($doublons['isbn'] == "oui") && ($cell[4]['value'] != '')) {
			$q = sql_select('isbn','spip_livres','isbn = '.sql_quote($cell[4]['value']).' AND id_catalogue = '.$id_catalogue);
			while ($r = sql_fetch($q)) if ($r['isbn'] == $cell[4]['value']) return;
		}
	}

	$livre = array();

	$id_livre = insert_livre();

	$livre['id_reference'] = $cell[0]['value']; // PRODUCT_ID
	// $cell[1]['value']; // ADVERT_ID
	// $cell[2]['value']; // SELLER_REF
	// $cell[3]['value']; // EAN
	$livre['isbn'] = $cell[4]['value']; // ISBN
	$livre['titre'] = $cell[5]['value']; // TITLE
	$livre['auteur'] = $cell[6]['value']; // Auteur
	$livre['edition'] = $cell[7]['value']; // Editeur
	// $cell[8]['value']; // Description
	// $cell[9]['value']; // code type
	// $cell[10]['value']; // code support
	$livre['prix_vente'] = $cell[11]['value']; // prix
	// $cell[12]['value']; // quantite
	$livre['etat_livre'] = $cell[13]['value']; // qualite
	$livre['commentaire'] = $cell[14]['value']; // commentaire
	$livre['type_livre'] = $cell[15]['value']; // livre/taille
	$livre['lieu_edition'] = $cell[16]['value']; // lieu de parution
	// $cell[17]['value']; // livre/periode
	// $cell[18]['value']; // livre/langue
	// $cell[19]['value']; // taux de tva
	// $cell[20]['value']; // livre/categorie prix
	// $cell[21]['value']; // livre/tome
	// $cell[22]['value']; // livre/longueur
	// $cell[23]['value']; // livre/largeur
	// $cell[24]['value']; // livre/epaisseur
	// $cell[25]['value']; // livre/traducteur
	// $cell[26]['value']; // livre/langue d'origine

	// gestion des images liés
	if (($cell[27]['type'] != 'null') && ($cell[28]['type'] != 'null')) { // url image/ secondaire; // url image/ principale
		if ($import_image != 'non') {
			$url_price = 'http://www.priceminister.com/offer/buy/'.$livre['id_reference'].'/';
			if ($page = file_get_contents($url_price)) {

				if (preg_match('#<div id="fp_pix">#',$page,$match, PREG_OFFSET_CAPTURE) > 0) {
					$tab = preg_split('#<div id="fp_pix">#',$page);
					$tab_2 = preg_split('#</div>#',$tab[1]);

					if (preg_match('#src="(.+?)"#',$tab_2[0],$m) > 0 ) {
						$livre['url_image'] = $m[1];
						if ($import_image == 'oui') $mode = "image";
						else if ($import_image == 'distant') $mode = "distant";

						if ($import_image != 'url') {
							$fichier = 'img_'.$titre.'.jpg';

							$ajouter_document = charger_fonction('ajouter_documents','inc');
							$ajouter_document($m[1],$fichier,"livre",$id_livre,$mode,$id_document,$documents_actifs);
						}
					}
				}
			}
		}
	}

	// $cell[27]['value']; // url image/ principale
	// $cell[28]['value']; // url image/ secondaire

	// $cell[29]['value']; // classification decitre 1
	// $cell[30]['value']; // classification decitre 2
	// $cell[31]['value']; // classification decitre 3
	$livre['format'] = $cell[32]['value']; // livre/format
	// $cell[33]['value']; // proprietaire
	// $cell[34]['value']; // livre/collection
	// $cell[35]['value']; // livre n° de collection
	// $cell[36]['value']; // livre/nombre de page

	// gestion des mots-cle

	if (($cell[37]['type'] != "null") && ($motscles == 'oui')) {  // classification Tilelive primaire

		// création du groupe_mot si necessaire
		$id_groupe = sql_fetsel("id_groupe, titre","spip_groupes_mots",'titre = '.sql_quote($cell[37]['value']));
		if (!intval($id_groupe['id_groupe'])) {
			$groupe = array();
			$groupe['titre'] = $cell[37]['value'];
			$groupe['unseul'] = "non";
			$groupe['obligatoire'] = "non";
			$groupe['articles'] = "oui";
			$groupe['breves'] = "oui";
			$groupe['syndic'] = "oui";
			$groupe['minirezo'] = "oui";
			$groupe['comite'] = "oui";
			$groupe['forum'] = "non";
			sql_insertq("spip_groupes_mots",$groupe);
			$ret = sql_fetsel(
				array('MAX(id_groupe) as id_groupe'),
				array('spip_groupes_mots')
			);
			$id_groupe = $ret['id_groupe'];
		} else $id_groupe = $id_groupe['id_groupe'];

		if ($cell[38]['type'] != "null"){ // classification Tilelive secondaire
			// création des mots si necessaires
			$mots = explode(" / ", $cell[38]['value']);
			foreach ($mots as $mot) {
				$id_mot = sql_fetsel("id_mot, titre","spip_mots",'titre = '.sql_quote($mot));
				if (!intval($id_mot['id_mot'])) {
					$m = array();
					$m['id_groupe'] = $id_groupe;
					$m['titre']  = $mot;
					sql_insertq("spip_mots",$m);
					$ret = sql_fetsel(
					array('MAX(id_mot) as id_mot'),
					array('spip_mots')
					);
					$id_mot = $ret['id_mot'];
				} else $id_mot = $id_mot['id_mot'];
				sql_insertq("spip_mots_livres",array('id_livre' => $id_livre, 'id_mot' => $id_mot));
			}
		}
	}

	$livre['statut'] = "a_vendre";
	$livre['etat_jaquette'] = "";
	$livre['reliure'] = "";
	$livre['annee_edition'] = '';
	$livre['num_edition'] = '';
	$livre['inscription'] = '';
	$livre['remarque'] = '';
	$livre['prix_achat'] = '';
	$livre['lieu'] = '';
	$livre['num_facture'] = '';

	$livre['id_auteur'] = $GLOBALS['auteur_session']['id_auteur'];
	$livre['id_catalogue'] = $id_catalogue;
	$livre['type_import'] = "priceminister";

	$update = array();
	foreach ($livre as $key => $val)
		$update[] = $key . '=' . sql_quote($val);

	if (!count($update)) return;

	sql_query("UPDATE spip_livres SET ".join(', ', $update)." WHERE id_livre=$id_livre");
	sql_insertq("spip_livres_catalogues",array('id_livre' => $id_livre, 'id_catalogue' => $id_catalogue));
}



function traiter_fichier_priceminister($fichier, $id_catalogue, $doublons,$import_image,$motscles) {

	include_spip('inc/ods_xml');

	if ($ods = ods_xml_load($fichier)) {

		$rows = array();

		foreach ($ods['sheets'] as $sheets) {

			$rows = $sheets['rows'];
			array_shift($rows); // suppression premiere ligne (les titres de colonnes)

			foreach ($rows as $row) {
				if (is_array($row['cells'])) {
					ajouter_livre_priceminister($row['cells'],$id_catalogue,$doublons,$import_image,$motscles);
				}
			}
		}
	} else return false;

	return true;
}

function traiter_fichier_bouquinerie($fichier, $id_catalogue, $doublons) {
	include_spip('inc/ods_xml');

	if ($ods = ods_xml_load($fichier)) {

		$rows = array();

		foreach ($ods['sheets'] as $sheets) {

			$rows = $sheets['rows'];
			array_shift($rows); // suppression premiere ligne (les titres de colonnes)

			foreach ($rows as $row) {
				if (is_array($row['cells'])) {
					ajouter_livre_bouquinerie($row['cells'],$id_catalogue,$doublons);
				}
			}
		}
	} else return false;

	return true;
}
?>
