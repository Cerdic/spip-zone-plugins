<?php
/**
 * Réaffiche le composant à crayonner, avec un lien d'édition
 * TODO: édition côté client en dhtml
 *
 * @param array $regs
 * @return array (html, status)
 */
function controleurs_composant($regs) {
  include_spip('public/assembler');
  include_spip('inc/acs_widgets');
  
  list(,$crayon,$type,$champ,$id) = $regs;
  $c = $champ.'/'.$champ;
  $crayon = new Crayon("'composant-$champ-" . $id, $valeur, array('hauteurMini' => 24));
  $html = '<div style="width:'.$crayon->largeur.'px; height:'.$crayon->hauteur.'px">'.
  '<div style="position: absolute; opacity: 0.5;width:'.$crayon->largeur.'px; height:'.$crayon->hauteur.'px">'.
    recuperer_fond("composants/$c").
  '</div>'.  
  '<div style="position: relative; opacity: 1;>'._T('crayons:editer').
    liste_widgets(array($champ),true).
  	'</div>'.
  '</div>';
  $status = NULL;

	return array($html, $status);
}
?>