<?php
  // ajouter aux path des squelettes le sous-dossier de /plugins/gallery2/squelettes
  // qui stocke le squel gallerie.html choisi via CFG
    $cfg = @unserialize($GLOBALS['meta']['g2']);
    if (isset($cfg['choix_squelette_integration']) AND $cfg['choix_squelette_integration'] != '')
        $rep_squel = $cfg['choix_squelette_integration'];
    else $rep_squel = 'dist';
    $GLOBALS['dossier_squelettes'] .= ':'._DIR_PLUGIN_G2.'squelettes/'.$rep_squel;
    //echo '<br>chem squl: '.$GLOBALS['dossier_squelettes'];
?>