<?php 

// Compatibilité de Patrice  VANNEUFVILLE
if (version_compare($GLOBALS['spip_version_code'],'1.9300','>=')) @define('_SPIP19300', 1);
if (version_compare($GLOBALS['spip_version_code'],'1.9200','>=')) @define('_SPIP19200', 1);
else @define('_SPIP19100', 1);

// Desactive typographie 
function typographie_fr ($x) {return $x;};
function typographie_en ($x) {return $x;};
  
?>
