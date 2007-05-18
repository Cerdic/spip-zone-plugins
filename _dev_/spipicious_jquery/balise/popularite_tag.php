<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

// balise pour recuperer la popularite d'un tag
function balise_POPULARITE_TAG($p) {
   include_spip('inc_spipicious');
   
   $_id_mot = champ_sql('id_mot', $p);
   $_id_article = champ_sql('id_article', $p);
   $p->code = "calcul_POPULARITE_TAG($_id_mot,$_id_article)";
   $p->statut = 'html';
   return $p;
}
?>
