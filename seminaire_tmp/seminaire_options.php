<?php
/*
 * Quelques notations que vous pouvez utiliser dans
 * votre fichier mes_options.php
 * 
 * Enlever le # pour decommenter la ligne
 */

// ID des webmasters, habilités à executer des taches administratives
// sans vérification de l'identité par FTP
// Ici, auteurs 1 et 2
//define('_ID_WEBMESTRES', '1:2');

// Supprimer les numeros sur les titres
# $GLOBALS['table_des_traitements']['TITRE'][]= 'typo(supprimer_numero(%s))';

// si on utilise ce plugin, configuration du dossier de squelettes correspondant
 // $chem_squel = 'squelettes';
  //0if ($Tsquel = explode(":",$GLOBALS['dossier_squelettes'])) $chem_squel = $Tsquel[0];
//  $GLOBALS['dossier_squelettes'] .= ':'.$chem_squel.'/median';

  
?>
