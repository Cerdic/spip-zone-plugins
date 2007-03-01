<?php
function exec_lienscontenus_ajax_doc_contenu()
{
    $id_doc = intval(_request('id_doc'));
    include_spip('base/abstract_sql');
    $query = "SELECT COUNT(*) AS nb FROM spip_liens_contenus WHERE type_objet_contenu='document' AND id_objet_contenu="._q($id_doc);
    $res = spip_query($query);
    $row = spip_fetch_array($res);
    
    $retour = '<lienscontenu><contenu>'.($row['nb'] > 0 ? 'oui' : 'non').'</contenu></lienscontenu>';
    header('Content-type: text/xml');
    echo '<'.'?xml version="1.0" encoding="utf-8" ?>'."\n".$retour;
    exit;
}
?>