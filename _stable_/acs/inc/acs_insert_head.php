<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Implémentation du pipeline insert_head pour le plugin ACS
 * Insère les fonctions insert_head des composants dans le flux
 *
 * Les composants peuvent définir une classe <acsComposant> qui étend Composant,
 * avec éventuellement une méthode insert_head($flux), dans le fichier
 * composants/<composant>/<composant>.php
 * Celà permet l'insertion de headers caclulés à la volée par les composants ACS
 * Le résultat des méthodes insert_head d'ACS ET de ses composants est mis en cache.
 */

require_once _DIR_ACS.'inc/acs_cache.php';

function acs_insert_head($flux) {
  $r = cache('acs_insert_heads', 'aih_'.md5($flux), array($flux));
  $flux .= $r[0];
  return $flux;
}

function acs_insert_heads($flux) {
  require_once _DIR_ACS.'lib/composant/composants_liste.php';

  $js = find_in_path($GLOBALS['meta']['acsModel'].'.js.html');
  if ($js)
    $flux = '<script type="text/javascript" src="?page='.$GLOBALS['meta']['acsModel'].'.js"></script>';

  if (is_array(composants_liste())) {
    // composants_liste() est statique,  mise en cache,
    // et tient compte de l'override éventuel
    foreach (composants_liste() as $c=>$tag) {
      if (!isUsed($c)) continue;
      $file = find_in_path('composants/'.$c.'/'.$c.'.php');
      if (!$file) continue;
      require_once _DIR_ACS.'lib/composant/classComposantPublic.php';
      if (include_once($file)) { // Ne JAMAIS masquer erreur de cet include avec @ !!!
        $c = 'acs'.ucfirst($c);
        $$c = @new $c();
        if (($$c instanceof Composant) && is_callable(array($$c, 'insert_head')))
          $flux = $$c->insert_head($flux);
      }
    }
  }
  return $flux;
}

/**
 * Indique si un composant optionnel est activé
 * Return true if an optionnal component is on
 */
function isUsed($c) {
  if ($GLOBALS['meta']['acs'.ucfirst($c).'Use'] == 'oui') return true;
  return false;
}
?>