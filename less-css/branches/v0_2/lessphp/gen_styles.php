<?php

require 'lessc.inc.php';

// créé un tableau $styles à partir de la recherche
// de tous les fichiers *.less dans le "path" spip
foreach (find_all_in_path("", ".[.]less$") as $styles) 
  {
    // chaque fichier less produit un fichier css de la forme :
    // "cheminVersFichierLess/less_nomFichierLess.css"
    $styles_css = dirname($styles).'/'.basename($styles).'.css';

    // le compilateur lessc compile chaque fichiers less et produit
    // la feuille de style correspondante dans le même répertoire.
    try {
      lessc::ccompile($styles, $styles_css);
    } catch (exception $ex) {
      exit('lessc fatal error:<br />'.$ex->getMessage());
    }
  }

?>