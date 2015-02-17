<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Réaffiche le composant à crayonner, avec son éditeur
 * Le crayon peut passer un id d'article, de rubrique, de mot-clé, ou de groupe 
 * de mot-clefs sous la forme: id_article-45 dans la classe du pinceau d'un 
 * composant dépendant de l'article ou de la rubrique.
 *
 * @param array $regs
 * @return array (html, status)
 */
function controleurs_composant_dist($regs) {
  global $spip_lang;
  
  include_spip('public/assembler');
  include_spip('inc/acs_presentation');
  include_spip('inc/composant/classComposantPrive');
	include_spip('inc/acs_widgets');

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
    'lang' => (_request('lang') ? _request('lang') : $GLOBALS['spip_lang'])
  );
  
  $css_class = _request('class'); /* classe du crayon */
  $ctxhtml = '';
  $contextes = array(
		'id_article'=>'/\bid_article-(\d+)\b/',
		'id_rubrique' => '/\bid_rubrique-(\d+)\b/',
		'id_mot' => '/\bid_mot-(\d+)\b/',
		'id_groupe' => '/\bid_groupe-(\d+)\b/',
		'recherche' => '/\brecherche-(\w+)\b/',
		'page' => '/\bpage-(\w+)\b/'
  );
  $matches = array();
  foreach($contextes as $c=>$re) {
  	if (preg_match($re, $css_class, $matches) > 0) {
  		$contexte[$c] = $matches[1];
  		$ctxhtml .= '<input type="hidden" name="'.$c.'" value="'.$contexte[$c].'" />';
  	}
  }
  $html = '<div style="width:'.$crayon->w.'px; height:'.$crayon->h.'px">'.
    '<div id="'."composant-$class-$id".'" style="position: absolute; border: 2px outset #fddf00; top: -1px;left: -1px;opacity: 0.98; width:'.$crayon->w.'px; height:'.$crayon->h.'px; font-size:'._request('em').'">'.
      recuperer_fond('vues/composant', $contexte).
    '</div>'.
    '<div style="position: relative; opacity: 1;">'.
    '<a href="'._DIR_RESTREINT.'?exec=acs&amp;onglet=composants&amp;composant='.$class.'&amp;nic='.$id.'"><img src="'.$icon.'" alt="'.$class.'" title="'._T('crayons:editer').' '._T($champ).'"  class="bouton_edit_composant" /></a>'.
  	'</div>'.
  	'<div class="edit_composant" style="top:0; left:'.$left.'px">'.
    acs_box(
      $composant->T('nom').($id==0 ? '' : ' '.$id),
      '<form id="acs" name="acs" class="formulaire_crayon" action="?action=crayons_composant_store" method="post">'.
        '<input type="hidden" class="crayon-id" name="crayons[]" value="'.$crayon->key.'" />'."\n".
        '<input type="hidden" name="name_'.$crayon->key.'" value="'.$crayon->name.'" />'."\n".
        '<input type="hidden" name="md5_'.$crayon->key.'" value="'.$crayon->md5.'" />'."\n".
      	$ctxhtml.
        $composant->edit('controleur').
        crayons_boutons().
      '</form>',
      $composant->icon,
      'editeur_composant',
      (($composant->nb_widgets > 0) ? '<a class="btn_show_widgets" href="'._DIR_RESTREINT.'?exec=acs&amp;onglet=composants&amp;composant='.$class.'&amp;nic='.$id.'"><img src="'. _DIR_PLUGIN_ACS.'/images/composant-24.gif" alt="widgets" height="16px" width="16px" style="float:right" /></a>' : '')
    ).
    '</div>'.
	'</div>'.
'<script language="javascript">jQuery(document).ready(
	function() {
  	init_controleur_composant();
	}
);</script>';
  $status = NULL;

	return array($html, $status);
}

?>