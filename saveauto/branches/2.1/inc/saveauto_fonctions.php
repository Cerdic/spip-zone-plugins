<?php
/**
 * saveauto : plugin de sauvegarde automatique de la base de donnees de SPIP
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
 *
 **/

function saveauto_trouve_table($table, $tableau_tables) {
    $trouve = false;
    foreach ($tableau_tables as $t)	{
        if (strstr($table, $t)) {
            $trouve = true;
            break;
        }
    }
    return $trouve;
}

function saveauto_mysql_version() {
   $result = sql_query('SELECT VERSION() AS version');
   if ($result != FALSE && sql_count($result) > 0) {
      $row = mysql_fetch_array($result);
      $match = explode('.', $row['version']);
   }
   else {
      $result = sql_query('SHOW VARIABLES LIKE \'version\'');
      if ($result != FALSE && sql_count($result) > 0) {
         $row = mysql_fetch_row($result);
         $match = explode('.', $row[1]);
      }
   }

   if (!isset($match) || !isset($match[0])) $match[0] = 3;
   if (!isset($match[1])) $match[1] = 21;
   if (!isset($match[2])) $match[2] = 0;
   return $match[0] . "." . $match[1] . "." . $match[2];
}

?>