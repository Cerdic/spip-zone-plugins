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
        $res = false;
    } elseif (in_array($type_objet, array('syndic', 'forum'))) {
        $res = sql_select("statut", "spip_".$type_objet, "id_".$type_objet."="._q($id_objet));
	} else {
		// Marche aussi pour les formulaires (type = "form")
        $res = sql_select("statut", "spip_".$type_objet."s", "id_".$type_objet."="._q($id_objet));
	}
	if ($res) {
		$row = sql_fetch($res);
		if (in_array($row['statut'], $listeStatuts)) {
			return $row['statut'];
		} else {
			return '';
		}
	} else {
		return '';
	}
}

function lienscontenus_titre_contenu($type_objet, $id_objet)
{
  if ($type_objet == 'syndic') {
    $titre = sql_getfetsel("nom_site", "spip_syndic", "id_syndic="._q($id_objet));
  } elseif ($type_objet == 'forum') {
    $titre = sql_getfetsel("titre", "spip_forum", "id_forum="._q($id_objet));
  } else {
    // Marche aussi pour les formulaires (type = "form")
    $titre = sql_getfetsel("titre", "spip_".$type_objet."s", "id_".$type_objet."="._q($id_objet));
  }
  return $titre;
}
?>