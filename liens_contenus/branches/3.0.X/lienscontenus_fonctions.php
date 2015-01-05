<?php
// Les filtres pour les squeletes
function lienscontenus_generer_url($type_objet, $id_objet)
{
  // TODO : Ajouter les autres
	$liste_urls = array(
    'rubrique' => array('rubrique', 'id_rubrique'),
    'article' => array('article', 'id_article'),
    'breve' => array('breves_voir', 'id_breve'),
    'syndic' => array('sites', 'id_syndic'),
    'mot' => array('mots_edit', 'id_mot'),
    'auteur' => array('auteur_infos', 'id_auteur'),
    'form' => array('forms_edit', 'id_form')
	);
	if (isset($liste_urls[$type_objet])) {
		return $GLOBALS['meta']['adresse_site'].'/ecrire/?exec='.$liste_urls[$type_objet][0].'&amp;'.$liste_urls[$type_objet][1].'='.$id_objet;
	} else {
		$f = 'lienscontenus_generer_url_'.$type_objet;
		if (function_exists($f)) {
			return $f($id_objet);
		} else {
			// On ne devrait pas se retrouver la
			spip_log('Plugin liens_contenus : il manque une fonction de generation d\'url pour le type '.$type_objet, 'liens_contenus');
			return '#';
		}
	}
}

function lienscontenus_generer_url_document($id_objet)
{
	include_spip('base/abstract_sql');
	$res = sql_getfetsel("id_objet, objet", "spip_documents_liens", "id_document="._q($id_objet));
	if (sql_count($res) == 1) {
		$row = sql_fetch($res);
		return lienscontenus_generer_url($row['objet'], intval($row['id_objet']));
	}
}

function lienscontenus_generer_url_modele($id_objet)
{
	return find_in_path('modeles/'.$id_objet.'.html');
}

function lienscontenus_generer_url_modele_non_reconnu($id_objet)
{
	return '#';
}

function lienscontenus_presentation_lien($type_objet, $id_objet)
{
  include_spip('base/abstract_sql');
  
  $listeStatuts = array('prepa', 'prop', 'publie', 'refuse', 'poubelle');
  $infos = array('type_objet' => $type_objet, 'id_objet' => $id_objet);
  $existe = false;

  if ($type_objet == 'modele') {
    if (find_in_path('modeles/'.$id_objet.'.html')) {
      $existe = true;
      $infos['statut'] = 'publie';
    }
  } else {
    $requetes = array(
      'syndic' => array('champs' => 'nom_site AS titre, statut', 'table' => 'spip_syndic', 'id' => 'id_syndic'),
      'forum' => array('champs' => 'titre, statut', 'table' => 'spip_forum', 'id' => 'id_forum'),
      'mot' => array('champs' => 'titre', 'table' => 'spip_mots', 'id' => 'id_mot'),
      'auteur' => array('champs' => 'nom AS titre', 'table' => 'spip_auteurs', 'id' => 'id_auteur'),
      'article' => array('champs' => 'titre, statut', 'table' => 'spip_articles', 'id' => 'id_article'),
      'rubrique' => array('champs' => 'titre, statut', 'table' => 'spip_rubriques', 'id' => 'id_rubrique'),
      'document' => array('champs' => 'titre, statut', 'table' => 'spip_documents', 'id' => 'id_document'),
      'breve' => array('champs' => 'titre, statut', 'table' => 'spip_breves', 'id' => 'id_breve')
      );
      
    if (!isset($requetes[$type_objet])) {
      spip_log('Affichage du type '.$type_objet.' non géré', 'lienscontenus');
      return 'non gere : '.$type_objet;
    }
  
    if ($row = sql_fetsel($requetes[$type_objet]['champs'], $requetes[$type_objet]['table'], $requetes[$type_objet]['id'].'='._q($id_objet))) {
      $existe = true;
      $infos = array_merge($infos, $row);
      if (!isset($infos['statut'])) {
        $infos['statut'] = 'publie';
      }
      switch ($type_objet) {
        case 'auteur':
          if ($infos['statut'] == '5poubelle') {
            $infos['statut'] == 'poubelle';
          } else {
            $infos['statut'] == 'publie';
          }
          break;
      }
    }
  }

  if ($existe) {
    return '<a href="'.lienscontenus_generer_url($type_objet, $id_objet).'" class="ok '.$infos['statut'].'">'._T('lienscontenus:type_'.$type_objet, $infos).'</a>';
  } else {
    return '<span class="ko">'._T('lienscontenus:type_'.$type_objet.'_inexistant', $infos).'</span>';
  }
      
}
?>
