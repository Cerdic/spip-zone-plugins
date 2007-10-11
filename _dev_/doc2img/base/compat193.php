<?php

/* si spip est en 192, on definit les nouvelles fonctions sql défini en 193 */

/* sql_showtable (193) est en fait spip_abstract_showtable(192) */ 
if(!function_exists('sql_showtable')) {
  function sql_showtable($table, $serveur='', $table_spip = false) {
  	return spip_abstract_showtable($table, $serveur, $table_spip);
  }
}
/* sql_fetch (193) est en fait spip_fetch_array(192) */
if(!function_exists('sql_fetch')) {
  function sql_fetch($res, $t=SPIP_ASSOC) {
  	return spip_fetch_array($res, $t);
  }
}

?>
