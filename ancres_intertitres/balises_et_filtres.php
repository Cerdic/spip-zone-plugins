<?php

 function balise_TABLE_MATIERE_dist($p) {
  $b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
  if ($b === '') {
   erreur_squelette(
    _T('zbug_champ_hors_boucle',
    array('champ' => '#TABLE_MATIERE')
    ), $p->id_boucle);
   $p->code = "''";
  }
  
  $p->code = "recuperer_fond(
   'modeles/article_table_matiere',
   array(
    'id_article' => ".champ_sql('id_article', $p).",
    'table_matiere' => AncresIntertitres_table_matiere('retour'))
  )";

		$p->interdire_scripts = false; // securite apposee par recuperer_fond()
		return $p;
 }

	function liste_non_ordonnee($table_matiere) {
	 $texte = '';
		if(!empty($table_matiere))
 	 foreach($table_matiere as $url => $titre) {
 	  $texte .= "\t<li><a href=\"#".$url."\">".$titre."</a></li>\n";
   }
	 return $texte ?
	  "<ul>\n".$texte."</ul>\n"
   : '';
 }

?>