<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

function acs_post_edition($pipe) {
  $auteur = $GLOBALS['auteur_session'];

  // On ne logue que les admins, le suivi des révisions propre à spip suffit pour les rédacteurs.
  if ($auteur['statut'] != '0minirezo') return false;

  $args = $pipe['args'];
  $table = $args['table'];
  $id_objet = $args['id_objet'];
  $data = $pipe['data'];

  if(is_array($data)) {
    if (isset($data['titre']) || ($table='spip_auteurs')) // articles ou auteurs
      $data = array('statut' => 'modif');
    if (isset($data['date'])) // On l'a déjà ds le log
      unset($data['date']);
    $data = '['.implode(', ', array_keys($data)).']='.implode(', ', $data);
  }

  // Ce format de log est relu dans acs_suivi_admins
  spip_log($auteur['id_auteur'].'|'.$table.'|'.$id_objet.'|'.$data, 'admins');
}
?>
