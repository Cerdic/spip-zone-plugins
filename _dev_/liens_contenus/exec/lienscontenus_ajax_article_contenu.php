<?php
function exec_lienscontenus_ajax_article_contenu()
{
    $id_article = intval(_request('id_article'));
    include_spip('base/abstract_sql');
    $query = "SELECT COUNT(*) AS nb FROM spip_liens_contenus WHERE type_objet_contenu='article' AND id_objet_contenu="._q($id_article);
    $res = spip_query($query);
    $row = spip_fetch_array($res);
    
    $retour = '<lienscontenu><contenu>'.($row['nb'] > 0 ? 'oui' : 'non').'</contenu></lienscontenu>';
    header('Content-type: text/xml');
    echo '<'.'?xml version="1.0" encoding="utf-8" ?>'."\n".$retour;
    exit;
}
?>