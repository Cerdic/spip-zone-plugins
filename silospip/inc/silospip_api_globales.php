<?php

// inc/spiplistes_api_globales.php

/******************************************************************************************/
/* Silo-SPIP est un systeme de creation de sites SPIP en "libre service"                  */
/* Copyright (C) 2009 Daniel Viñar Ulriksen  dani<at>boliviaos.net                        */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
// Certains hebergeurs ont desactive l'acces a syslog (free,...)
// Recreer les constantes pour trier les journaux
if(!defined("LOG_WARNING")) 
        define("LOG_WARNING", 4);
if(!defined("LOG_DEBUG")) 
        define("LOG_DEBUG", 7);

function silospip_log ($texte, $level = LOG_WARNING) {

        static $lan, $syslog; 
        if($lan === null) {
                $lan = silospip_server_rezo_local();
                $syslog = (($s = silospip_pref_lire('opt_console_syslog')) && ($s == 'oui'));
        }
        if($lan) {
                if($syslog) {
                        $tag = "_";
                        if(empty($tag)) { 
                                $tag = basename ($_SERVER['PHP_SELF']); 
                        }
                        else if($level == LOG_DEBUG) {
                                $tag = "DEBUG: ".$tag; 
                        }
                        return(
                                openlog ($tag, LOG_PID | LOG_CONS, LOG_USER) 
                                        && syslog ($level, (string)$texte) 
                                        &&      closelog()
                        );
                }
                else {
                        spip_log($texte, _SILOSPIP_PREFIX);
                }
                
        }
        else if($level <= LOG_WARNING) {
                // Taille du log SPIP trop courte en 192
                // Ne pas envoyer si DEBUG sinon tronque sans cesse
                // En SPIP 193, modifier globale $taille_des_logs pour la rotation
                spip_log($texte, _SILOSPIP_PREFIX);
        }

        return(true);
}

function silospip_server_rezo_local () {
        static $lan;
        if($lan === null) {
                $lan = preg_match('/^(10|172\.16|192\.168|127\.0)/', $_SERVER['SERVER_ADDR']);
        }
        return($lan);
}

function silospip_pref_lire ($key) { 
        return(silospip_lire_key_in_serialized_meta($key, _SILOSPIP_META_PREFERENCES));
}


/*
 * lecture dans les metas, format serialise
 * @return 
 * @param $meta_name Object
 */
function silospip_lire_serialized_meta ($meta_name) {
        if(isset($GLOBALS['meta'][$meta_name])) {
                if(!empty($GLOBALS['meta'][$meta_name])) {
                        return(unserialize($GLOBALS['meta'][$meta_name]));
                }
                else silospip_log("erreur sur meta $meta_name (vide)", _SILOSPIP_LOG_DEBUG);
        }
        return(false);
}

/*
 * lecture d'une cle dans la meta serialisee
 * @return 
 * @param $key Object
 * @param $meta_name Object
 */
function silospip_lire_key_in_serialized_meta ($key, $meta_name) {
        $result = false;
        $s_meta = silospip_lire_serialized_meta($meta_name);
        if($s_meta && isset($s_meta[$key])) {
                $result = $s_meta[$key];
        } 
        return($result);
}


/*
 * @return la version du fichier plugin.xml 
 */
function silospip_real_version_get ($prefix) {
        static $r;
        if($r === null) {
                $r = silospip_real_tag_get($prefix, 'version');
        }
        return ($r);
}

/*
 * renvoie la version_base du fichier plugin.xml
 */
function silospip_real_version_base_get ($prefix) {
        $r = silospip_real_tag_get($prefix, 'version_base');
        return ($r);
}

function silospip_current_version_get ($prefix) {
        global $meta; 
        return $meta[$prefix."_version"];
}

function silospip_real_tag_get ($prefix, $s) {
        include_spip("inc/plugin");
        $dir = silospip_get_meta_dir($prefix);
        $f = _DIR_PLUGINS.$dir."/"._FILE_PLUGIN_CONFIG;
        if(is_readable($f) && ($c = file_get_contents($f))) {
                $p = array("/<!--(.*?)-->/is","/<\/".$s.">.*/s","/.*<".$s.">/s");
                $r = array("","","");
                $r = preg_replace($p, $r, $c);
        }
        return(!empty($r) ? $r : false);
}

/*
 * renvoie les infos du plugin contenues dans les metas
 * qui contient 'dir' et 'version'
 */
function silospip_get_meta_infos ($prefix) {
        if(isset($GLOBALS['meta']['plugin'])) {
                $result = unserialize($GLOBALS['meta']['plugin']);
                $prefix = strtoupper($prefix);
                if(isset($result[$prefix])) {
                        return($result[$prefix]);
                }
        }
        return(false);
}

/*
 * renvoie le dir du plugin present dans les metas
 */
function silospip_get_meta_dir($prefix) {
        $result = false;
        $info = silospip_get_meta_infos($prefix);
        if(isset($info['dir'])) {
                $result = $info['dir'];
        }
        return($result);
}

?>
