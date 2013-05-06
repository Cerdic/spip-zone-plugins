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

function exec_csvimport_telecharger(){
    $table = _request('table');
    $retour = _request('retour');

    $delim = _request('delim');
    if ($delim == 'TAB') $delim = "\t";

    if (!$retour)
        $retour = generer_url_ecrire('csvimport_tous');

    $operations = array();

    $titre = _T("csvimport:export_table",array('table'=>$table));
    $is_importable = csvimport_table_importable($table,$titre,$operations);
    if (in_array('export',$operations))
        $csvimport_export_actif = true;


    if ((!$delim)&&($csvimport_export_actif)){
        $icone = _DIR_PLUGIN_CSVIMPORT."img_pack/csvimport-24.png";
        //
        // Affichage de la page
        //
        $commencer_page = charger_fonction('commencer_page', 'inc');
        pipeline('exec_init',array('args'=>$_GET,'data'=>''));

        echo $commencer_page($titre,"csvimport");

        echo debut_gauche('',true);

        $raccourcis = icone_horizontale(_T('csvimport:administrer_tables'), generer_url_ecrire("csvimport_admin"), $icone, "", false);
        $raccourcis .= icone_horizontale(_T('csvimport:import_export_tables'), generer_url_ecrire("csvimport_tous"), $icone, "", false);

        echo bloc_des_raccourcis($raccourcis);

        echo pipeline('affiche_gauche',array('args'=>array('exec'=>'csvimport_telecharger','table'=>$table),'data'=>''));

        echo creer_colonne_droite("",true);
        echo pipeline('affiche_droite',array('args'=>array('exec'=>'csvimport_telecharger','table'=>$table),'data'=>''));
        echo debut_droite("",true);

        $milieu = '';

        $milieu .= "<div class='entete-formulaire'>";
        //
        // Icones retour
        //
        if ($retour) {
            if($GLOBALS['spip_version_branche'][0]=="2") {
                $milieu .= icone_inline(_T('icone_retour'), $retour, $icone, "rien.gif",$GLOBALS['spip_lang_left']);
            } else {
                $milieu .= icone_verticale(_T('icone_retour'), $retour, $icone, "rien.gif",$GLOBALS['spip_lang_left']);
            }
        }
        $milieu .= gros_titre($titre,'', false);
        $milieu .= "</div>";

        $milieu .= "<div class='formulaire_spip'>";
        $action = generer_url_ecrire("csvimport_telecharger","table=$table&retour=$retour");
        $milieu .= "\n<form action='$action' method='post' class='formulaire_editer'><div>".form_hidden($action);
        $milieu .= "<ul><li><label for='delim'>"._T("csvimport:export_format")."</label>";
        $milieu .= "<select name='delim' id='delim'>\n";
        $milieu .= "<option value=','>"._T("csvimport:export_classique")."</option>\n";
        $milieu .= "<option value=';'>"._T("csvimport:export_excel")."</option>\n";
        $milieu .= "<option value='TAB'>"._T("csvimport:export_tabulation")."</option>\n";
        $milieu .= "</select></li></ul>";
        $milieu .= "<p class='boutons'><input type='submit' class='submit' name='ok' value='"._T('bouton_download')."' /></p>\n";
        $milieu .= "</div></form>";
        $milieu .= "</div>";

        echo pipeline('affiche_milieu',array('args'=>array('exec'=>'csvimport_telecharger','table'=>$table),'data'=>$milieu));

        echo fin_gauche(), fin_page();
        exit;

    }

    $csvimport_tables_auth = csvimport_tables_auth();
    if ($csvimport_export_actif){
        if (isset($csvimport_tables_auth[$table]['field']))
            $tablefield=$csvimport_tables_auth[$table]['field'];
        else
            $tablefield=array_keys($tables_principales[$table]['field']);

        //
        // Telechargement du contenu de la table au format CSV
        //

        $output = csvimport_csv_ligne($tablefield,$delim);
        //$tablefield = array_flip($tablefield);

        $result = sql_select('*',$table);
        while ($row=sql_fetch($result)){
            $ligne=array();
            foreach($tablefield as $key)
                if (isset($row[$key]))
                    $ligne[]=$row[$key];
                else
                    $ligne[]="";
            $output .= csvimport_csv_ligne($ligne,$delim);
        }

        $charset = $GLOBALS['meta']['charset'];


        $filename = preg_replace(',[^-_\w]+,', '_', translitteration(textebrut(typo($titre))));

        // Excel ?
        if ($delim == ',') {

            $extension = 'csv';

        } else {

            // Extension 'csv' si delim = ';' (et pas forcément 'xls' !)
            if ($delim == ';') { $extension = 'csv'; }
            else { $extension = 'xls'; }

            # Excel n'accepte pas l'utf-8 ni les entites html... on fait quoi?
            include_spip('inc/charsets');
            $output = unicode2charset(charset2unicode($output), 'iso-8859-1');
            $charset = 'iso-8859-1';
        }

        Header("Content-Type: text/comma-separated-values; charset=$charset");
        Header("Content-Disposition: attachment; filename=$filename.$extension");
        //Header("Content-Type: text/plain; charset=$charset");
        Header("Content-Length: ".strlen($output));
        echo $output;
        exit;
    }
    else {
        include_spip('inc/minipres');
        echo minipres();
    }
}
?>
