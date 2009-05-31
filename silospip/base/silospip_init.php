<?php 

/*********************************************************\
 *  base/silospip_init.php                               *
 *  Daniel Viñar Ulriksen <dani@rexo.net> 2009           *
 *                                                       *
\*********************************************************/

if(isset($GLOBALS['plugins']['silospip'])) {
	include_spip('inc/silospip_api_globales');
	include_spip('inc/silospip_api');
} else {
	include_once( dirname(__FILE__).'/../inc/silospip_api_globales.php');
	include_once( dirname(__FILE__).'/../inc/silospip_api.php');
}


function silospip_install ($action) {

	silospip_log("silospip_install()", _SILOSPIP_LOG_DEBUG);

        switch($action) {
                case 'test':
                        // si renvoie true, c'est que la base est a jour, inutile de re-installer
                        // la valise plugin "effacer tout" apparait.
                        // si renvoie false, SPIP revient avec $action = 'install' (une seule fois)
                        $silospip_version = $GLOBALS['meta']['silospip_version'];
                        $result = (
                                $silospip_version
                                && ($silospip_version >= silospip_real_version_get(_SILOSPIP_PREFIX))
                                && sql_showtable("spip_silosites",true)
                                );
                        silospip_log("TEST: ".($result ? "OK" : "NO"), _SILOSPIP_LOG_DEBUG);
                        return($result);
                        break;
                case 'install':
                        silospip_log("INSTALL: on commence", _SILOSPIP_LOG_DEBUG);
                        if(!$GLOBALS['meta']['silospip_version']) {
                                $result = silospip_base_creer();
                                $str_log = "create";
                        }
/****
                        else {
                                // logiquement, ne devrait pas passer par la (upgrade assure par mes_options)
                                include_spip('base/silospip_upgrade');
                                $result = silospip_upgrade();
                                $str_log = "upgrade";
                        }
                        $result = (
                                $result
                                && silospip_initialise_spip_metas_silospip()
                                && silospip_activer_inscription_visiteurs()
                                );
****/
                        silospip_log("avant metas premier res: ".($result ? "Ok" : "ERR"), _SILOSPIP_LOG_DEBUG);
			$res_metas = silospip_initialise_spip_metas_silospip();
                        silospip_log("metas execute: ".($res_metas ? "Ok" : "ERR"), _SILOSPIP_LOG_DEBUG);
			$res_visit = silospip_activer_inscription_visiteurs();
                        silospip_log("visit execute: ".($res_visit ? "Ok" : "ERR"), _SILOSPIP_LOG_DEBUG);
                        $result = ( $result && $res_metas && $res_visit);
                        $str_log = "INSTALL: $str_log " . silospip_str_ok_error($result);
                        if(!$result) {
                                // nota: SPIP ne filtre pas le resultat. Si retour en erreur,
                                // la case a cocher du plugin sera quand meme cochee
                                $str_log .= ": PLEASE REINSTALL PLUGIN";
                        }
                        else {
                                echo(_T('silospip:_aide_install'
                                        , array('url_config' => generer_url_ecrire(_SILOSPIP_EXEC_CONFIGURE))
                                        ));
                        }
                        silospip_log($str_log);
                        return($result);
                        break;
                case 'uninstall':
                        // est appelle lorsque "Effacer tout" dans exec=admin_plugin
                        $result = silospip_vider_tables();
                        silospip_log("UNINSTALL: " . silospip_str_ok_error($result));
                        return($result);
                        break;
                default:
                        break;
        }
}

function silospip_base_creer () {

        silospip_log("silospip_base_creer(): on commence ", _SILOSPIP_LOG_DEBUG);
        global $tables_principales;
        
        // demande a SPIP de creer les tables (base/create.php)
        include_spip('base/create');
        include_spip('base/abstract_sql');
        include_spip('base/db_mysql');
        include_spip('base/silospip_tables');
        creer_base();
        silospip_log("silospip_base_creer() : creer_base executee", _SILOSPIP_LOG_DEBUG);
        $descauteurs = sql_showtable('spip_auteurs_elargis',true);
/*
        if(!isset($descauteurs['field']['spip_silo_format'])){
                // si la table spip_auteurs_elargis existe déjà
                sql_alter("TABLE spip_auteurs_elargis ADD `spip_silo_format` VARCHAR(8) DEFAULT 'non' NOT NULL");
        }
*/
        silospip_log("INSTALL: database creation",_SILOSPIP_LOG_DEBUG);
        $silospip_base_version = silospip_real_version_base_get(_SILOSPIP_PREFIX);
        ecrire_meta('silospip_base_version', $silospip_base_version);
        // ecrire_metas();
        
        $silospip_base_version = $GLOBALS['meta']['silospip_base_version'];
        return($silospip_base_version);
}

function silospip_initialise_spip_metas_silospip ($reinstall = false) {

	silospip_log("init_meta: on commence", _SILOSPIP_LOG_DEBUG);

        if(!isset($GLOBALS['meta'][_SILOSPIP_META_PREFERENCES])) {
                $GLOBALS['meta'][_SILOSPIP_META_PREFERENCES] = "";
        }

        //  valeurs par defaut a l'installation
        $silospip_spip_metas = array(
                'silospip_domaine' => _SILOSPIP_DOMAINE
                , 'silospip_charset' => _SILOSPIP_CHARSET
                , 'silospip_version' => silospip_real_version_get(_SILOSPIP_PREFIX)
        );
	silospip_log("init_meta: ".var_export($silospip_spip_metas,TRUE), _SILOSPIP_LOG_DEBUG);
        foreach($silospip_spip_metas as $key => $value) {
                if($reinstall 
                        || !isset($GLOBALS['meta'][$key])
                        || ($GLOBALS['meta'][$key] != $value)
                ) {
                        ecrire_meta($key, $value);
                }
        }
	ecrire_config('silospip_domaines/',array(1 => _SILOSPIP_DOMAINE));
	ecrire_config('silospip_prefere/','1') ;
        // ecrire_metas();
        
        return(true);
}

function silospip_activer_inscription_visiteurs () {
        $accepter_visiteurs = $GLOBALS['meta']['accepter_visiteurs'];
        if($accepter_visiteurs != 'oui') {
                $accepter_visiteurs = 'oui';
                ecrire_meta("accepter_visiteurs", $accepter_visiteurs);
                // spiplistes_ecrire_metas();
                // echo "<br />"._T('spiplistes:autorisation_inscription');
                 silospip_log("ACTIVER accepter visiteur");
        }
        return(true);
}


function silospip_vider_tables () {

        include_spip('base/abstract_sql');
        
        // ne supprime pas la table spip_auteurs_elargis (utilisee par inscription2, echoppe, ... ? )
        $sql_tables = "spip_silosites, spip_auteurs_sites";
        
        silospip_log("DROP TABLES ".$sql_tables);
        sql_drop_table($sql_tables, true);
        
        // effacer les metas (prefs, etc.)
        $sql_silospip_metas = array(
                'silospip_version'
                , 'silospip_base_version'
                );
        silospip_log("DELETE meta: " . implode(", ", $sql_silospip_metas));
        sql_delete('spip_meta', "nom=".implode(" OR nom=", array_map("sql_quote", $sql_silospip_metas)));

        // recharge les metas en cache 
        // silospip_ecrire_metas();
        
        return(true);
} 


?>
