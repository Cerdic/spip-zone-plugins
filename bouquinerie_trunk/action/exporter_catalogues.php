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


function action_exporter_catalogues_dist() {



	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	if (!$id_auteur) redirige_par_entete('./');

	$rapport = '';
	$xml = array('sheets' => array(), 'head' => '');
	$xml['sheets'][0] = array('rows' => array(), 'name' => 'Feuille 1', 'style-name' => '', 'print' => 'false');

	$tout = _request('tout');
	if ($tout == "oui") {
		$livres = sql_select("*","spip_livres");
	}
	else {
		$catalogues = array();
		$q = sql_select("titre,id_catalogue","spip_catalogues");

		while ($r = sql_fetch($q)) {
			$catalogues[$r['id_catalogue']] = _request($r['id_catalogue']);
		}

		$in = '';
		foreach ($catalogues as $id => $catalogue) {
			if ($catalogue == "oui") {
				$in .= ",'$id'";
			}
		}
		$in = substr($in,1);
		$livres = sql_select("*","spip_livres","id_catalogue IN ($in)");
	}


	creer_structure_bouquinerie(&$xml,$livres);

	include_spip('inc/ods_xml');
	$contenu = ods_array_xml($xml);

	$uid = uniqid();
	$path = _DIR_TMP . 'bouquinerie_export_'.$uid;
	$fichier = 'bouquinerie_'.date('YmdHis').'.ods';

	mkdir($path);

	ecrire_fichier($path.'/content.xml',$contenu);
	ecrire_fichier($path.'/mimetype','application/vnd.oasis.opendocument.spreadsheet');
	ecrire_fichier($path.'/meta.xml',ods_get_meta('fr-FR')); // format de la langue : min-MAJ es-ES en-EN, etc ...
	ecrire_fichier($path.'/styles.xml',ods_get_style());
	ecrire_fichier($path.'/settings.xml',ods_get_settings());
	mkdir($path.'/META-INF/');
	mkdir($path.'/Configurations2/');
	mkdir($path.'/Configurations2/acceleator/');
	mkdir($path.'/Configurations2/images/');
	mkdir($path.'/Configurations2/popupmenu/');
	mkdir($path.'/Configurations2/statusbar/');
	mkdir($path.'/Configurations2/floater/');
	mkdir($path.'/Configurations2/menubar/');
	mkdir($path.'/Configurations2/progressbar/');
	mkdir($path.'/Configurations2/toolbar/');
	ecrire_fichier($path.'/META-INF/manifest.xml',ods_get_manifest());


	shell_exec('cd '.$path.';zip -r ../'.escapeshellarg($fichier).' ./');
	$rapport .= _T('bouq:fichier_creer',array('fichier'=>$fichier));

	shell_exec('rm -R '. $path);
	$redirect = parametre_url(urldecode(generer_url_ecrire('admin_bouquinerie')),
				'rapport', $rapport, '&');
	redirige_par_entete($redirect);
}


