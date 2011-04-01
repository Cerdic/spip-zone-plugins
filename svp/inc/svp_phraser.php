<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/xml');


// Phraser un fichier de source dont l'url est donnee
// Le fichier est un fichier XML contenant deux balises principales :
// - <depot>...</depot> : informations de description du depot (facultatif)
// - <archives>...</archives> : liste des informations sur chaque archive (obligatoire)
function svp_phraser_depot($url){

	// On lit le fichier xml. A priori il ne peut y avoir d'erreur, l'url a ete verifiee par l'appelant...
	include_spip('inc/distant');
	if (!$xml = recuperer_page($url))
		return false;

	// Initialisation du tableau des informations
	// -- Si aucun bloc depot n'est trouve le titre et le type prennent une valeur par defaut
	$infos = array(
				'depot' => array(
							'titre' => _T('svp:titre_nouveau_depot'), 
							'type' => 'manuel'),
				'paquets' => array());

	// Extraction et phrasage du bloc depot si il existe
	// -- Si le bloc <depot> n'est pas renseigne on ne considere pas cela comme une erreur
	$balises_depot = array('titre', 'descriptif', 'type', 'url_serveur', 'url_archives');
	if (preg_match(_SVP_REGEXP_BALISE_DEPOT, $xml, $matches)) {
		if (is_array($arbre_depot = spip_xml_parse($matches[1]))) {
			$infos['depot'] = svp_aplatir_balises($balises_depot, $arbre_depot, 'nonvide', $infos['depot']);
		}
	}

	// Extraction et phrasage du bloc des archives si il existe
	// -- Si le bloc <archives> n'existe pas c'est une erreur
	if (!preg_match(_SVP_REGEXP_BALISE_ARCHIVES, $xml, $matches))
		return false;
	// -- Si aucun bloc <archive> c'est aussi une erreur
	if (!preg_match_all(_SVP_REGEXP_BALISE_ARCHIVE, $xml, $matches))
		return false;
	$infos['paquets'] = svp_phraser_archives($matches[0]);
	// -- Si aucun paquet extrait c'est aussi une erreur
	if (!$infos['paquets'])
		return false;

	return $infos;
}


// Phraser la liste des balises <archive>
	// Chaque bloc XML est constitue de 3 sous-blocs principaux :
	// - <zip> : contient les balises d'information sur le zip (obligatoire)
	// - <traductions> : contient la compilation des informations de traduction (facultatif)
	// - <plugin> ou <paquet> suivant la DTD : le contenu du fichier plugin.xml ou paquet.xml (facultatif)
function svp_phraser_archives($archives){
	$paquets = array();

	// On verifie qu'il existe au moins une archive
	if (!$archives)
		return $paquets;

	// On phrase chacune des archives
	// Seul le bloc <zip> est obligatoire
	foreach ($archives as $_cle => $_archive){
		if (preg_match(_SVP_REGEXP_BALISE_ZIP, $_archive, $matches)) {
			// Extraction de la balise <zip>
			$zip = svp_phraser_zip($matches[1]);
			if ($zip) {
				// Affectation des informations du zip
				$paquets[$zip[file]] = $zip;
			
				// Extraction de la balise traductions
				$paquets[$zip[file]]['traductions'] = array();
				if (preg_match(_SVP_REGEXP_BALISE_TRADUCTIONS, $_archive, $matches))
					$paquets[$zip[file]]['traductions'] = svp_phraser_traductions($matches[1]);
				
				// La balise <archive> peut posseder un attribut qui precise la DTD utilisee pour les plugins (plugin ou paquet)
				// Sinon, c'est la DTD plugin qui est utilisee
				list($tag, $attributs) = spip_xml_decompose_tag($_archive);
				// -- On stocke la DTD d'extraction des infos du plugin
				$paquets[$zip[file]]['dtd'] = (isset($attributs['dtd'])) ? $attributs['dtd'] : _SVP_DTD_PLUGIN;

				// Extraction de la balise plugin ou paquet suivant la DTD et la version SPIP
				// -- DTD : si on utilise plugin.xml on extrait la balise <plugin> sinon la balise <paquet>
				// -- Pour SPIP < 2.2, seule la DTD de plugin.xml est utilisee. 
				// -- De plus, la fonction infos_plugins() n'existant pas dans SPIP 2.1, son backport 
				// est inclus dans SVP
				$paquets[$zip[file]]['plugin'] = array();
				$regexp = ($paquets[$zip[file]]['dtd'] == 'plugin') ? _SVP_REGEXP_BALISE_PLUGIN : _SVP_REGEXP_BALISE_PAQUET;
				if (preg_match($regexp, $_archive, $matches)) {
					// Extraction des informations du plugin suivant le standard SPIP
					$informer = charger_fonction('infos_' . $paquets[$zip[file]]['dtd'], 'plugins');
					$paquets[$zip[file]]['plugin'] = $informer($matches[0]);
				}
			}
		}
	}
	return $paquets;
}


