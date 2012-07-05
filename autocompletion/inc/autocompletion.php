<?php

/*  Charger un SPIP minimum => Pour utiliser les foncitons spip (sql_, include_spip(), etc.)
  ----------------------------------------------- */
if (!defined('_ECRIRE_INC_VERSION')) {
    // recherche du loader SPIP.
    $deep = 2;
    $lanceur = 'ecrire/inc_version.php';
    $include = '../../' . $lanceur;

    while (!defined('_ECRIRE_INC_VERSION') && $deep++ < 6) {
        // attention a pas descendre trop loin tout de meme ! 
        // plugins/zone/stable/nom/version/tests/ maximum cherche
        $include = '../' . $include;
        if (file_exists($include)) {
            chdir(dirname(dirname($include)));
            require $lanceur;
        }
    }
}
if (!defined('_ECRIRE_INC_VERSION')) {
    return 'SPIP NON CHARGE';
}

include_spip('base/abstract_sql');

$ville      = (_request('ville'))?      trim(_request('ville'))      : '' ;
$cp         = (_request('codePostal'))? trim(_request('codePostal')) : '' ;
$cpRef      = (_request('cpRef'))?      trim(_request('cpRef'))      : '' ;
$maxRows    = (_request('maxRows'))?    trim(_request('maxRows'))    : '' ;
$delim      = ",- "; // Les differents separateurs possible
$message    = '';
$infos      = array(); 
 
if (!empty($cp) || !empty($ville)) {

    $liste   = array();

    $where   = '';
    $groupby = array();
    $orderby = array(); 
    
    $champs  = array('code_postal CodePostal', 'lib_commune Ville', 'latitude Latitude', 'longitude Longitude');
    $from    = array('spip_communes');
    
    
    if($ville){
        // On coupe la chaine en segments
        $tok = strtok($ville, $delim);        
        // Boucle pour rechercher sur chaque segments
        while ($tok !== false) {
            // Au minimum 2 characters
            if(strlen($tok) > 1){
                $whereVille[] = "lib_commune LIKE '%".trim($tok)."%'";
            }
            $tok = strtok($delim);
        }

        if($cpRef){
            $whereVille[]   = "code_postal LIKE '".sql_quote($cpRef)."%'";
            $whereCpRef[]   = "code_postal LIKE '".sql_quote($cpRef)."%'";
            $orderby[]      = "CASE WHEN "."(". join("\n\tAND ", $whereVille) .") "." THEN 1 ELSE 2 END";
            $message       .= str_replace("%cp", $cpRef, _T('autocompletion:message_pas_resultat_commune_cp') ) ;
        }
        else{
            $orderby[] = "CASE WHEN "."(". "lib_commune LIKE '".sql_quote($ville)."%'" .") "." THEN 1 ELSE 2 END";
        }
        // Ce champ 'type résult' permet de controler s'il y a un résultat en correspondance à la recherche
        $champs[] =  "(". "CASE WHEN "."(". join("\n\tAND ", $whereVille) .") "." THEN 1 ELSE 2 END" .") as type_result";
        
        $where .= "(". join("\n\tAND ", $whereVille) .") ";
        $where .= (!empty($whereCpRef))? "OR (". join("\n\tAND ", $whereCpRef) .") " : '';
    
        $orderby[] = "lib_commune, code_postal";
    }
    
    if($cp){
        $where[] = "code_postal LIKE '".sql_quote($cp)."%'";
        $orderby[] = "code_postal, lib_commune";
        $champs[] =  "(". "CASE WHEN "."(". "code_postal LIKE '".sql_quote($cp)."%'" .") "." THEN 1 ELSE 2 END" .") as type_result";
        $message .= _T('autocompletion:message_pas_resultat_cp');
    }
    
    $limit   = (!empty($maxRows))? $maxRows : '';
    
    if ($liste_des_communes = sql_select($champs, $from, $where, $groupby, $orderby, $limit)) {
        $resultat = false;
        while( $row=sql_fetch($liste_des_communes) ){
            if( $resultat == false && $row['type_result'] == '1') $resultat = true ;
            $liste[] = $row;
        }
    }
    if(!empty($message)) $infos [] = array("message" => $message);
    if($resultat === true) echo json_encode($liste);
    else echo json_encode(array_merge ($infos, $liste));
}
