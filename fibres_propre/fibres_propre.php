<?php

function fibres_propre_pre_propre($texte) {
  include_spip('inc/classTextile');
  if($texte) {
   $textile = new Textile();
   $texte = echappe_retour($texte); 
   $texte = echappe_html('<html>'.$textile->TextileThis($texte).'</html>');
   return $texte;
  }
}

?>
