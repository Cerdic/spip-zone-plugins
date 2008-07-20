<?php
/**
 * Réaffiche le composant à crayonner, avec son éditeur
 *
 * @param array $regs
 * @return array (html, status)
 */

function controleurs_composant($regs) {
  global $spip_lang;
  
  include_spip('public/assembler');
  include_spip('inc/acs_presentation');
  include_spip('lib/composant/classComposantPrive');
  //include_spip('inc/traduire');

  list(,$crayon,$type,$champ,$id) = $regs;
  
  $c = $champ.'/'.$champ;
  $crayon = new Crayon("composant-$champ-" . $id, $valeur, array('hauteurMini' => 24, 'largeurMaxi' => 1280, 'hauteurMaxi' => 1024));

  //$GLOBALS['idx_lang'] = 'i18n_ecrire'.$spip_lang;
  $composant = new AdminComposant($champ);
  $icon = find_in_path('composants/'.$champ.'/img_pack/'.$champ.'_icon.gif');
  $html = '
  <div style="width:'.$crayon->largeur.'px; height:'.$crayon->hauteur.'px">'.
    '<div style="position: absolute; opacity: 0.5;width:'.$crayon->largeur.'px; height:'.$crayon->hauteur.'px">'.
      recuperer_fond("composants/$c").
    '</div>'.  
    '<div style="position: relative; opacity: 1;">'.
    '<a href="'._DIR_RESTREINT.'index.php?exec=acs&onglet=composants&composant='.$champ.'"><img src="'.$icon.'" alt="'.$champ.'" title="'._T('crayons:editer').' '._T($champ).'" /></a>'.
  	'</div>'.
  	'<div class="edit_composant" style="position: absolute; display: block; top:0; left:0; z-index: 99999999">'.
    acs_box(_T($composant->type), $composant->edit(), $composant->icon).
    '</div>'.
	'</div>'.
  '</script><script language="javascript">$(".edit_composant").Draggable({zIndex: 99999999});</script>';
  $status = NULL;

	return array($html, $status);
}
?>