<?php
/* inscription2 fait différemment que  spip-listes, dont on s'est inspiré ceci est un brouillon non utilise */
function inscription2_declarer_tables_interfaces($interface){
        $interface['tables_jointures']['spip_auteurs'][] = 'auteurs_elargis';
        //-- Table des tables ----------------------------------------------------
        $interface['table_des_tables']['auteurs_elargis']='auteurs_elargis';

        return $interface;
}

?>
