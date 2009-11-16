<?php
function masquer_pre_boucle($boucle) {
  static $id_mot;
  static $liste_rubriques;
  static $liste_articles;

  // On ne masque que sur l'espace public
  if (test_espace_prive() || isset($boucle->modificateur['tout_voir'])) {
    return $boucle;
  }
  
  // Récupération de l'id_mot du mot dont le titre est "masquer"
  if (!isset($id_mot)) {
    $id_mot = sql_getfetsel("id_mot", "spip_mots", "titre='masquer'");
  }
  if (!isset($id_mot)) {
    spip_log('[Plugin « Masquer »] Il n\'y a pas de mot clef « masquer »');
    return $boucle;
  }
  
  // Récupération de la liste des rubriques masquées et leurs branches
  // Hors du cas de la boucle RUBRIQUE, car utilisé aussi par le cas de la boucle ARTICLES
  if (!isset($liste_rubriques)) {
    $liste_rubriques = array();
    // On masque les rubriques qui ont le mot clef
    $res = sql_select("id_rubrique", "spip_mots_rubriques", "id_mot=".$id_mot); 
    while($row = sql_fetch($res)) {
      $liste_rubriques = array_merge($liste_rubriques, liste_rubriques_branche($row['id_rubrique']));
    }
  }
  
  // Cas de la boucle RUBRIQUES
  if ($boucle->type_requete == 'rubriques') {
    if (count($liste_rubriques) == 0) {
      return $boucle;
    }
    $rub = $boucle->id_table . '.id_rubrique';
    $boucle->where[] = "array('NOT IN', '$rub', '(".implode(',', $liste_rubriques).")')";
  }

  // Cas de la boucle ARTICLES
  if ($boucle->type_requete == 'articles') {
    // On ne récupère la liste des articles masqués que si on a une boucle ARTICLES
    if (!isset($liste_articles)) {
      $liste_articles = array();
      // On masque les articles qui ont le mot clef
      $res = sql_select("id_article", "spip_mots_articles", "id_mot=".$id_mot); 
      while($row = sql_fetch($res)) {
        $liste_articles[] = $row['id_article'];
      }
      // On masque les articles qui sont dans des rubriques masquées
      if (count($liste_rubriques) != 0) {
        $res = sql_select("id_article", "spip_articles", "id_rubrique IN (".implode(',', $liste_rubriques).")"); 
        while($row = sql_fetch($res)) {
          $liste_articles[] = $row['id_article'];
        }
      }
    }
    if (count($liste_articles) == 0) {
      return $boucle;
    }
    $art = $boucle->id_table . '.id_article';
    $boucle->where[] = "array('NOT IN', '$art', '(".implode(',', $liste_articles).")')";
  }    

  return $boucle;
}

// Récupération de la liste des rubriques d'une branche
function liste_rubriques_branche($id_rubrique) {
  $liste = array($id_rubrique);
  $res = sql_select("id_rubrique", "spip_rubriques", "id_parent=".$id_rubrique); 
  while($row = sql_fetch($res)) {
    $liste[] = $row['id_rubrique'];
    $sous_rubriques = liste_rubriques_branche($row['id_rubrique']);
    $liste = array_merge($liste, $sous_rubriques);
  }
  return $liste;
}
?>