function creer_structure_bouquinerie(&$xml,$livres) {

	include_spip('inc/export');

	// TITRES

	$cell = array();
	$cell['value'] = text_to_xml('ID'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][0] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('TITRE'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][1] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('AUTEUR'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][2] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('ILLUSTRATEUR'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][3] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('EDITION'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][4] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('PRIX DE VENTE'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][5] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('ISBN'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][6] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('CATALOGUE'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][7] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('STATUT'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][8] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('ETAT DU LIVRE'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][9] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('ETAT DE LA JAQUETTE'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][10] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('FORMAT'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][11] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('RELIURE'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][12] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('TYPE DE LIVRE'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][13] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('LIEU D\'EDITION'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][14] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('ANNEE D\'EDITION'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][15] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('NUMERO D\'EDITION'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][16] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('INSCRIPTION'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][17] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('REMARQUE'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][18] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('COMMENTAIRE'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][19] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('PRIX D\'ACHAT'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][20] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('LIEU'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][21] = $cell;

	$cell = array();
	$cell['value'] = text_to_xml('NUMERO DE FACTURE'); 
	$cell['type'] = 'string';
	$xml['sheets'][0]['rows'][0]['cells'][22] = $cell;


	$i = 1;
	while ($livre = sql_fetch($livres)) {
		$xml['sheets'][0]['rows'][$i] = array ('cells' => array());

		// id_livre
		$cell = array();
		$cell['value'] = $livre['id_livre']; 
		$cell['type'] = $livre['id_livre'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][0] = $cell;

		// titre
		$cell = array();
		$cell['value'] = text_to_xml($livre['titre']); 
		$cell['type'] = $livre['titre'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][1] = $cell;

		// auteur
		$cell = array();
		$cell['value'] = text_to_xml($livre['auteur']); 
		$cell['type'] = $livre['auteur'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][2] = $cell;

		// illustrateur
		$cell = array();
		$cell['value'] = text_to_xml($livre['illustrateur']); 
		$cell['type'] = $livre['illustrateur'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][3] = $cell;

		// edition
		$cell = array();
		$cell['value'] = text_to_xml($livre['edition']); 
		$cell['type'] = $livre['edition'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][4] = $cell;

		// prix_vente
		$cell = array();
		$cell['value'] = text_to_xml($livre['prix_vente']); 
		$cell['type'] = $livre['prix_vente'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][5] = $cell;

		// isbn
		$cell = array();
		$cell['value'] = text_to_xml($livre['isbn']); 
		$cell['type'] = $livre['isbn'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][6] = $cell;

		// catalogue 
		$r = sql_fetsel("titre","spip_catalogues",'id_catalogue='.$livre['id_catalogue']);
		$cell = array();
		$cell['value'] = text_to_xml($r['titre']); 
		$cell['type'] = $r['titre'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][7] = $cell;

		// statut
		$cell = array();
		$cell['value'] = text_to_xml($livre['statut']); 
		$cell['type'] = $livre['statut'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][8] = $cell;

		// etat_livre
		$cell = array();
		$cell['value'] = text_to_xml($livre['etat_livre']); 
		$cell['type'] = $livre['etat_livre'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][9] = $cell;

		// etat_jaquette
		$cell = array();
		$cell['value'] = text_to_xml($livre['etat_jaquette']); 
		$cell['type'] = $livre['etat_jaquette'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][10] = $cell;

		// format
		$cell = array();
		$cell['value'] = text_to_xml($livre['format']); 
		$cell['type'] = $livre['format'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][11] = $cell;

		// reliure
		$cell = array();
		$cell['value'] = text_to_xml($livre['reliure']); 
		$cell['type'] = $livre['reliure'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][12] = $cell;

		// type_livre
		$cell = array();
		$cell['value'] = text_to_xml($livre['type_livre']); 
		$cell['type'] = $livre['type_livre'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][13] = $cell;

		// lieu_edition
		$cell = array();
		$cell['value'] = text_to_xml($livre['lieu_edition']); 
		$cell['type'] = $livre['lieu_edition'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][14] = $cell;

		// annee_edition
		$cell = array();
		$cell['value'] = text_to_xml($livre['annee_edition']); 
		$cell['type'] = $livre['annee_edition'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][15] = $cell;

		// num_edition
		$cell = array();
		$cell['value'] = text_to_xml($livre['num_edition']); 
		$cell['type'] = $livre['num_edition'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][16] = $cell;

		// inscription
		$cell = array();
		$cell['value'] = text_to_xml($livre['inscription']); 
		$cell['type'] = $livre['inscription'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][17] = $cell;

		// remarque
		$cell = array();
		$cell['value'] = text_to_xml($livre['remarque']); 
		$cell['type'] = $livre['remarque'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][18] = $cell;

		// commentaire
		$cell = array();
		$cell['value'] = text_to_xml($livre['commentaire']); 
		$cell['type'] = $livre['commentaire'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][19] = $cell;

		// prix_achat
		$cell = array();
		$cell['value'] = text_to_xml($livre['prix_achat']); 
		$cell['type'] = $livre['prix_achat'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][20] = $cell;

		// lieu
		$cell = array();
		$cell['value'] = text_to_xml($livre['lieu']); 
		$cell['type'] = $livre['lieu'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][21] = $cell;

		// num_facture
		$cell = array();
		$cell['value'] = text_to_xml($livre['num_facture']); 
		$cell['type'] = $livre['num_facture'] ? 'string' : 'null';
		$xml['sheets'][0]['rows'][$i]['cells'][22] = $cell;

		$i++;
	}
}

?>
