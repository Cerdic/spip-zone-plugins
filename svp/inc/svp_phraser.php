<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/xml');


// Phraser un fichier de source dont l'url est donnee
// ce fichier est un fichier XML contenant <depot>...</depot>
// et <archives>...</archives>
function svp_xml_parse_depot($url){
	include_spip('inc/distant');

	// On lit le fichier xml
	if (!$xml = recuperer_page($url)) {
		return false;
	}

	// -- Les traitements du XML dependent de la DTD utilisee
	return svp_xml_parse_archives($xml);
}


function svp_xml_parse_archives($xml){
	// On enleve la balise doctype qui provoque une erreur "balise non fermee" lors du parsage
	$xml = preg_replace('#<!DOCTYPE[^>]*>#','',$xml);

	// Deux cas d'erreur de fichier non conforme
	// - la syntaxe xml est incorrecte
	// - aucun plugin dans le depot
	// Si le bloc <depot> n'est pas renseigne on ne considere pas cela comme une erreur
	$arbre = array();
	if (!is_array($arbre = spip_xml_parse($xml)) OR !is_array($archives = $arbre['archives'][0])){
		return false;
	}

	// On extrait les informations du depot si elles existent (balise <depot>)
	$infos = array('depot' => array(), 'paquets' => array());
	if (is_array($depot = $arbre['depot'][0]))
		$infos['depot'] = svp_xml_aplatit_multiple(array('titre', 'descriptif', 'type', 'url_serveur', 'url_archives'), $depot);
	if (!$infos['depot']['titre'])
		$infos['depot']['titre'] = _T('svp:titre_nouveau_depot');
	if (!$infos['depot']['type'])
		$infos['depot']['type'] = 'svn';

	// On extrait les informations de chaque plugin du depot (balise <archives>)
	foreach ($archives as $z=>$c){
		$c = $c[0];
		// si fichier zip, on ajoute le paquet dans la liste
		// - cas 1 : c'est un plugin donc on integre les infos du plugin
		// - cas 2 : c'est une archive non plugin, pas d'infos autres que celles de l'archive
		if ($url = $c['file'][0]) {
			// Recuperation des infos du plugin (balise <plugin>)
			$plugin = array();
			if (is_array($c[_SVP_DTD_PLUGIN]))
				$plugin = svp_xml_parse_plugin($c[_SVP_DTD_PLUGIN][0]);

			// Recuperation des infos de traductions (balise <traductions>)
			$traductions = array();
			if (is_array($c['traductions']))
				$traductions = svp_xml_parse_traduction($c['traductions'][0]);

			// On compile les infos du paquet
			$infos['paquets'][$url] = array(
				'plugin' => $plugin, 
				'file' => $url,
				'size' => $c['size'][0],
				'date' => $c['date'][0],	// c'est la date de generation du zip
				'source' => $c['source'][0],
				'last_commit' => $c['last_commit'][0],
				'traductions' => $traductions
			);
		}
	}
	return $infos;
}


// parse un plugin.xml genere par spip_xml_parse()
// en un tableau plus facilement utilisable
// cette fonction doit permettre de mapper des changements 
// de syntaxe entre plugin.xml et step
function svp_xml_parse_plugin($arbre){

	if (!is_array($arbre)) 
		return false;
	
	// on commence par les simples !
	$plug_arbre = svp_xml_aplatit_multiple(
				array('nom','icon','auteur','licence','version','version_base','etat','slogan','categorie','tags',
				'description','lien','options','fonctions','prefix','install'), 
				$arbre);
	$plug_arbre['prefix'] = strtolower($plug_arbre['prefix']);
	
	// on continue avec les plus complexes...	
	// 1) balises avec attributs
	foreach (array(
			'necessite'=>array('necessite', null),
			'utilise'=>array('utilise', null),
			'chemin'=>array('path', array('dir'=>'')))
				as $balise=>$p){
		$params = $res = array();
		// recherche de la balise et extraction des attributs
		if (spip_xml_match_nodes(",^$balise,", $arbre, $res)){
			foreach (array_keys($res) as $tag){
				list($tag,$att) = spip_xml_decompose_tag($tag);
				$params[] = $att;
			}
		} 
		// valeur par defaut
		else {
			if ($p[1]!==null)
				$params[] = $p[1];
		}
		$plug_arbre[$p[0]] = $params;		
	}

	return $plug_arbre;
}


// parse le contenu d'une balise <traductions> genere par spip_xml_parse()
// en un tableau plus facilement utilisable
function svp_xml_parse_traduction($arbre){

	if (!is_array($arbre)) 
		return false;
	
	$traductions = array();
	
	foreach ($arbre as $_tag => $_langues) {
		// On commence par les balises <traduction> et leurs attributs	
		list($tag, $attributs_traduction) = spip_xml_decompose_tag($_tag);
		$traductions[$attributs_traduction['module']]['reference'] = $attributs_traduction['reference'];
		$traductions[$attributs_traduction['module']]['gestionnaire'] = isset($attributs_traduction['gestionnaire']) ? $attributs_traduction['gestionnaire'] : '' ;

		// On continue par les balises <langue> qui donnent le code en attribut
		// et les balises <traducteur> qui donnent uniquement le nom en attribut
		if (trim($_langues[0])) {
			foreach ($_langues[0] as $_tag => $_traducteurs) {
				list($tag, $attributs_langue) = spip_xml_decompose_tag($_tag);
				$traducteurs = array();
				if (trim($_traducteurs[0])) {
					foreach ($_traducteurs[0] as $_tag => $_vide) {
						list($tag, $attributs_traducteur) = spip_xml_decompose_tag($_tag);
						$traducteurs[] = $attributs_traducteur['nom'];
					}
				}
				$traductions[$attributs_traduction['module']]['langues'][$attributs_langue['code']] = $traducteurs;
			}
		}
	}

	return $traductions;
}


// aplatit plusieurs cles d'un arbre xml dans un tableau
// effectue un trim() au passage
function svp_xml_aplatit_multiple($array, $arbre){
	$a = array();
	// array('uri','archive'=>'zip',...)
	foreach ($array as $i=>$n){
		if (is_string($i)) $cle = $i;
		else $cle = $n;
		$a[$n] = trim(spip_xml_aplatit($arbre[$cle]));
	}
	return $a;	
}

?>
