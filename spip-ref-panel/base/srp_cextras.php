<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function srp_declarer_champs_extras($champs = array()) {
  
  $champs['spip_rubriques']['rub_301'] = array(
      'saisie' => 'input',//Type du champ (voir plugin Saisies)
      'options' => array(
            'nom' => 'rub_301', 
            'label' => _T('srp:rub_301'), 
            'sql' => "varchar(30) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
                        'modifier' => array('auteur' => 'webmestre')),//Seuls les webmestres peuvent modifier
      ),
  );
  

  $champs['spip_rubriques']['rub_bot'] = array(
      'saisie' => 'input',//Type du champ (voir plugin Saisies)
      'options' => array(
            'nom' => 'rub_bot', 
            'label' => _T('srp:rub_bot'), 
            'sql' => "varchar(30) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
                        'modifier' => array('auteur' => 'webmestre')),//Seuls les webmestres peuvent modifier
      ),
  );
  


  $champs['spip_articles']['art_301'] = array(
      'saisie' => 'input',//Type du champ (voir plugin Saisies)
      'options' => array(
            'nom' => 'art_301', 
            'label' => _T('srp:art_301'), 
            'sql' => "varchar(30) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
                        'modifier' => array('auteur' => 'webmestre')),//Seuls les webmestres peuvent modifier
      ),
  );
  $champs['spip_articles']['art_bot'] = array(
      'saisie' => 'input',//Type du champ (voir plugin Saisies)
      'options' => array(
            'nom' => 'art_bot', 
            'label' => _T('srp:art_bot'), 
            'sql' => "varchar(30) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
                        'modifier' => array('auteur' => 'webmestre')),//Seuls les webmestres peuvent modifier
      ),
  );


  $champs['spip_mots']['mot_301'] = array(
      'saisie' => 'input',//Type du champ (voir plugin Saisies)
      'options' => array(
            'nom' => 'mot_301', 
            'label' => _T('srp:mot_301'), 
            'sql' => "varchar(30) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
                        'modifier' => array('auteur' => 'webmestre')),//Seuls les webmestres peuvent modifier
      ),
  );
  $champs['spip_mots']['mot_bot'] = array(
      'saisie' => 'input',//Type du champ (voir plugin Saisies)
      'options' => array(
            'nom' => 'mot_bot', 
            'label' => _T('srp:mot_bot'), 
            'sql' => "varchar(30) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
                        'modifier' => array('auteur' => 'webmestre')),//Seuls les webmestres peuvent modifier
      ),
  );


  return $champs; 
}
?>