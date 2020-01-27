<?php
/**
 * Fonctions utiles au plugin Askwiki
 *
 * @plugin     Askwiki
 * @copyright  2020
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * @package    SPIP\Askwiki\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc','askwiki');

/**
 * distinguer 2 types d'url pour wikipedia
 * l'une qui cherche, l'autre enregistrée
 * à partir de la config 
 * config defaut https://fr.wikipedia.org/
 * URL API = https://fr.wikipedia.org/w/api.php
 * URL Consultation = https://fr.wikipedia.org/wiki/
 *
**/
function which_wikipedia($type = ''){
		include_spip('inc/config');
		$url_wikipedia = lire_config('askwiki/url_wikipedia', 'https://fr.wikipedia.org/');
		$url_parse_wikipedia = parse_url($url_wikipedia);
		$url_wikipedia_clean = 'https://'.$url_parse_wikipedia["host"].'/';
		if($type == 'api'){
			$ajout = 'w/api.php';
		} else {
			$ajout = 'wiki/';
		}
	return 	$url_wikipedia_clean.''.$ajout;
}

/*
 * extraire des dates de naissance ou de décès depuis une page wikipedia existante
 * param string $titre_page
 * param numeric bool $life
 * exemple [Date de naissance: (#GET{titre_page}|askwiki_datelife{0})]
 * exemple [Date de décès: (#GET{titre_page}|askwiki_datelife{1})]
 */
function askwiki_datelife($titre_page,$life){
	$datas = askwiki($titre_page);
	$first_paragraph = first_paragraph($datas);
	if($first_paragraph){
		$array_dates = extraire_balises($first_paragraph,'time');
		if((count($array_dates) > 1 AND $life == 1) 
			OR (count($array_dates) > 0 AND $life == 0) ){
			$datetime = extraire_attribut($array_dates[$life],'datetime');
			$date = new DateTime($datetime);
			return $date->format('Y-m-d H:i:s');
		}
	} return;
}

function quel_age($date_deces,$date_naissance){
	
		$date_deces = new DateTime($date_deces);
		$date_naissance = new DateTime($date_naissance);
		
	if($date_deces > $date_naissance){
		return $date_naissance->diff($date_deces)->y;
	}
}

function askwiki_first_paragraph($titre_page){
	$datas = askwiki($titre_page);
	$first_paragraph = first_paragraph($datas);
	return $first_paragraph;
}

function first_paragraph($text){
	$p = extraire_balise($text,'p');
	if(is_string($p) AND strlen($p) > 0){
		return $p;
	} 
	return false;
}

/*
 * extrait des données d'une page wikipedia depuis un titre préformaté
 * @use 
 * param string $titre_page
 * voir https://www.mediawiki.org/wiki/API:Get_the_contents_of_a_page
 *
 */
function askwiki($titre_page){
	
	//$titre_page = rawurlencode(utf8_encode($titre_page));
	
	$endPoint = which_wikipedia('api');

	$params = [
		"action" => "query",
		"format" => "json",
		"prop" => "extracts",
		"titles" => $titre_page,
		"exlimit" => "1",
		"formatversion" => "2",
		"exsentences" => "1" //nombre de phrases à extraire
	];
	
	$url = $endPoint . "?" . http_build_query( $params );
	spip_log("acces demande pour $url",'test_wiki');
		
	$ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$output = curl_exec( $ch );
	curl_close( $ch );
	
	$result = json_decode( $output, true );
	
	$retour = '';
	 foreach( $result["query"]["pages"] as $k => $v ) {
				@$retour .= $v["extract"];
	 }
		
	return $retour;

}

//function generique pour trouver la bonne page wikipedia sur presque n'importe quel objet
function titre_page_wiki($id_objet,$objet){
	$titre_page = FALSE;
	$id_table_objet = id_table_objet($objet); //date_naissance
	$table = table_objet_sql($objet); //spip_contacts
	$prenom = sql_getfetsel('prenom', $table, "$id_table_objet = ".sql_quote($id_objet));
	$nom = sql_getfetsel('nom', $table, "$id_table_objet = ".sql_quote($id_objet));
	if($prenom AND $nom){
		$titre_page = trim($prenom).'_'.trim($nom); //essayer aussi sans accents ?
	} else if(!$prenom AND $nom){
			$titre_page = trim($nom);
	} else {
		$titre = sql_getfetsel('titre', $table, "$id_table_objet = ".sql_quote($id_objet));
		$titre_page = $titre;
	}
	return $titre_page;
}