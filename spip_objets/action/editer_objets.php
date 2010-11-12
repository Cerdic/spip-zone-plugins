<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_objets_dist() {
  //TODO : faire en sorte que les objets soient eux aussi sécurisés...
  $securiser_action = charger_fonction('securiser_action', 'inc');
  
  //TODO : le list ne marche que sur les index numérique ...
  // 
  $secu_action = $securiser_action("editer_"._request('nom_objet')."-"._request('id_objet'),_request('arg'),_request('redirect'),-1);
  $arg=$secu_action['arg'];
  
  // le -1 concerne le mode , et donc sécuriser_action vas renvoyer un tableau et pas une url
  $id_objet=_request('id_objet');

  // pas d'objet ? on en cree un nouveau, mais seulement si 'oui' en argument.
  if ($id_objet != intval($arg)) {
    if ($arg != 'oui') {
      include_spip('inc/headers');
      //TODO : le $arg ne peut pas contenir 'oui' car le securiser_action n'est pas en place...
      redirige_url_ecrire();
    }
    $id_objet = insert_objet();
  }

  if ($id_objet) $err = revisions_objet($id_objet);
  return array($id_objet,$err);
}


function insert_objet() {
  $champs = array(
    'titre' => _request('titre')
  );
  $objet=_request('objet');
  include_spip('inc/objets_fonctions_inc');
  $nom_objet=objets_nom_objet($objet);
  

  // Envoyer aux plugins
  //on récupére les champs extra de l'objet
  //include_spip('inc/cextras_gerer');
  $champs=array('titre'=>_request('titre'));

   /* foreach ($champs as $champ=>$desc_sql) {
      if (($a = _request($champ)) !== null) {
        $c[$champ] = $a;
      }
    }*/
  
  $id_objet = sql_insertq("spip_".$objet, $champs);
  return $id_objet;
}


// Enregistrer certaines modifications d'un objet
function revisions_objet($id_objet, $c=false) {

  $objet=_request('objet');
   
  // recuperer les champs dans POST s'ils ne sont pas transmis
  if ($c === false) {
    $c = array();
    
    $champs = array(
      'titre' => _request('titre')
    );

    //on récupére les champs extra de l'objet
    include_spip('inc/cextras_gerer');
    $champs=array_merge($champs,extras_champs(table_objet_sql($objet),''));

        
    
    foreach ($champs as $champ=>$desc_sql) {
      if (($a = _request($champ)) !== null) {
        $c[$champ] = $a;
      }
    }
  }
  
  //var_dump($id_objet);
  
  include_spip('inc/modifier');
  modifier_contenu($objet, $id_objet, array(
      'nonvide' => array('titre' => _T('info_sans_titre')),
      'invalideur' => "id='id_".$nom_objet."/$id_objet'"
    ),
    $c);
  include_spip('inc/objets_fonctions');
  // on fait les liaisons entre les objets et les parents
  objets_set_parents($objet,$id_objet,_request('parents'));
} 


?>