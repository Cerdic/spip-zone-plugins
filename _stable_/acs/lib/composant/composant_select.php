<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/** Retourne un select des composants
 * Return a component select
 */
function select_composant($choixComposants, $cc, $onglet) {
  if(isset($GLOBALS['meta']['acsSqueletteOverACS']) && $GLOBALS['meta']['acsSqueletteOverACS']) {
    $in_path = find_in_path('composants/'.$cc);
    if (!(strpos($in_path, $GLOBALS['meta']['acsSqueletteOverACS']) === false) && (strpos($in_path, _DIR_PLUGIN_ACS.'models/') === false))
      $over = '<img src="'._DIR_PLUGIN_ACS.'img_pack/over.gif" alt="over" title="'._T('acs:squelette').' '.$GLOBALS['meta']['acsSqueletteOverACS'].'" />';
  }
  $style = 0;
  $r = '<form name="acs_composant" action="" method="get">'.
    '<input type="hidden" name="exec" value="acs">'.
    '<input type="hidden" name="onglet" value="'.$onglet.'">'.
    '<table width="100%"><tr><td width="80%">'._T('acs:composant').'</td><td>'.$over.'</td><td><select name="composant" class="forml" style="width:auto" onchange="this.form.submit()"> ';
  foreach($choixComposants as $c)
    if ($c === '') $style++;
    else $r .= '<option name="'.$c.'" value="'.$c.'"'.(($c == $cc) ? ' selected': '').' class="btc_'.$style.'">'.ucfirst($c).'</option>';
  $r .= '</select></td><noscript><td><input type="submit" name="'._T('bouton_valider').'" value="'._T('bouton_valider').'" class="fondo"></td></noscript></tr></table></form>';
  return $r;
}

?>