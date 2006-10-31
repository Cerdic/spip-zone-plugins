<?php
// pour les recherches de classes "widget"
define('_PREG_WIDGET', ',widget\b[^<>\'"]+\b((\w+)-(\w+)-(\d+))\b,');

// retourne vrai si l'utilisateur courant a le droit de modifier un champ
// de l'objet du type et d'id donnes

function inc_autoriser_modifs_dist($type, $champ, $id) {
    global $connect_id_auteur, $connect_statut;
    $connect_id_auteur = intval($GLOBALS['auteur_session']['id_auteur']);
    $connect_statut = $GLOBALS['auteur_session']['statut'];
    include_spip('inc/auth');
    auth_rubrique($GLOBALS['auteur_session']['id_auteur'], $GLOBALS['auteur_session']['statut']);

    switch($type) {
        case 'article':
            return acces_article($id);

        case 'rubrique':
            return acces_rubrique($id);

        case 'breve':
            $s = spip_query("SELECT id_rubrique FROM spip_breves WHERE id_breve=$id");
            $t = spip_fetch_array($s);
            return acces_article($t['id_rubrique']);

        default:
            echo "pas implemente";
            return false;
    }
}

?>
