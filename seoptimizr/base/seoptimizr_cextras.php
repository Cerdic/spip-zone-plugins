<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function seoptimizr_declarer_champs_extras($champs = array()) {
  
  $champs['spip_rubriques']['seoptimizr_rub_url_301'] = array(
      'saisie' => 'input',//Type du champ (voir plugin Saisies)
      'options' => array(
            'nom' => 'seoptimizr_rub_url_301', 
            'label' => _T('seoptimizr:seoptimizr_rub_url_301'), 
            'sql' => "varchar(30) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
                        'modifier' => array('auteur' => 'webmestre')),//Seuls les webmestres peuvent modifier
      ),
  );
  

  $champs['spip_rubriques']['seoptimizr_rub_meta_robots'] = array(
      'saisie' => 'input',//Type du champ (voir plugin Saisies)
      'options' => array(
            'nom' => 'seoptimizr_rub_meta_robots', 
            'label' => _T('seoptimizr:seoptimizr_rub_meta_robots'), 
            'sql' => "varchar(30) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
                        'modifier' => array('auteur' => 'webmestre')),//Seuls les webmestres peuvent modifier
      ),
  );
  


  $champs['spip_articles']['seoptimizr_art_url_301'] = array(
      'saisie' => 'input',//Type du champ (voir plugin Saisies)
      'options' => array(
            'nom' => 'seoptimizr_art_url_301', 
            'label' => _T('seoptimizr:seoptimizr_art_url_301'), 
            'sql' => "varchar(30) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
                        'modifier' => array('auteur' => 'webmestre')),//Seuls les webmestres peuvent modifier
      ),
  );
  $champs['spip_articles']['seoptimizr_art_meta_robots'] = array(
      'saisie' => 'input',//Type du champ (voir plugin Saisies)
      'options' => array(
            'nom' => 'seoptimizr_art_meta_robots', 
            'label' => _T('seoptimizr:seoptimizr_art_meta_robots'), 
            'sql' => "varchar(30) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
                        'modifier' => array('auteur' => 'webmestre')),//Seuls les webmestres peuvent modifier
      ),
  );


  $champs['spip_mots']['seoptimizr_mot_url_301'] = array(
      'saisie' => 'input',//Type du champ (voir plugin Saisies)
      'options' => array(
            'nom' => 'seoptimizr_mot_url_301', 
            'label' => _T('seoptimizr:seoptimizr_mot_url_301'), 
            'sql' => "varchar(30) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
                        'modifier' => array('auteur' => 'webmestre')),//Seuls les webmestres peuvent modifier
      ),
  );
  $champs['spip_mots']['seoptimizr_mot_meta_robots'] = array(
      'saisie' => 'input',//Type du champ (voir plugin Saisies)
      'options' => array(
            'nom' => 'seoptimizr_mot_meta_robots', 
            'label' => _T('seoptimizr:seoptimizr_mot_meta_robots'), 
            'sql' => "varchar(30) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
                        'modifier' => array('auteur' => 'webmestre')),//Seuls les webmestres peuvent modifier
      ),
  );


  return $champs; 
}
?>