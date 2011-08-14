<?php


if (!function_exists("objet_info")) {
    /**
     * Renvoyer l'info d'un objet
     * telles que definies dans declarer_tables_objets_sql
     *
     * @param string $objet
     * @param string $info
     * @return string
     */
    function objet_info($objet,$info){
	    $table = table_objet_sql($objet);
	    $infos = lister_tables_objets_sql($table);
	    return (isset($infos[$info])?$infos[$info]:'');
    }
}
?>
