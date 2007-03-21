<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function calcul_FORMULAIRE_RECHERCHE_GOOGLE() {
   return "<div id='searchcontrol' />";
}

function balise_FORMULAIRE_RECHERCHE_GOOGLE($p) {
   $p->code = "calcul_FORMULAIRE_RECHERCHE_GOOGLE()";
   $p->statut = 'html';
   return $p;
}
?>
