<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$gouvernance = array(
  'all' => 'Tout le monde a le droit de voter',
  'ide' => 'Seuls les visiteurs enregistrés, les rédacteurs, et les administrateurs peuvent voter',
  'aut' => 'Seuls les rédacteurs et les administrateurs peuvent voter',
  'adm' => 'Seuls les administrateurs peuvent voter'
);

$pnp = unserialize($GLOBALS['meta']['notation']);

$GLOBALS[$GLOBALS['idx_lang']] = array(

'gouvernance' => $gouvernance[$pnp['acces']],

'articles_non_publies' => 'Les articles proposés ne sont publiés que lorsque leur note dépasse <b>'.$GLOBALS['meta']['acsDemocratieSeuilPublic'].'</b>.<br />Voici les articles les plus proches de ce seuil et les derniers articles proposés à la publication: évaluez-les pour décider s\'ils doivent être publiés.',

'mal_notes' => 'Articles mal notés',
'pas_notes' => 'Articles pas encore évalués'
);
?>
