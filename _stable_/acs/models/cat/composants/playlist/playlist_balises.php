<?php

function joli_titre($titre) {
  $titre=basename($titre);
  $titre=ereg_replace('.mp3','',$titre);
  $titre=ereg_replace('^ ','',$titre);
  $titre = eregi_replace("_"," ", $titre );
  $titre = eregi_replace("'"," ",$titre );
  return $titre ;
}
?>