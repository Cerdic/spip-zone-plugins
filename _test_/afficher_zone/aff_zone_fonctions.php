<?php
  if (!defined("_ECRIRE_INC_VERSION")) return;

  $GLOBALS['titres_enregistres'] = array();
  function traiter_titre($titre) {
    // nettoyer le nom
      switch (substr_count($titre, '[')) {
          case 0: 
          break;
          case 1: $titre = preg_replace('/\[.*?\]/is', '', $titre);
          break;
          default:
              if (substr_count($titre, '[fr]') > 0) {
                  preg_match('/\[fr\]([^\[]*)/is', $titre, $res);
                  $titre = $res[1];
              }
              else {
                  preg_match('/\[.*?\]([^\[]*)/is', $titre, $res);
                  if ($res[1]) $titre = $res[1];
                  
              }
      }
    // virer le version du titre
      $titre =  preg_replace('/ - Version .*$/is', '', $titre);
    // virer les "pour x.x.x" et autre num de version dans le titre ainsi que les trucs entre () ou /!\
      $titre =  preg_replace(array('/\(.*?\)/is', '/( pour)?[0-9\.]*$/is', ',\/!\\\\.*?\/!\\\\,is'), '', $titre);
      $titre =  trim(rtrim(preg_replace('/pour(.*)$/is', '', $titre)));
      
    // ne pas afficher 2 fois le même plugin
      if (in_array($titre, $GLOBALS['titres_enregistres'])) return;
      
      $GLOBALS['titres_enregistres'][] = $titre;
      return $titre;
  }
  
  function makearray($liste) {
    return explode(',', $liste);
  }
  
  function makelike($ch) {
    return '%>%'.$ch.'%';
  }
  
  function sauts_lignes($txt) {
    return preg_replace('/[\r\n]/is', "\r\n&lt;br /&gt;", $txt);
  }
  
  function sans_version($txt) {
    return preg_replace('/ - Version.*$/is', '', $txt);
  }
  
?>
