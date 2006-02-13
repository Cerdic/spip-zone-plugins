<?php

function TriMots_ajouter_boite_gauche($arguments) {
  if($arguments['args']['exec'] == 'articles') {
	return $arguments['data'] .= TriMots_boite_tri_mots($arguments['args']['id_article']);
  }
  return $arguments['data'];
}
  
function TriMots_boite_tri_mots($id_article) {
  include_ecrire('inc_abstract_sql');
  $to_ret = '<div>&nbsp;</div>';
  $to_ret .= '<div class="bandeau_rubriques" style="z-index: 1;">';
  $to_ret .= bandeau_titre_boite2('Tri Mots',"article-24.gif","white","black", false);
  $to_ret .= '<div class="plan-articles">';
  $from = array('spip_mots_articles as lien','spip_mots as mots');
  $select = array('lien.rang','lien.id_mot','mots.titre');
  $where = array('lien.id_mot=mots.id_mot',"lien.id_article=$id_article");

  $rez = spip_abstract_select($select,$from,$where);
  $to_ret .= '<ul>';
  while($row = spip_abstract_fetch($rez)) {
    $to_ret .= '<li>';
    $to_ret .= $row['rang'].'<a href="'.generer_url_ecrire('tri_mots','id_mot='.$row['id_mot']).'">'.$row['titre'].'</a></li>';
  }
  $to_ret .= '</ul>';
  $to_ret .= '</div></div>';
  return $to_ret;
}

?>
