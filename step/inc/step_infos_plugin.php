<?php

// Fonctions analysant les fichiers plugin.xml et archives.xml en decoulant
// Il faudrait aussi admettre les fichiers paquet.xml de la 2.2

include_spip('inc/xml');

function step_get_infos_plugin($constante, $p, $actifs, $recents) {
	$dir = constant($constante);
	lire_fichier($dir . $p . '/plugin.xml', $xml);
	// enlever la balise doctype qui provoque une erreur "balise non fermee"
	$xml = preg_replace('#<!DOCTYPE[^>]*>#','',$xml);
	// traduire le xml en php
	spip_log("spip_get_infos $dir $p");
	if (!is_array($plugin = spip_xml_parse($xml))) return;
		
	// [extrait] de plugins/verifie_conformite.php
	// chercher la declaration <plugin spip='...'> a prendre pour cette version de SPIP
	if (!spip_xml_match_nodes(",^plugin(\s|$),", $plugin, $matches))
		return '';

	// version de SPIP
	$vspip = $GLOBALS['spip_version_branche'];
	foreach($matches as $tag => $sous){
		list($tagname, $atts) = spip_xml_decompose_tag($tag);
		if ($tagname == 'plugin' AND is_array($sous)){
			if (!isset($atts['spip'])
			OR plugin_version_compatible($atts['spip'], $vspip))
				// on prend la derniere declaration avec ce nom
				$plugin = end($sous);
		}
	}

	// [/extrait] de plugins/verifie_conformite.php

	// applatir les champs du plugin (ie les balises <multi> sont des chaines...) 
	return step_xml_parse_plugin($plugin);
}


// parse un fichier de source dont l'url est donnee
// ce fichier est un fichier XML contenant <zone><zone_elt/></zone>
function step_xml_parse_zone($xml){

	
	// enlever la balise doctype qui provoque une erreur "balise non fermee"
	$xml = preg_replace('#<!DOCTYPE[^>]*>#','',$xml);
	if (!is_array($arbre = spip_xml_parse($xml)) OR !is_array($arbre = $arbre['archives'][0])){
		return false;
	}

	// boucle sur les elements pour creer un tableau array (url_zip => datas)
	$paquets = array();			
	foreach ($arbre as $z=>$c){
		$c = $c[0];
		// si plugin et fichier zip, on ajoute le paquet dans la liste
		if ((is_array($c['plugin'])) AND ($url = $c['file'][0])) {
			$paquets[$url] = array(
				'plugin' => step_xml_parse_plugin($c['plugin'][0]), 
				'file' => $url, 
			);
		}
	}
	if (!$paquets) {
		return false;
	}
	
	return $paquets;
}

// aplatit plusieurs cles d'un arbre xml dans un tableau
// effectue un trim() au passage
function step_xml_aplatit_multiple($array, $arbre){
	$a = array();
	// array('uri','archive'=>'zip',...)
	foreach ($array as $i=>$n){
		if (is_string($i)) $cle = $i;
		else $cle = $n;
		$a[$n] = trim(spip_xml_aplatit($arbre[$cle]));
	}
	return $a;	
}


// parse un plugin.xml genere par spip_xml_parse()
// en un tableau plus facilement utilisable
// cette fonction doit permettre de mapper des changements 
// de syntaxe entre plugin.xml et step
function step_xml_parse_plugin($arbre){

	if (!is_array($arbre)) 
		return false;
	
	// on commence par les simples !
	$plug_arbre = step_xml_aplatit_multiple(
				array('nom','icon','auteur','licence','version','version_base','etat','shortdesc','categorie','tags',
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
		if (spip_xml_match_nodes(",^$balise,",$arbre, $res)){
			foreach(array_keys($res) as $tag){
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

	// 2) balises impriquees
	// (pipeline, boutons, onglets)
	// on verra plus tard si besoin !

	return $plug_arbre;
}

?>
