<?php
/**
* Plugin Notation v.0.3
* par JEM (jean-marc.viglino@ign.fr)
*
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*
* Recuperation des parametres, la fonction de ponderation
*
**/

// Recuperation des parametres
function notation_get_ponderation()
{ // Recherche des valeurs
	$ponderation = $GLOBALS['meta']['notation_ponderation'];
  if ($ponderation == '') $ponderation = 30;
	$ponderation = intval($ponderation);
  if ($ponderation < 1) $ponderation = 1;
  return $ponderation;
}

function notation_get_acces()
{ // Acces
	$acces = $GLOBALS['meta']['notation_acces'];
  if ($acces == '') $acces = 'all';
  return $acces;
}

function notation_get_ip()
{ // Acces  IP ?
	$ip = $GLOBALS['meta']['notation_ip'];
  if ($ip == '') $ip = 'id';
  return $ip;
}

function notation_get_nb_notes()
{ // Acces
	$nb = intval($GLOBALS['meta']['notation_nb']);
  if ($nb < 1) $nb = 5;
  if ($nb > 10) $nb = 10;
  return $nb;
}

// Calcul de la note ponderee
function notation_ponderee ($note, $nb)
{  // Calcule de la note ponderee
   $note_ponderee = round($note*(1-exp(-5*$nb/notation_get_ponderation()))*100)/100;
   return $note_ponderee;
}

?>