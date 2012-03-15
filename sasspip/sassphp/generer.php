<?php

require 'SassParser.php';

// créé un tableau $styles à partir de la recherche
// de tous les fichiers *.sass & *.scss dans le "path" spip
foreach (find_all_in_path("", ".[.]s[ac]ss") as $styles) 
  {
    // chaque fichier sass produit un fichier css de la forme :
    // "cheminVersFichierSass/nomFichier.css"
	$styles = str_replace(".scss", "", $styles);
	$styles = str_replace(".sass", "", $styles);
    $styles_css = dirname($styles).'/'.basename($styles).'.css';

    // le compilateur sass compile chaque fichiers less et produit
    // la feuille de style correspondante dans le même répertoire.
	$options = array(
		'style' => 'nested',
		'cache' => FALSE,
		'syntax' => $syntax,
		'debug' => FALSE,
		'callbacks' => array(
		'warn' => 'cb_warn',
		'debug' => 'cb_debug',
		),
	);
  
	$parser = new SassParser($options);
	$contenu = $parser->toCss($styles);
	ecrire_fichier($styles_css, $contenu);
    
  }

?>