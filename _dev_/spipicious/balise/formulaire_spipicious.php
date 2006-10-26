<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_FORMULAIRE_SPIPICIOUS ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_SPIPICIOUS', array('id_article', 'page'));
}


/*
* La fonction statique retourne fait les verifications sur les variables récupérées par collecte
* ici, $args[0] contient donc id_article.
*
* ensuite en renvoi les arguments en questions pour la balise dynamique.
* 
*/
function balise_FORMULAIRE_SPIPICIOUS_stat($args, $filtres) {  
  
	// Pas d'id_article ? Erreur de squelette 
	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_SPIPICIOUS',
					'motif' => 'ARTICLES')), '');

	return $args;
}
/* la fonction dynamique fait les calculs et renvois le squelette qu'il faut afficher.
_request() cherche dans les valeurs post.
*/

function balise_FORMULAIRE_SPIPICIOUS_dyn($id_article,$page) {
  global $auteur_session;
  $auteur_nom = $auteur_session['nom'];
  $auteur_id = $auteur_session['id_auteur'];

  include_spip('spipicious_fonctions');
  $msg = "** testing **";
  
  //recuperation des variables utiles  
  $tags = _request('tags');
  //$id_article = _request('id_article');
  
  $table_pref = 'spip';
  if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];
  $groupe_tags = spipicious_get_idgroup_tags();  // FIXME si retourne false -> break (pas installe)
  $id_groupe_tags = $groupe_tags['id_groupe'];
  $type_groupe_tags = $groupe_tags['titre'];
  
  // action ?   
  if ($tags && $auteur_id) {
    $tableau_tags = explode(" ",$tags); 
    if (is_array($tableau_tags)) { 
     foreach ($tableau_tags as $k=>$tag) {  
  
      // doit on creer un nouveau tag ? 
      $tag = strtolower(trim($tag));  
      $result = spip_query("SELECT id_mot FROM {$table_pref}_mots WHERE titre='$tag' AND id_groupe=$id_groupe_tags");
      if (spip_num_rows($result) == 0) { // creation tag
        $sql = "INSERT INTO {$table_pref}_mots (titre,id_groupe,type,idx) VALUES('".addslashes($tag)."',$id_groupe_tags,'oui','$type_groupe_tags')";  // FIXME a securiser + verifier encodage caractere
        $result = spip_query($sql);
        $id_tag = spip_insert_id();
      } else {  // on recupere l'id du tag 
        while($row=spip_fetch_array($result)){
          $id_tag = $row['id_mot'];
        }
      }
    
      // on lie le mot au couple article (uniquement si pas deja fait)
      $result = spip_query("SELECT id_mot FROM {$table_pref}_mots_articles WHERE id_mot=$id_tag AND id_article=$id_article");
      if (spip_num_rows($result) == 0) {
         spip_query("INSERT INTO {$table_pref}_mots_articles(id_mot,id_article) VALUES('$id_tag','$id_article')");     
      }      
      
      // auteur identifie:  on enregistre le couple (mot / article / auteur) ds la table spipicious
      // FIXME verifier si table installe
      // ajout uniquement si pas deja tague  ?
      $result = spip_query("SELECT id_mot FROM {$table_pref}_spipicious WHERE id_mot='$id_tag' AND id_auteur='$auteur_id' AND id_article='$id_article' ");
      $msg .="SELECT id_mot FROM {$table_pref}_spipicious WHERE id_mot='$id_tag' AND id_auteur='$auteur_id' AND id_article='$id_article'";
      if (spip_num_rows($result) == 0) {
              spip_query("INSERT INTO {$table_pref}_spipicious(id_mot,id_auteur,id_article) VALUES('$id_tag','$auteur_id','$id_article')"); 
              $msg .="\nINSERT INTO {$table_pref}_spipicious(id_mot,id_auteur,id_article) VALUES('$id_tag','$auteur_id','$id_article')\n";
       }
     
      
     }
    }
    
    
  }

    
  // retour sur le formulaire  
  return array('formulaires/formulaire_spipicious', $GLOBALS['delais'],
				   array('self' => $url,
						 'id' => $id_article,
						 'fond' => $page,
						 'auteur_nom' => $auteur_nom,
						 'auteur_id' => $auteur_id,
						 'msg' => $msg						 
						 )
				   );
  
}

?>
