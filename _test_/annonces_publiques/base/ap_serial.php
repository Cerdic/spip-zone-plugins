<?php

include_spip('base/serial');

//ajout du champ lieu
global  $tables_principales;
$tables_principales['spip_messages']['field']['lieu'] = "text DEFAULT '' NOT NULL";


?>