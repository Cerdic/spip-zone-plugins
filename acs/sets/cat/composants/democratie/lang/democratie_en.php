<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$gouvernance = array(
  'all' => 'Everybody vote',
  'ide' => 'Only registered members, authors, and administrators vote',
  'aut' => 'Only authors and administrators vote',
  'adm' => 'Only administrators vote'
);

$pnp = unserialize($GLOBALS['meta']['notation']);

$GLOBALS[$GLOBALS['idx_lang']] = array(

'gouvernance' => $gouvernance[$pnp['acces']],

'articles_non_publies' => 'Articles are published only when rated more than <b>'.$GLOBALS['meta']['acsDemocratieSeuilPublic'].
													'</b>.<br />Here are articles near this level and last proposed articles: evaluate its to make the publication decision.',

'mal_notes' => 'Low rated articles',
'pas_notes' => 'Unrated articles'
);
?>
