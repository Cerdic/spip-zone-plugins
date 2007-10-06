<?php
/*
 * langue_preferee
 * Langue preferee par l'internaute
 *
 * Auteur :
 * Nicolas Hoizey
 * modification : chryjs - exclusion de rubriques
 * © 2007 - Distribue sous licence GNU/GPL
 */

function balise_LANGUE_PREFEREE_SECTEUR_REDIRECTION($p)
{
	return calculer_balise_dynamique($p, 'LANGUE_PREFEREE_SECTEUR_REDIRECTION', array());
}

function balise_LANGUE_PREFEREE_SECTEUR_REDIRECTION_stat($args, $filtres)
{
	return $args;
}

function balise_LANGUE_PREFEREE_SECTEUR_REDIRECTION_dyn($liste_rub_exclues="")
{
    include_spip('inc/meta');

    // Recuperation des langues des secteurs
    $langues_secteurs = array();
    include_spip('base/abstract_sql');
    $query = 'SELECT DISTINCT(lang) FROM spip_rubriques WHERE id_parent=0 GROUP BY lang';
    if ($res = spip_query($query)) {
        while($row = spip_fetch_array($res)) {
        	$langues_secteurs[] = $row['lang'];
        }
    }

    // Detection de la langue preferee
    if (isset($_GET['lang']) && in_array($_GET['lang'], $langues_secteurs)) {
        // Soit passee dans l'url, auquel cas c'est un choix qu'on conserve pour la suite
        $langue_preferee = $_GET['lang'];
        include_spip('inc/cookie');
        // On pose un cookie d'un an de duree de vie
    	spip_setcookie('spip_langue_preferee', $langue_preferee, time() + 3660*24*365, chemin_cookie());
    } elseif (isset($_COOKIE['spip_langue_preferee']) && in_array($_COOKIE['spip_langue_preferee'], $langues_secteurs)) {
        // Soit deja enregistree dans un cookie
        $langue_preferee = $_COOKIE['spip_langue_preferee'];
    } else {
        // Soit indeterminee
        $langues_navigateur = getenv('HTTP_ACCEPT_LANGUAGE');
        // On supprime les taux de pertinence des langues acceptees
        $langues_navigateur = preg_replace("/;q=[.0-9]+(,)?/", "$1", $langues_navigateur);
        $langues_navigateur = explode(',', $langues_navigateur);
        // Quelles sont les langues acceptees disponibles dans les secteurs
        $langues_possibles = array_intersect($langues_navigateur, $langues_secteurs);
        if (count($langues_possibles)) {
            list(, $langue_preferee) = each($langues_possibles);
        } else {
            // fr-ca -> fr
            $langues_navigateur_reduites = array();
            foreach($langues_navigateur as $langue) {
            	$langue_reduite = substr($langue, 0, 2);
                if (!in_array($langue_reduite, $langues_navigateur_reduites)) {
                	$langues_navigateur_reduites[] = $langue_reduite;
                }
            }
            // Quelles sont les langues acceptees reduites disponibles dans les secteurs
            $langues_reduites_possibles = array_intersect($langues_navigateur_reduites, $langues_secteurs);
            if (count($langues_reduites_possibles)) {
                list(, $langue_preferee) = each($langues_reduites_possibles);
            } elseif (in_array(lire_meta('langue_site'), $langues_secteurs)) {
                // Quelle est alors la langue par defaut du site
                $langue_preferee = lire_meta('langue_site');
            } else {
                // Tant pis, on prend le premier secteur qui vient...
                list(, $langue_preferee) = each($langues_secteurs);
            }
        }
    }

    // On recupere l'id du premier secteur trouve correspondant a la langue preferee (tant pis s'il y en a plusieurs)
    if (!empty($liste_rub_exclues)) {
       $query='SELECT id_rubrique FROM spip_rubriques WHERE id_parent=0 AND lang='._q($langue_preferee).' AND id_rubrique NOT IN ('.$liste_rub_exclues.') LIMIT 0,1';
    }
    else {
       $query = 'SELECT id_rubrique FROM spip_rubriques WHERE id_parent=0 AND lang='._q($langue_preferee).' LIMIT 0,1';
    }
    $res = spip_query($query);
    if ($row = spip_fetch_array($res)) {
        $id_rubrique = $row['id_rubrique'];
	if (!function_exists('generer_url_rubrique')) { include_spip('urls/'.$GLOBALS['type_urls']); }
        $url_rubrique = generer_url_rubrique($id_rubrique);
        spip_log('Redirection vers '.$url_rubrique);
        header('Location: '.$url_rubrique);
        exit;
    }
}
?>
