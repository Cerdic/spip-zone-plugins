<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Point d'entrée principal du dépôt OAI
// On donnera donc aux moissonneurs les URL suivantes :
// http://site.tld/oai.api => dépôt par défaut
// http://site.tld/oai.api/patates => dépôt personnalisé
function action_api_oai_dist(){
	if (!$depot = _request('arg')){
		$depot = 'articles';
	}
	
	$verbes = array(
		'GetRecord' => array(
			'required' => array('identifier', 'metadataPrefix'),
			'optional' => array(),
		),
		'Identify' => array(
			'required' => array(),
			'optional' => array(),
		),
		'ListIdentifiers' => array(
			'required' => array('metadataPrefix'),
			'optional' => array('from', 'until', 'set'),
			'resumptionToken' => true,
		),
		'ListMetadataFormats' => array(
			'required' => array(),
			'optional' => array('identifier'),
		),
		'ListRecords' => array(
			'required' => array('metadataPrefix'),
			'optional' => array('from', 'until', 'set'),
			'resumptionToken' => true,
		),
		'ListSets' => array(
			'required' => array(),
			'optional' => array(),
		),
	);
	
	// POST ou GET, on s'en fiche
	$requete = array_merge($_POST, $_GET);
	// Nettoyage de ce qu'on sait superflu
	unset($requete['action']);
	unset($requete['arg']);
	unset($requete['var_zajax']);
#	var_dump($requete);exit;
	
	// Pas d'erreur pour l'instant
	$erreur = array();
	
	// On commence les tests
	
	// Si pas de "verb" du tout
	if (!$verbe = $requete['verb']) {
		$erreur[] = array(
			'error' => 'badVerb',
			'message' => _T('oai:erreur_badVerb_absent'),
		);
	}
	// Si le "verb" ne fait pas parti de la liste autorisée
	elseif (!in_array($verbe, array_keys($verbes))) {
		$erreur[] = array(
			'error' => 'badVerb',
			'message' => _T('oai:erreur_badVerb_inconnu', array('verbe' => $verbe)),
		);
	}
	// C'est un bon verbe, on peut tester les arguments
	else{
		$arguments_ok = $verbes[$verbe];
		$tester_arguments = true;
		
		// Si pour ce verbe, on gère les listes incomplètes, on doit tester cet argument à part, car il doit être unique
		if (isset($arguments_ok['resumptionToken']) and $arguments_ok['resumptionToken']) {
			// Si on a ce paramètre
			if ($token = $requete['resumptionToken']) {
				$tester_arguments = false;
				
				// S'il n'est pas tout seul, c'est une erreur
				if (count($requete) > 1) {
					$erreur[] = array(
						'error' => 'badArgument',
						'message' => _T('oai:erreur_badArgument_resumptionToken_exclusif'),
					);
				}
				// Sinon c'est bon et on le récupère
				else{
					// Le moissonneur a normalement échappé les caractères spéciaux : il faut les décoder
					$token = rawurldecode($token);
				}
			}
		}
		
		// S'il faut bien tester tous les autres arguments autres que resumptionToken
		if ($tester_arguments){
			// Pour chaque argument obligatoire du verbe, on regarde s'il fait partie de la requête
			foreach ($arguments_ok['required'] as $arg_obli=>$val) {
				if (!in_array($arg_obli, $requete)) {
					$erreur[] = array(
						'error' => 'badArgument',
						'message' => _T('oai:erreur_badArgument_obligatoire', array('arg' => $arg_obli, 'verbe' => $verbe)),
					);
				}
			}
			
			// Pour chaque argument de la requête, on regarde s'il est autorisé ou en trop
			foreach ($requete as $arg=>$val) {
				if ($arg != 'verb' and !in_array($arg, array_merge($arguments_ok['required'], $arguments_ok['optional']))) {
					$erreur[] = array(
						'error' => 'badArgument',
						'message' => _T('oai:erreur_badArgument_interdit', array('arg' => $arg, 'verbe' => $verbe)),
					);
				}
			}
		}
	}
	
	// S'il n'y a pas encore d'erreur on va chercher un squelette avec le bon verbe
	if (empty($erreur)) {
		$squelette = '';
		
		/*
		oai/articles/GetRecord.oai_dc.html
		oai/articles/Identify.html
		oai/articles/ListIdentifiers.oai_dc.html
		oai/articles/ListMetadataFormats.html
		oai/articles/ListMetadataFormats.oai_dc.html
		oai/articles/ListRecords.html
		oai/articles/ListSets.html
		*/
		
		// On cherche la liste des formats possibles pour ce dépôt
		$formats = array();
		if ($formats_fichiers = find_all_in_path("oai/$depot/", 'ListMetadataFormats\.[\w_-]+\.html$')) {
			foreach ($formats_fichiers as $fichier=>$chemin){
				$formats[] = preg_replace('/ListMetadataFormats\.([\w_-]+)\.html/', '$1', $fichier);
			}
		}
		
		// À cette étape là, s'il y a un format dans la requête, c'est valide
		// On va chercher un squelette avec ce format
		if ($format = $requete['metadataPrefix']) {
			// Si le format demandé n'existe pas dans la liste
			if (!in_array($requete['metadataPrefix'], $formats)) {
				$erreur[] = array(
					'error' => 'badArgument',
					'message' => _T('oai:erreur_badArgument_format', array('arg' => $arg_obli, 'verbe' => $verbe)),
				);
			}
			// Pas d'erreur de format, on utilise le squelette contenant le format
			else {
				$squelette = "oai/$depot/$verbe.$format";
			}
		}
		// Si c'est ListMetadataFormats on a un conteneur commun
		elseif ($verbe == 'ListMetadataFormats') {
			$squelette = "oai/$verbe";
		}
		// Sinon enfin c'est le verbe dans le dépôt
		else {
			$squelette = "oai/$depot/$verbe";
		}
		
		// Si on a bien trouvé un squelette, on génère le contenu
		if ($squelette){
			$contexte = array_merge($requete, array('depot'=>$depot, 'formats'=>$formats));
			$contenu = recuperer_fond($squelette, $contexte);
			
			// On teste si le squelette a levé une erreur
			if (stripos($contenu, '<error') !== false){
				$erreur = true;
			}
		}
	}
	
	// Maintenant on construit le retour complet
	// D'abord la racine
	$retour = '<?xml version="1.0" encoding="UTF-8"?>';
	$retour .= '<OAI-PMH';
	$retour .= '	xmlns="http://www.openarchives.org/OAI/2.0/"';
	$retour .= '	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
	$retour .= '	xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/	http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd"';
	$retour .= '>';
	
	// La date de la réponse, en UTC
	$retour .= '<responseDate>'.date('c').'</responseDate>';
	
	// La description de la requête
	$retour .= '<request';
	// Si ya aucune erreur, on ajoute tous les paramètres
	if (!$erreur){
		foreach ($requete as $param=>$valeur) {
			// Sauf "depot" car c'est un truc interne
			if ($param != 'depot') {
				$retour .= " $param=\"$valeur\"";
			}
		}
	}
	include_spip('inc/filtres');
	$retour .= '>'.url_absolue('oai.api/'._request('arg')).'</request>';
	
	// Le contenu du squelette
	if (isset($contenu) and is_string($contenu)) {
		$retour.= $contenu;
	}
	
	// Génération des erreurs s'il y en a en tableau
	if ($erreur and is_array($erreur)) {
		foreach ($erreur as $message_erreur) {
			$retour .= '<error code="'.$message_erreur['error'].'">'.$message_erreur['message'].'</error>';
		}
	}
	
	// Et c'est normalement terminé
	$retour .= '</OAI-PMH>';
	
	header('Status: 200 OK');
	header("Content-type: text/xml; charset=utf-8");
	echo $retour;
	exit;
}
