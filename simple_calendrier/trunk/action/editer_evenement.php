<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/* 	On trouve ici les fonctions 'action' appelees par le formulaire		*/
/*	'editer_evenement' : insertion d'un nouvel evenement, revision 		*/
/* 	d'un evenement existant...						*/

if (!defined("_ECRIRE_INC_VERSION")) return;

// Edition ? Revision ?
function action_editer_evenement() {

    $securiser_action = charger_fonction('securiser_action', 'inc');

    // Test si autorisation pour les actions d'edition
    $arg = $securiser_action();

    // bloc 'statut' depuis evenement_voir
    if ($id_evenement = intval($arg)) { 	
        // Si cet evenement possede un 'id', alors il n'est pas nouveau
        // Effectuons donc une revision plutot qu'une edition.
        revisions_evenements($id_evenement);
    }

    // Envoi depuis le formulaire de creation d'un evenement
    else if ($arg == 'oui') {
        $id_evenement = insert_evenement();
        if ($id_evenement) revisions_evenements($id_evenement);
    }
    // Erreur
    else{
        // Si nous sommes dans aucun des cas precedents, alors on  a un probleme : renvoyons une erreur.
        include_spip('inc/headers');
        redirige_url_ecrire();
    }

    // Redirection suite au changement de statut
    if (_request('redirect')) {
        $redirect = parametre_url(urldecode(_request('redirect')), 'id_evenement', $id_evenement, '&');
        include_spip('inc/headers');
        redirige_par_entete($redirect);
    }
    else 
        // Sinon on se contente de renvoyer l'id de l'objet
        // (Utile par exemple pour une creation, ou la redirection est geree en amont)
        return array($id_evenement,'');

}

// Cette fonction ne sert en fait qu'a ajouter une nouvelle ligne dans la table, 
// elle y insert juste la date du jour, puis retourne 
// l'id de la ligne creee. Le reste la fonction 
// revision_evenement s'en occupe.
function insert_evenement() {

    spip_log("Création d'un nouvel evenement dans la base", "simplecal");
    return sql_insertq("spip_evenements", array('date' => date('Y-m-d')));

}


// Enregistre une revision d'evenement
// $c est un contenu (par defaut on prend le contenu via _request())
function revisions_evenements($id_evenement, $c=false) {

    // ** Champs normaux **
    if ($c === false) {
        // Pour contenir les nouvelles valeurs des differents champs
        $c = array();
        
        $les_champs = array('titre', 'date_debut', 'date_fin', 'lieu', 'descriptif', 'texte', 'statut');
        foreach ($les_champs as $champ) {
            $a = _request($champ);
            if ($a !== null) {
                $c[$champ] = $a;
            }
        }
    }

    // Si l'evenement est publie, invalider les caches et demander sa reindexation
    $t = sql_getfetsel("statut", "spip_evenements", "id_evenement=$id_evenement");
    if ($t == 'publie') {
        $invalideur = "id='id_evenement/$id_evenement'";
        $indexation = true;
    }

    include_spip('inc/modifier');
    modifier_contenu('evenement', $id_evenement,
        array(
            'nonvide' => array('titre' => _T('info_sans_titre')),
            'invalideur' => $invalideur,
            'indexation' => $indexation
        ),
        $c);


    // Un cas special : changer le statut ? 
    // On recupere le statut courant
    $row = sql_fetsel("statut", "spip_evenements", "id_evenement=$id_evenement");
    $statut_ancien = $statut = $row['statut'];
    // Si un nouveau statut est demande, ET qu'il est different de l'actuel, 
    if (_request('statut', $c) AND _request('statut', $c) != $statut) {
        $statut = $champs['statut'] = _request('statut', $c);
        
        // changement de la date de publication
        if ($champs['statut'] == 'publie') {
            $date_now = date('Y-m-d H:i:s');
            $champs['date'] = $date_now;
        }        
    }

// ** Rendre effective la revision **
    // Si le tableau contenant les nouvelles valeurs est vide (rien a changer),
    // alors c'est termine !
    if (!$champs) return;

    // Si l'etape precedente est passee, alors on a des choses a faire.
    // On demande simplement une mise a jour de la table avec les nouvelles valeurs ($champs)
    sql_updateq('spip_evenements', $champs, "id_evenement=$id_evenement");

// ** Post-modifications **
    // Invalider les caches
    include_spip('inc/invalideur');
    suivre_invalideur("id='id_evenement/$id_evenement'");

}


?>
