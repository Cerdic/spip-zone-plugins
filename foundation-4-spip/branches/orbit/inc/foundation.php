<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction de callback utiliser par le filtre |iframe_responsive
 * A chaque iFrame, on encadre de div.flex-video.
 * Si vimeo est détecté, on ajoute la class vimeo
 * @param  string $matches iframe
 * @return string          iframe encadrée
 */
function responsive($matches) {
  // Dans le cas de vimeo, il faut ajouter une classe
  if (strpos($matches[0], 'vimeo')) $vimeo = ' vimeo';
  else $vimeo = '';

  // On inclu les filtres, au cas ou
  include_spip('inc/filtres');

  // On revoie la bonne structure html d'iframe.
  return wrap('<iframe '.$matches[0].'></iframe>', '<div class="flex-video'.$vimeo.'">');;
}


/**
 * Récupération des fichier javascript de foundation
 */
function foundation_get_js($flux = '') {

  // On lit la configuration du plugin pour savoir quel version de Foundation charger.
  $config = lire_config('foundation');

  // On renvoie le flux head avec le squelette foundation correspondant.
  if ($config['variante'] == '3')
    $flux .= $flux.recuperer_fond('inclure/head-foundation-3');
  elseif ($config['variante'] == '4')
    $flux .= $flux.recuperer_fond('inclure/head-foundation-4');
  elseif ($config['variante'] == '5')
    $flux .= $flux.recuperer_fond('inclure/head-foundation-5');
  // Si foundation est désactivé, on revoie directement le flux, sans aller chercher le head-foundation.
  else
    return $flux;

  // Charger le head commun a foundation
  $flux = $flux.recuperer_fond('inclure/head-foundation');

  return $flux;
}

/**
 * Récupération des fichier css de foundation
 */
function foundation_get_css($flux = '') {

  // On lit la configuration du plugin pour savoir quel version de Foundation charger.
  $config = lire_config('foundation');

  // On renvoie le flux head avec le squelette foundation correspondant.
  if ($config['variante'] == '3')
    $flux .= $flux.recuperer_fond('inclure/css/head-foundation-3');
  elseif ($config['variante'] == '4')
    $flux .= $flux.recuperer_fond('inclure/css/head-foundation-4');
  elseif ($config['variante'] == '5')
    $flux .= $flux.recuperer_fond('inclure/css/head-foundation-5');
  // Si foundation est désactivé, on revoie directement le flux, sans aller chercher le head-foundation.
  else
    return $flux;

  return $flux;
}