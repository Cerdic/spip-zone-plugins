<?php
/*
 * SPIP - Plugin "Comments phpBB"
 * Création des topics pour tous les articles déjà existant dans SPIP
 * 
 * Auteur : David Dorchies http://dorch.fr
 * (c)2009 - Distribué sous licence GPL
 */
if (!defined("_ECRIRE_INC_VERSION")) return;


function exec_comments_phpbb_maj() {
    if (!autoriser('voir', 'nom')) {
        include_spip('inc/minipres');
        echo minipres();
        exit;
    }
      
   if(!function_exists('comments_phpbb_new'))
      include_spip('comments_phpbb_pipeline');
      
   //Préparation des arguments de la fonction comments_phpbb_new
   $flux=array('args' => array('table' => $GLOBALS['table_prefix'].'_'.table_objet('article')));
      
   //Requete pour récupérer les identifiants des articles SPIP existants
   $result = sql_select('id_article',$flux['args']['table']);
   
   //Pour chaque article existant
   while ($article = sql_fetch($result)) {
      $flux['args']['id_objet'] = $article['id_article'];
      comments_phpbb_new($flux);
   }
   include_spip('inc/presentation');
   #
    // pipeline d'initialisation
    pipeline('exec_init', array('args'=>array('exec'=>'nom'),'data'=>''));
    // entetes
    $commencer_page = charger_fonction('commencer_page', 'inc');
    // titre, partie, sous_partie (pour le menu)
    echo $commencer_page(_T('comments_phpbb:titre'), "editer", "editer");
    // titre
    echo "<br /><br /><br />\n"; // outch ! aie aie aie ! au secours !
    echo gros_titre(_T('comments_phpbb:titre'),'', false);
    // colonne gauche
    echo debut_gauche('', true);
    echo pipeline('affiche_gauche', array('args'=>array('exec'=>'nom'),'data'=>''));
   
    // colonne droite
    echo creer_colonne_droite('', true);
    echo pipeline('affiche_droite', array('args'=>array('exec'=>'nom'),'data'=>''));
   
    // centre
    echo debut_droite('', true);
    // contenu
    // ...
    
    echo '<p>'._T('comments_phpbb:maj_ok_texte').'</p>';
    
    // ...
    // fin contenu
    echo pipeline('affiche_milieu', array('args'=>array('exec'=>'nom'),'data'=>''));
    echo fin_gauche(), fin_page();
}
?>