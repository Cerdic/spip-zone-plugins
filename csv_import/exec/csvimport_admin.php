<?php
/*
 * CSVimport
 * Plug-in d'import csv dans les tables spip et d'export CSV des tables
 *
 * Auteur :
 * Cedric MORIN
 * notre-ville.net
 * © 2005,2009 - Distribue sous licence GNU/GPL
 *
 */

include_spip("inc/csvimport");
include_spip("inc/presentation");
include_spip("base/abstract_sql");
include_spip("base/serial");
include_spip("base/auxiliaires");
include_spip("public/interfaces"); // definition des jointures et autres

function csvimport_admin_action(){
    global $tables_principales;
    global $tables_auxiliaires;
    if (_request('modif')){
        $csvimport_tables_auth = array();
        $exportable = _request("exportable");
        if (count($exportable)){
            $titre = _request("titre");
            $statut=_request("statut");
            $operation=_request("operation");
            $field=_request("field");
            foreach(array_keys($exportable) as $table){
                $csvimport_tables_auth[$table]=array(
                    'titre'=>$titre[$table],
                    'statut'=>isset($statut[$table])?$statut[$table]:array(),
                    'operations'=>isset($operation[$table])?$operation[$table]:array(),
                    'field'=>isset($field[$table])?$field[$table]:array(),
                    'dyn_declare_aux'=>(!isset($tables_principales[$table])&&!isset($tables_auxiliaires[$table]))
                );
            }
        }
        ecrire_meta('csvimport_tables_auth',serialize($csvimport_tables_auth));
        ecrire_metas();
    }
}

function ligne_table_import($table,$desc){
    static $csvimport_tables_auth=NULL;
    $liste_statuts = array('0minirezo'=>_T('item_choix_administrateurs'), '1comite'=>_T('item_choix_redacteurs'));
    $liste_operations = array('add' => _T('csvimport:ajouter_donnees'),'replaceall' =>_T('csvimport:tout_remplacer'),'export' =>_T('csvimport:exporter'));

    if ($csvimport_tables_auth==NULL)
        $csvimport_tables_auth = csvimport_tables_auth();


    if (isset($csvimport_tables_auth[$table])){
        $exportable = true;
        $titre = $csvimport_tables_auth[$table]['titre'];
        $statuts = $csvimport_tables_auth[$table]['statut'];
        $operations = $csvimport_tables_auth[$table]['operations'];
        $fields = $csvimport_tables_auth[$table]['field'];
    }
    else{
        $exportable = false;
        $titre = "";
        $statuts = array('0minirezo');
        $operations = array('export');
        $fields = array();
    }

    $vals=array();

    // nom de la table dans mysql
    $s = "<input type='checkbox' name='exportable[$table]' value='1' id='exportable_$table'";
    $s .= ($exportable)?" checked='checked'":"";
    $s .= " /> <label for='exportable_$table'>";
    $s .= $table;
    $s .= "</label>\n";
    $vals[] = $s;

    // Libelle explicite
    $s = "<input type='text' name='titre[$table]' id='titre_$table' class='texte' value='".entites_html($titre)."' />";
    //$vals[] = $s;
    $s .= "<br />";

    // status autorises a manipuler la table
    //$s = "";
    foreach($liste_statuts as $stat=>$lib){
        $s .= "<input type='checkbox' name='statut[$table][]' value='$stat' id='statut_".$table."_".$stat."'";
        $s .= (in_array($stat,$statuts))?" checked='checked'":"";
        $s .= " />&nbsp;<label for='statut_".$table."_".$stat."'>";
        $s .= str_replace(" ","&nbsp;",$lib);
        $s .= "</label>\n\n";
        //$s .= "<br />";
    }
    $s .= "<hr />\n\n";
    //$vals[] = $s;


    // operations autorises sur la table
    //$s = "";
    foreach($liste_operations as $op=>$lib){
        $s .= "<input type='checkbox' name='operation[$table][]' value='$op' id='statut_".$table."_".$op."'";
        $s .= (in_array($op,$operations))?" checked='checked'":"";
        $s .= " />&nbsp;<label for='statut_".$table."_".$op."'>";
        $s .= str_replace(" ","&nbsp;",$lib);
        $s .= "</label>\n\n";
        //$s .= "<br />";
    }
    $s .= "<hr />\n\n";
    //$vals[] = $s;

    // champs de la table
    //$s = "";
    $s .= "<table class='csv_import' >";
    $col=0;
    foreach($desc['field'] as $field=>$type){
        if ($col==0)
            $s .= "<tr>";
        $s.="<td>";
        $s .= "<input type='checkbox' name='field[$table][]' value='$field' id='statut_".$table."_".$field."'";
        $s .= (in_array($field,$fields))?" checked='checked'":"";
        $s .= " />&nbsp;<label for='statut_".$table."_".$field."'>";
        $s .= $field;
        $s .= "</label>";
        $s.="</td>\n\n";
        $col++;
        if ($col==3){
            $s .= "</tr>\n\n";
            $col = 0;
        }
        //$s .= "<br />";
    }
    if ($col!=0)
        $s .= "</tr>\n\n";
    $s.= "</table>";
    $vals[] = $s;

    return $vals;
}

