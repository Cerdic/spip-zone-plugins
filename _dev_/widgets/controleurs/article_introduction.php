<?php

function controleurs_article_introduction_dist($regs) {
    list(,$widget,$type,$champ,$id) = $regs;
//    return array($widget, 0);
    $s = spip_query(
    'SELECT descriptif, chapo, texte FROM spip_articles WHERE id_article=' . $id);
    if (!($t = spip_fetch_array($s))) {
	    return array("$type $id $champ: " . _U('widgets:pas_de_valeur'), 6);
    }

    // taille du widget
    $w = intval($_GET['w']);
    $w = $w<100 ? 100 : $w;
    $w = $w>700 ? 700 : $w;
    $h = intval($_GET['h']);
    $h = $h<234 ? 234 : $h;
    // hauteur maxi d'un textarea -- pas assez ? trop ?
    $wh = intval($_GET['wh']); // window height
    $maxheight = min(max($wh-50,400), 700);
    if ($h>$maxheight) $h=$maxheight;
    $hauteur = array('descriptif' => (int)ceil($h*2/13),
    				 'chapo' => (int)ceil($h*4/13),
    				 'texte' => (int)ceil($h*4/13));

	foreach ($t as $champs => $valeur) {
        $n = new Widget('article-introduction:' . $champs . '-' . $id, $valeur);
        $widgetsCode[$champs] = $n->code();
        $widgetsInput[$champs] = $n->input('texte', array(
	        'style' => 'width:' . $w . 'px; height:' . $hauteur[$champs] . 'px;'));
	}

    $widgetsAction = str_replace('widgets_html', 'widgets_store', self());
    $widgetsImgPath = dirname(find_in_path('images/cancel.png'));

    // title des boutons
    $OK = texte_backend(_T('bouton_enregistrer'));
    $Cancel = texte_backend(_L('Annuler'));
    $Editer = texte_backend(_L("&Eacute;diter $type $id"));
    $url_edit = "ecrire/?exec={$type}s_edit&amp;id_{$type}=$id";

	return array(<<<FIN_FORM

<form method="post" action="{$widgetsAction}">
  {$widgetsCode['descriptif']}
  {$widgetsInput['descriptif']}
  {$widgetsCode['chapo']}
  {$widgetsInput['chapo']}
  {$widgetsCode['texte']}
  {$widgetsInput['texte']}
  <div class="widget-boutons">
  <div>
    <a class="widget-submit" title="{$OK}">
      <img src="{$widgetsImgPath}/ok.png" width="20" height="20" />
    </a>
    <a class="widget-cancel" title="{$Cancel}">
      <img src="{$widgetsImgPath}/cancel.png" width="20" height="20" />
    </a>
    <a href="{$url_edit}" title="{$Editer}" class="widget-full">
      <img src="{$widgetsImgPath}/edit.png" width="20" height="20" />
    </a>
  </div>
</div>
</form>

FIN_FORM

	, null);
}

?>
