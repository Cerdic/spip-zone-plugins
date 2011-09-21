<?php
/**
 * Affiche le diff d'un objet 
 * (uniquement pour les articles en 2.0.X)
 * Ex: [<small> (#ID_ARTICLE|affiche_diff{article,#ID_VERSION,diff}|supprimer_tags|couper{50})</small>]
 * 
 * @param int $id_objet 
 * @param string $objet
 * @param int $id_version
 * @param string $format [optional]
 * @return 
 */
function affiche_diff($id_objet, $objet, $id_version, $format='complet', $id_diff=NULL) {
  include_spip('inc/suivi_versions');
/*
  if($GLOBALS['spip_version_branche'] < '2.1'){
    if($objet == 'article'){
      $textes = revision_comparee($id_objet, $id_version, $format);
    }else{
      return;
    }
  }else{
    $textes = revision_comparee($id_objet, $objet, $id_version, $format);
  }
*/
  $textes = revision_comparee($id_objet, $id_version, $format, $id_diff);
/*
  $ret = '';
  foreach ($textes as $champ => $texte) {
    $texte = propre_diff($texte);

    if ($champ == 'titre')
      $texte = "<h1>$texte</h1>";
    else
      $texte = "<div class='$champ'>$texte</div>";

    $ret .= "\n". $texte;
  }*/
  foreach ($textes as $champ => $texte) {
    $textes[$champ] = propre_diff(PtoBR($texte));
  }
  $contexte = array(
      'id_article' => $id_objet,
      'id_version' => $id_version,
      'lang' => $GLOBALS['spip_lang']
  );
  $contexte = array_merge($contexte, $textes);
  return recuperer_fond('composants/article/inc-article_version', $contexte);
}

/**
 * Affiche le nom de l'auteur à partir de son id_auteur
 * 
 * @param object $auteur
 * @return 
 */
function affiche_auteur_diff($auteur) {
  // Si c'est un nombre, c'est un auteur de la table spip_auteurs
  if ($auteur == intval($auteur)
  AND $s = sql_getfetsel("nom","spip_auteurs","id_auteur=".intval($auteur))) {
    return typo($s);
  } else {
    return $auteur;
  }
}
?>