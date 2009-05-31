<?php
include_spip('base/abstract_sql');

function exec_lienscontenus_ajax_mot_contenu()
{
  $id_mot = intval(_request('id_mot'));
  $nb = sql_countsel("spip_liens_contenus", "type_objet_contenu='mot' AND id_objet_contenu="._q($id_mot));
  $retour = '<lienscontenu><contenu>'.($nb > 0 ? 'oui' : 'non').'</contenu></lienscontenu>';
  header('Content-type: text/xml');
  echo '<'.'?xml version="1.0" encoding="utf-8" ?>'."\n".$retour;
  exit;
}
?>