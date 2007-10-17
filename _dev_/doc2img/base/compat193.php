<?php
/*! \file compat193.php
 *  \brief Compatibilité ascendante de 192 à 193
 *         
 *  On utilise la technique de realt avec compat193, mais dans l'autre sens. Si spip est en 192, on definit les nouvelles fonctions sql défini en 193.
 */

/*! \fn sql_showtable
 *  \brief surcharge de sql_showtable  
 *
 *  En 192 cette fonctionne s'appelle abstract_showtable
 *  
 *  \param $table
 *  \param $serveur
 *  \param $table_spip      
 */     
if(!function_exists('sql_showtable')) {
  function sql_showtable($table, $serveur='', $table_spip = false) {
  	return spip_abstract_showtable($table, $serveur, $table_spip);
  }
}

/*! \fn sql_fetch
 *  \brief surcharge de sql_fetch
 *    
 *  En 192 cette fonction s'appelle spip_fetch_array
 *  
 *  \param $res
 *  \param $t    
 */  
if(!function_exists('sql_fetch')) {
  function sql_fetch($res, $t=SPIP_ASSOC) {
  	return spip_fetch_array($res, $t);
  }
}

?>
