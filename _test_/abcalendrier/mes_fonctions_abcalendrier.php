<?php
/***************************************************************/
/*    Dichiarazione del campo 'evento' aggiunto alle brevi
/***************************************************************/

include ('ecrire/base/serial.php');
global $tables_principales;
$tables_principales['spip_breves']['field']['evento']= "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";

?>