// Phrase le contenu dans la balise <zip>
// -- nom du zip, taille, date, dernier commit, arborescence relative des sources...
function svp_phraser_zip($contenu) {
	static $balises_zip = array('file', 'size', 'date', 'source', 'last_commit');
	
	$zip = array();
	if (is_array($arbre = spip_xml_parse($contenu)))
		$zip = svp_aplatir_balises($balises_zip, $arbre);

	return $zip;
}


// Phrase le contenu d'une balise <traductions> en un tableau plus facilement utilisable
// -- Par module, la langue de reference, le gestionnaire, les langues traduites et leurs traducteurs
function svp_phraser_traductions($contenu){
	
	$traductions = array();
	if (is_array($arbre = spip_xml_parse($contenu))) {
		foreach ($arbre as $_tag => $_langues) {
			// On commence par les balises <traduction> et leurs attributs	
			list($tag, $attributs_traduction) = spip_xml_decompose_tag($_tag);
			$traductions[$attributs_traduction['module']]['reference'] = $attributs_traduction['reference'];
			$traductions[$attributs_traduction['module']]['gestionnaire'] = isset($attributs_traduction['gestionnaire']) ? $attributs_traduction['gestionnaire'] : '' ;
	
			// On continue par les balises <langue> qui donnent le code en attribut
			// et les balises <traducteur> qui donnent uniquement le nom en attribut
			if (is_array($_langues[0])) {
				foreach ($_langues[0] as $_tag => $_traducteurs) {
					list($tag, $attributs_langue) = spip_xml_decompose_tag($_tag);
					$traducteurs = array();
					if (is_array($_traducteurs[0])) {
						foreach ($_traducteurs[0] as $_tag => $_vide) {
							list($tag, $attributs_traducteur) = spip_xml_decompose_tag($_tag);
							$traducteurs[] = $attributs_traducteur['nom'];
						}
					}
					$traductions[$attributs_traduction['module']]['langues'][$attributs_langue['code']] = $traducteurs;
				}
			}
		}
	}

	return $traductions;
}


// Aplatit plusieurs cles d'un arbre xml dans un tableau
// -- Effectue un trim() au passage
// -- le mode 'nonvide' permet de ne pas modifier une valeur du tableau si sa valeur dans
//    l'arbre est vide et d'y affecter sa valeur par defaut si elle existe, la chaine vide sinon
function svp_aplatir_balises($balises, $arbre_xml, $mode='vide_et_nonvide', $tableau_initial=array()) {
	$tableau_aplati = array();

	if (!$balises)
		return $tableau_initial;

	foreach ($balises as $_cle => $_valeur){
		$tag = (is_string($_cle)) ? $_cle : $_valeur;
		$valeur_aplatie = trim(spip_xml_aplatit($arbre_xml[$tag]));
		if (($mode == 'vide_et_nonvide')
		OR (($mode == 'nonvide') AND $valeur_aplatie))
			$tableau_aplati[$_valeur] = $valeur_aplatie;
		else
			$tableau_aplati[$_valeur] = isset($tableau_initial[$_valeur]) ? $tableau_initial[$_valeur] : '';
	}

	return $tableau_aplati;
}

?>
