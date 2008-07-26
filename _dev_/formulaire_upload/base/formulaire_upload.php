<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


// permettre <BOUCLE_a(AUTEURS){id_document}>
global $tables_jointures;
$tables_jointures['spip_auteurs']['id_document']= 'documents_liens';

?>