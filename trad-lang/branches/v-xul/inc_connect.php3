<?php
if (defined("_ECRIRE_INC_CONNECT")) return;
define("_ECRIRE_INC_CONNECT", "1");
$GLOBALS['spip_connect_version'] = 0.1;
include_ecrire('inc_db_mysql.php3');
@spip_connect_db('localhost','','spip','glouglou','spip');
$GLOBALS['db_ok'] = !!@spip_num_rows(@spip_query_db('SELECT COUNT(*) FROM spip_meta'));
?>