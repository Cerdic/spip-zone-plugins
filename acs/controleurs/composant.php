<?php
/**
 * Réaffiche le composant à crayonner, avec son éditeur
 *
 * @param array $regs
 * @return array (html, status)
 */

function controleurs_composant_dist($regs) {
  global $spip_lang;
  
  include_spip('public/assembler');
  include_spip('inc/acs_presentation');
  include_spip('lib/composant/classComposantPrive');

  list(,$crayon,$type,$class,$id) = $regs;
  
  $c = $class.'/'.$class;
  $crayon = new SecureCrayon("composant-$class-$id");

  $composant = new AdminComposant($class, $id);  
  $icon = find_in_path('composants/'.$class.'/images/'.$class.'_icon.gif');
  if (($crayon->left + $crayon->largeur) > ($crayon->ww / 2))
    $left = ($crayon->left < 300) ? 0 : - 300;
  else
    $left = Min($crayon->ww / 2, $crayon->left + $crayon->largeur);
	$contexte = array(
    'c' => 'composants/'.$c,
    'nic' => $id,
    'lang' => $GLOBALS['spip_lang']
  );
  $css_class = _request('class');
  $html = '<div style="width:'.$crayon->w.'px; height:'.$crayon->h.'px">'.
    '<div id="'."composant-$class-$id".'" style="position: absolute; border: 2px outset #fddf00; top: -1px;left: -1px;opacity: 0.98; width:'.$crayon->w.'px; height:'.$crayon->h.'px; font-size:'._request('em').'"'.($css_class ? ' class="'.$css_class.'"' : '').'>'.
      recuperer_fond('vues/composant', $contexte).
    '</div>'.
    '<div style="position: relative; opacity: 1;">'.
    '<a href="'._DIR_RESTREINT.'?exec=acs&amp;onglet=composants&amp;composant='.$class.'&amp;nic='.$id.'"><img src="'.$icon.'" alt="'.$class.'" title="'._T('crayons:editer').' '._T($champ).'"  class="bouton_edit_composant" /></a>'.
  	'</div>'.
  	'<div class="edit_composant" style="position: absolute; display: block; top:0; left:'.$left.'px; z-index: 99999999">'.
    acs_box(
      $composant->T('nom').($id==0 ? '' : ' '.$id),
      '<form id="acs" name="acs" class="formulaire_crayon" action="?action=crayons_composant_store" method="post">'.
        '<input type="hidden" class="crayon-id" name="crayons[]" value="'.$crayon->key.'" />'."\n".
        '<input type="hidden" name="name_'.$crayon->key.'" value="'.$crayon->name.'" />'."\n".
        '<input type="hidden" name="md5_'.$crayon->key.'" value="'.$crayon->md5.'" />'."\n".
      	'<input type="hidden" name="var_mode" value="recalcul" />'.
        $composant->edit('controleur').
        crayons_boutons().
      '</form>',
      $composant->icon,
      'editeur_composant'
    ).
    '</div>'.
	'</div>'.
'<script language="javascript">	init_controleur_composant();</script>';
  $status = NULL;

	return array($html, $status);
}

?>