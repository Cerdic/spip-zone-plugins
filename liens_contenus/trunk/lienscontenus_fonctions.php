<?php
// Les filtres pour les squeletes
function lienscontenus_generer_url($type_objet, $id_objet)
{
  // TODO : Ajouter les autres
	$liste_urls = array(
    'rubrique' => array('naviguer', 'id_rubrique'),
    'article' => array('articles', 'id_article'),
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

function lienscontenus_verifier_si_existe($type_objet, $id_objet)
{
	switch ($type_objet) {
		case 'modele':
			if(find_in_path('modeles/'.$id_objet.'.html')) {
				return 'ok';
			} else {
				return 'ko';
			}
			break;
		default:
			include_spip('base/abstract_sql');
			if (in_array($type_objet, array('syndic', 'forum'))) {
				$nb = sql_countsel("spip_".$type_objet, "id_".$type_objet."="._q($id_objet));
			} else {
				// Marche aussi pour les formulaires (type = "form")
                $nb = sql_countsel("spip_".$type_objet."s", "id_".$type_objet."="._q($id_objet));
			}
			return ($nb == 1 ? 'ok' : 'ko');
	}
}

function lienscontenus_icone_statut($type_objet, $id_objet)
{
	$statut = lienscontenus_statut($type_objet, $id_objet);
  if ($statut != '') {
    return '<img src="'._DIR_PLUGIN_LIENSCONTENUS.'/images/statut-'.$statut.'.gif" align="absmiddle" alt="'._T('lienscontenus:statut_'.$statut).'" />';
  } else {
    return '';
  }
}

function lienscontenus_statut($type_objet, $id_objet)
{
	$listeStatuts = array('prepa', 'prop', 'publie', 'refuse', 'poubelle');
	include_spip('base/abstract_sql');
	if ($type_objet == 'document') {
    // TODO: gérer le statut des docs si médiathèque est installé
    $statut = 'publie';
  } elseif ($type_objet == 'modele') {
    $statut = find_in_path('modeles/'.$id_objet.'.html') ? 'publie' : 'poubelle';
  } elseif ($type_objet == 'auteur') {
    $statut = sql_getfetsel("statut", "spip_auteurs", "id_auteur="._q($id_objet));
    if ($statut != '') {
      if ($statut == '5poubelle') {
        $statut = 'poubelle';
      } else {
        $statut = 'publie';
      }
    }
  } elseif (in_array($type_objet, array('syndic', 'forum'))) {
    $statut = sql_getfetsel("statut", "spip_".$type_objet, "id_".$type_objet."="._q($id_objet));
	} else {
		// Marche aussi pour les formulaires (type = "form")
    $statut = sql_getfetsel("statut", "spip_".$type_objet."s", "id_".$type_objet."="._q($id_objet));
	}
  return $statut;
}

function lienscontenus_titre_contenu($type_objet, $id_objet)
{
  switch ($type_objet) {
  	case 'syndic':
	    if (!$nom_site = sql_getfetsel("nom_site", "spip_syndic", "id_syndic="._q($id_objet))) {
        $nom_site = _T('lienscontenus:inexistant');
      }
	    return _T('lienscontenus:type_syndic', array('nom_site' => $nom_site, 'id_objet' => $id_objet));
	    break;
  	case 'forum':
	    $titre_message = sql_getfetsel("titre", "spip_forum", "id_forum="._q($id_objet));
	    return _T('lienscontenus:type_forum', array('titre_message' => $titre_message, 'id_objet' => $id_objet));
	    break;
    case 'auteur':
      $nom = sql_getfetsel("nom", "spip_auteurs", "id_auteur="._q($id_objet));
      return _T('lienscontenus:type_auteur', array('nom' => $nom));
  	case 'modele':
	    return _T('lienscontenus:type_modele', array('type_objet' => $type_objet, 'id_objet' => $id_objet));
	    break;
  	default:
	    // Marche aussi pour les formulaires (type = "form")
	    return sql_getfetsel("titre", "spip_".$type_objet."s", "id_".$type_objet."="._q($id_objet));
  }
}
?>