<?php

function TriMots_ajouter_boite_gauche($arguments) {
  if($arguments['args']['exec'] == 'articles') {
	return $arguments['data'] .= TriMots::boite_tri_mots($arguments['args']['id_article']);
  }
  return $arguments['data'];
}
  
function TriMots_boite_tri_mots($id_article) {
  $to_ret = '<div>&nbsp;</div>';
  $to_ret .= '<div class="bandeau_rubriques" style="z-index: 1;">';
  $to_ret .= bandeau_titre_boite2('Tri Mots',"article-24.gif","white","black", false);
  $to_ret .= '<div class="plan-articles">';
  $to_ret .= 'TRI'.$id_article;
  $to_ret .= '</div></div>';
  return $to_ret;
}

?>
