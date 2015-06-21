<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_langonet_debusquer_charger() {
	$valeurs = array();

	$fichiers_test = preg_files(_DIR_RACINE.'plugins/langotests/', '(?<!/charsets|/lang|/req)(/[^/]*\.(xml|yaml|html|php))$');
	include_spip('inc/verifier_items');
	$regexps = array(
		'paquet.xml' => '_LANGONET_ITEM_PAQUETXML',
		'plugin.xml' => '_LANGONET_ITEM_PLUGINXML',
		'XML : contenu balise' => '_LANGONET_ITEM_XML_CONTENU',
		'XML : attribut balise' => '_LANGONET_ITEM_XML_ATTRIBUT',
		'YAML' => '_LANGONET_ITEM_YAML',
		'HTML : balise spip' => '_LANGONET_ITEM_HTML_BALISE',
		'HTML : singulier_ou_pluriel argument 1' => '_LANGONET_ITEM_HTML_FILTRE_PLURIEL_1',
		'HTML : singulier_ou_pluriel argument 2' => '_LANGONET_ITEM_HTML_FILTRE_PLURIEL_2',
		'HTML : filtre _T' => '_LANGONET_ITEM_HTML_FILTRE_T',
		'PHP : declarations objet spip' => '_LANGONET_ITEM_PHP_OBJET',
		'PHP : filtres _T ou _U avec simple quote' => '_LANGONET_ITEM_PHP_TRADA',
		'PHP : filtres _T ou _U avec double quote' => '_LANGONET_ITEM_PHP_TRADG'
	);

	$valeurs = array(
		'_fichiers_test' => $fichiers_test,
		'_regexps' => $regexps,
		'fichier_test' => _request('fichier_test'),
		'regexp' => _request('regexp')
	);

	return $valeurs;
}

function formulaires_langonet_debusquer_verifier() {
	$erreurs = array();
	return $erreurs;
}

function formulaires_langonet_debusquer_traiter() {

	include_spip('inc/verifier_items');
	$fichier = _request('fichier_test');
	$regexp = constant(_request('regexp'));

	$utilises = array(
					'raccourcis' => array(),
					'modules' => array(),
					'items' => array(),
					'occurrences' => array(),
					'suffixes' => array(),
					'variables' => array(),
					'debug' => array()
	);

	if ($contenu = file_get_contents($fichier)) {
		// On stocke aussi le fichier à scanner sous forme d'un tableau de lignes afin de rechercher
		// les numéros de ligne et de colonne des occurrences
		$lignes = file($fichier);
		if (preg_match_all($regexp, $contenu, $matches, PREG_OFFSET_CAPTURE)) {
			foreach ($matches[0] as $_cle => $_expression) {
				$occurrence[0] = $_expression[0];
				$occurrence[1] = $matches[1][$_cle][0];
				$occurrence[2] = $matches[2][$_cle][0];
				$occurrence[3] = isset($matches[3]) ? $matches[3][$_cle][0] : '';
				// Recherche de la ligne et de la colonne à partir de l'offset global de début
				// de l'expression
				list($ligne, $no_ligne, $no_colonne) = rechercher_ligne($_expression[1], $lignes);
				$occurrence[4] = $no_colonne;
				$utilises = memoriser_occurrence($utilises, $occurrence, $fichier, $no_ligne, $ligne, $regexp);
			}
		}
	}

	$retour['message_ok']['resume'] = _T('langonet:message_ok_debug');
	$retour['message_ok']['resultats'] = var_export($utilises['debug']);
	$retour['editable'] = true;
	return $retour;
}

?>