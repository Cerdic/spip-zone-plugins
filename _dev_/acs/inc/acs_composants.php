<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Page composant
 */
include_spip('lib/composant/classComposantPrive');

function composants_gauche($c) {
  $nom = $c->T('nom');
  if ($nom == $c->type.' nom') $nom = ucfirst($c->type);  
  return acs_box($nom, $c->gauche(), $c->icon, false, '<img src="'._DIR_PLUGIN_ACS.'/img_pack/info.png" />');
}

function composants($c) {
  include_spip('lib/composant/composant_select');
  include_spip('lib/composant/composants_liste');

  $r .= acs_box(
         select_composant(array_keys(composants_liste()), $c->type),
         $c->edit(true),
         _DIR_PLUGIN_ACS."/img_pack/composant-24.gif"
        );
  $r .='<br /><a name="cTrad"></a><div id="cTrad"></div>'; // Container for translations - Ajax
  return $r;
}
?>