function exec_csvimport_admin(){
    global $tables_jointures;
    global $tables_principales;
    global $tables_auxiliaires;
    global $table_des_tables;
    global $table_prefix;
    $tables_defendues = array('ajax_fonc','meta','ortho_cache','ortho_dico','caches','test');

    if (!autoriser('configurer')) {
        include_spip('inc/minipres');
        minipres();
        exit;
    }

    //
    // Afficher une liste de tables importables
    //
    $icone = _DIR_PLUGIN_CSVIMPORT."img_pack/csvimport-24.png";
    $commencer_page = charger_fonction('commencer_page','inc');
    echo $commencer_page(_T("csvimport:import_csv"), "csvimport");
    echo debut_gauche('',true);

    $raccourcis = icone_horizontale(_T('csvimport:import_export_tables'), generer_url_ecrire("csvimport_tous"), $icone, "", false);
    echo bloc_des_raccourcis($raccourcis);

    echo debut_droite('',true);

    csvimport_admin_action();

    //csvimport_afficher_tables(_L("Tables declar&eacute;es pour import"));

    $retour = _request('retour');
    if (!$retour)
        $retour = generer_url_ecrire('csvimport_tous');

    $titre = _T('csvimport:administrer_tables');
    //
    // Icones retour
    //
    $milieu = '';
    $milieu .= "<div class='entete-formulaire'>";
    if($GLOBALS['spip_version_branche'][0]=="2") {
        $milieu .= icone_inline(_T('icone_retour'), $retour, $icone, "rien.gif",$GLOBALS['spip_lang_left']);
    } else {
        $milieu .= icone_verticale(_T('icone_retour'), $retour, $icone, "rien.gif",$GLOBALS['spip_lang_left']);
    }
    $milieu .= gros_titre($titre,'', false);
    $milieu .= "</div>";


    // on construit un index des tables de liens
    // pour les ajouter SI les deux tables qu'ils connectent sont sauvegardees
    $tables_for_link = array();
    foreach($tables_jointures as $table=>$liste_relations)
        if (is_array($liste_relations))
        {
            $nom = $table;
            if (!isset($tables_auxiliaires[$nom])&&!isset($tables_principales[$nom]))
                $nom = "spip_$table";
            if (isset($tables_auxiliaires[$nom])||isset($tables_principales[$nom])){
                foreach($liste_relations as $link_table){
                    if (isset($tables_auxiliaires[$link_table])/*||isset($tables_principales[$link_table])*/){
                        $tables_for_link[$link_table][] = $nom;
                    }
                    else if (isset($tables_auxiliaires["spip_$link_table"])/*||isset($tables_principales["spip_$link_table"])*/){
                        $tables_for_link["spip_$link_table"][] = $nom;
                    }
                }
            }
        }

    $res = sql_showbase();
    $liste_des_tables_spip=array();
    $liste_des_tables_autres=array();
    while ($row=sql_fetch($res)){
        $table = array_shift($row);
        // on ne retient que les tables prefixees par spip_
        // evite les melanges sur une base avec plusieurs spip installes
        if (substr($table,0,strlen($table_prefix))==$table_prefix){
            $table_abr = substr($table,strlen($table_prefix)+1);
            // option de config $GLOBALS['csvimport_tables_jointures'] = true pour gérer aussi les tables de jointures
            if ((!isset($tables_for_link["spip_$table_abr"])
                || (isset($GLOBALS['csvimport_tables_jointures'])&& $GLOBALS['csvimport_tables_jointures']))
                && !in_array($table_abr,$tables_defendues)
            ){
                $liste_des_tables_spip[]=$table;
            }
        }
        else {
            $liste_des_tables_autres[] = $table;
        }
    }

    $milieu .= "<div class='formulaire_spip'>";
    $action = generer_url_ecrire("csvimport_admin", "modif=1&retour=".urlencode($retour));

    $milieu .= "\n<form action='$action' method='post' class='formulaire_editer'><div>".form_hidden($action);
    $milieu .= "<table width='100%' cellpadding='5' cellspacing='0' border='0'>";
    $num_rows = count($liste_des_tables_spip)+count($liste_des_tables_autres);

    $ifond = 0;
    $premier = true;

    $compteur_liste = 0;
    $tableau = array();
    foreach($liste_des_tables_spip as $table) {
        $desc = sql_showtable($table);
        if (is_array($desc)){
            $ligne = ligne_table_import($table,$desc);
            $tableau[] = $ligne;
        }
    }
    foreach($liste_des_tables_autres as $table) {
        $desc = sql_showtable($table);
        if (is_array($desc)){
            $ligne = ligne_table_import($table,$desc);
            $tableau[] = $ligne;
        }
    }

    $largeurs = array('','','');
    $styles = array('arial11', 'arial1', 'arial1');
    $evt = ' style="width:100%"';
    $liste = '';
    foreach ($tableau as $t) {
        reset($largeurs);
        if ($styles) reset($styles);
        $res ='';
        while (list(, $texte) = each($t)) {
            $style = $largeur = "";
            list(, $largeur) = each($largeurs);
            if ($styles) list(, $style) = each($styles);
            if (!trim($texte)) $texte .= "&nbsp;";
            $res .= "\n<td" .
                ($largeur ? (" style='width: $largeur px;'") : '') .
                ($style ? " class='$style'" : '') .
                ">" . lignes_longues($texte) . "</td>\n\n";
        }

        $liste .=  "\n<tr class='tr_liste'$evt>$res</tr>";
    }

    $milieu .= $liste;
    $milieu .= "</table>";
    $milieu .= "<p class='boutons'>";
    $milieu .= "<input type='submit' name='Enregistrer' value='"._T('bouton_enregistrer')."' class='submit' />";
    $milieu .= "</p>";
    $milieu .= "</div></form>";
    $milieu .= "</div>";

    echo pipeline('affiche_milieu',array('args'=>array('exec'=>'csvimport_admin','table'=>$table),'data'=>$milieu));

    echo fin_gauche(), fin_page();
}

?>
