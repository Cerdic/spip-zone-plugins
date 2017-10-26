<?php
/**
 * Fonctions utiles au plugin Videos
 *
 * @plugin     Videos
 * @copyright  2014
 * @author     Charles Stephan
 * @licence    GNU/GPL
 * @package    SPIP\Video\Fonctions
 *
 *
 */

	if (!defined('_ECRIRE_INC_VERSION')) return;

	include_spip('inc/utils');
	include_spip('inc/distant');
	include_spip('inc/filtres');


	function vimeo_creer_auteur($infos) {

		$infos = $infos["body"];

		$compte = lire_config('vimeo');

		$id = filter_var($infos["uri"], FILTER_SANITIZE_NUMBER_INT);

		$id_auteur		= $compte['id_auteur'] ? $compte['id_auteur'] : $id;
		$bio 			= $infos["bio"];
		$bio 			= "<multi>".$bio."</multi>";
		$nom 		 	= $infos["name"];
		$logo_auteur 	= $infos["pictures"]["sizes"][3]["link"];

		$nom_site 	 	= $infos["websites"][0]["name"];
		$url_site 	 	= $infos["websites"][0]["link"];

		$statut = $compte["profil_auteur_statut"] ? $compte["profil_auteur_statut"] : "6forum";

		$k = recuperer_infos_distantes($logo_auteur);

		$portrait = copie_locale($logo_auteur,'auto','IMG/auton'.$id_auteur.'.'.$k['extension']);

		$spip_auteurs = array(
			"id_auteur" => $id_auteur,
			"nom" => $nom,
			"bio" => $bio,
			"nom_site" => $nom_site,
			"url_site" => $url_site,
			"statut" => $statut
		);

		$where = "id_auteur = ". intval($id_auteur);
		$result = sql_countsel("spip_auteurs",$where);

		if ($result) {

			unset($spip_auteurs['id_auteur']);
			$spip_auteurs = sql_updateq('spip_auteurs',$spip_auteurs,$where);

		} else {

			$spip_auteurs = sql_insertq('spip_auteurs',$spip_auteurs);

		}

		return $id_auteur;

	}

	function vimeo_creer_archives($archives, $archives_lier_vimeo) {

		$compte = lire_config('vimeo');

		$titre = sql_getfetsel('titre', 'spip_groupes_mots', 'id_groupe=' . intval($compte["id_groupe_archives"]));

		$mots = array();

		foreach ($archives as $key => $id_mot) {

			$where = "id_mot = ". intval($id_mot);
			$result = sql_countsel("spip_mots",$where);

			$mot = array(
				"id_mot" => $id_mot,
				"titre" => $id_mot,
				"id_groupe"=> $compte["id_groupe_archives"],
				"type" => $titre
			);

			array_push($mots, $mot);

		}

		$spip_mot = sql_replace_multi('spip_mots',$mots);

		$archives_lier_vimeo = sql_replace_multi('spip_mots_liens',$archives_lier_vimeo);

	}

	function vimeo_creer_video($videos) {

		$spip_vimeos = array();
		$compte = lire_config('vimeo');

		$archives_lier_vimeo = array();
		$archives = array();

		foreach ($videos as $key => $value) {

			$id_vimeo = explode("/", $value["uri"]);
			$id_vimeo = $id_vimeo[2];

			$titre 		= $value["name"];

			$url_vimeo  = "https://www.vimeo.com/".$id_vimeo;

			$description = $value['description'];

			$description = str_replace('[e]','##e##',$description);
			$description = str_replace('"','”',$description);

			if ($compte["archives"] === "on") {

				$trads 		= extraire_trads($description);
				$texte 		= $trads['fr'];

				$texte 		= explode("***", $texte);
				$millesime  = filter_var($texte[2], FILTER_SANITIZE_NUMBER_INT);

				array_push($archives, $millesime);
				array_push($archives_lier_vimeo, array(
					'id_mot' 	=> $millesime,
					'id_objet'	=> $id_vimeo,
					'objet'		=> 'vimeo'
				));

			}

			$date 		= date('Y-m-d', strtotime($value["created_time"]));
			$logo 		= $value["pictures"]["sizes"][4]["link"];

			$description      = str_replace("Année", "{{Année}}", $description);
			$description      = str_replace("Production", "{{Production}}", $description);
			$description      = str_replace("Year", "{{Year}}", $description);
			$description      = str_replace("***", "", $description);

			$description = str_replace('##e##','[e]',$description);

			$k = recuperer_infos_distantes($logo);
    		$fichier = copie_locale($logo,'modif','IMG/vimeoon'.$id_vimeo.'.'.$k['extension']);

			array_push($spip_vimeos,
				array(
					"id_vimeo" 	=> $id_vimeo,
					"url_video"	=> $url_vimeo,
					"titre"		=> $titre,
					"texte"		=> "<multi>".$description."</multi>",
					"credits"	=> "",
					"date"		=> $date,
					"statut" 	=> $compte['vimeo_statut']
				)
			);
		}

		$spip_vimeos = sql_replace_multi('spip_vimeos',$spip_vimeos);

		if ($compte["archives"] === "on") {
			vimeo_creer_archives($archives, $archives_lier_vimeo);
		}

	}

	function vimeo_creer_albums($albums,$album_lier_vimeo) {

		$compte = lire_config('vimeo');

		$titre = sql_getfetsel('titre', 'spip_groupes_mots', 'id_groupe=' . intval($compte["id_groupe_albums"]));

		$mots = array();

		foreach ($albums as $key => $value) {

			$nom = $value[0];
			$id_mot = explode("/", $value[1]);
			$id_mot = $id_mot[4];

			$where = "id_mot = ". intval($id_mot);
			$result = sql_countsel("spip_mots",$where);

			$mot = array(
				"id_mot" => $id_mot,
				"titre" => $nom,
				"id_groupe"=> $compte["id_groupe_albums"],
				"type" => $titre
			);

			array_push($mots, $mot);

		}

		$spip_mot = sql_replace_multi('spip_mots',$mots);

		$album_lier_vimeo = sql_replace_multi('spip_mots_liens',$album_lier_vimeo);

	}

	function action_vimeo_dist($arg=null) {

		/*

			TO DO SECURISER CETTE ACTION

		if (is_null($arg)){
			$securiser_action = charger_fonction("securiser_action","inc");
			$arg = $securiser_action();
		}
		*/

		$videos = array();
		$auteurs_lier_vimeo = array();

		$album_lier_vimeo = array();

		include_spip('lib/vimeo/autoload');

		$erreur = array();

		$compte = lire_config('vimeo');

		$lib = new \Vimeo\Vimeo($compte['client_id'], $compte['client_secret']);
 		$user_id = $compte['user_vimeo'];

    $user = $lib->request('/users/'.$user_id);
    $id_auteur = "";

    if ($compte['profil'] !== 'rien'){
			$id_auteur = vimeo_creer_auteur($user);
		}

		$albums = $user["body"]["metadata"]["connections"]["albums"]["uri"];
		$albums = $lib->request($albums)["body"]["data"];

		$albums_infos = array();

		foreach ($albums as $key => $value) {
			array_push($albums_infos, array($value["name"],$value["uri"]));
		}

		foreach ($albums_infos as $key => $value) {

			$vid = $lib->request($value[1]."/videos")["body"]["data"];

			$id_mot = explode("/", $value[1]);
			$id_mot = $id_mot[4];

			foreach ($vid as $key => $value) {

				array_push($videos, $value);

				$id_vimeo = explode("/", $value["uri"]);
				$id_vimeo = $id_vimeo[2];

				array_push($album_lier_vimeo, array(
					'id_mot' 	=> $id_mot,
					'id_objet'	=> $id_vimeo,
					'objet'		=> 'vimeo'
				));

				array_push($auteurs_lier_vimeo, array(
					'id_auteur' => $id_auteur,
					'id_objet'	=> $id_vimeo,
					'objet'		=> 'vimeo'
				));

			}

		}

		if ($compte["liaison_auteur"]) {
			$auteurs_lier_vimeo = sql_replace_multi('spip_auteurs_liens',$auteurs_lier_vimeo);
		}

		if ($compte["albums"] === "on") {
			vimeo_creer_albums($albums_infos,$album_lier_vimeo);
		}

		vimeo_creer_video($videos);

	}

?>
