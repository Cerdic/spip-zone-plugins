<?php

chdir('/var/local/sphinx/');
define('_LOCK', 'data/lock');
# 0. s'il y a un lock, on ne bouge pas
if (@file_exists(_LOCK)) die("locked\n"); else touch(_LOCK);

# config (yaml)
$sources = array (
  'forums' => true,
  'articles' => true,
  'articles2' => true,
);

# connecter a la base
mysql_connect('localhost', 'root', 'root');

# 1. voir s'il y a du nouveau dans les sources
# (on compare la date du contenu a la date de son index)
$new = array();
foreach ($sources as $source => $cfg) {
  if ($cfg) {
    $last = @filemtime('data/last.'.$source);
    $up = update_time($source, $cfg);
    if ($up > $last AND time() > $up + 600)
     # attendre au moins 10 minutes avant de reindexer en boucle
      $new[] = $source;
    else
      echo "$source inchangee (modifie il y a ".(time()-$up)."s, indexe ".($last-$up)."s plus tard)\n";

    # creer le dictionnaire s'il n'existe pas ou s'il date
    $dict = 'data/'.$source.'.dict.txt';
    if (@filemtime($dict) < $last - 7*24*3600) {
      $cmd = "indexer --buildstops $dict 100000 --buildfreqs $source -c sphinx.conf";
      echo `$cmd`;
    }
  }
}

# 2. indexer ce qui contient une nouvelle source
if ($new) {
  foreach($new as $source) {
    echo "Indexation de $source\n";
    touch ('data/last.'.$source);
  }
  $cmd = '/usr/local/bin/indexer -c sphinx.conf --rotate '
    .join(' ', $new);
  echo `$cmd`;
  #sleep(60);

  # merge pour l'index articlesall
  if (false)  #### desactivons car pas utile, on peut select from source1,source2,source3
  foreach($new as $source) {
    if (in_array($source, array('articles','articles2'))) {
      $cmd = '/usr/local/bin/indexer -c sphinx.conf --merge diploall '
        .$source .' --rotate';
      echo `$cmd`;
    }
  }
}




# supprimer le lock
unlink(_LOCK);





function update_time($source, $cfg) {

  switch($source) {
    case 'forums':
      mysql_select_db('spip');
      if ($s = mysql_query('SELECT date_heure FROM spip_forum ORDER BY id_forum DESC LIMIT 0,1')
      AND $t = mysql_fetch_row($s))
        $up = strtotime($t[0]);
      break;
    case 'articles':
      mysql_select_db('spip');
      if ($s = mysql_query('SELECT MAX(maj) as date FROM spip_articles')
      AND $t = mysql_fetch_row($s))
        $up = strtotime($t[0]);
      break;
    case 'articles2':
      mysql_select_db('spip2');
      if ($s = mysql_query('SELECT MAX(maj) as date FROM spip_articles')
      AND $t = mysql_fetch_row($s))
        $up = strtotime($t[0]);
      break;
    default:
      $up = 0; # si pas prevu, pas d'indexation auto
  }

  return $up;
}
