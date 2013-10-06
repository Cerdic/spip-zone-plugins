<?php
if (!function_exists('lire_config')) {
function lire_config($cfg='', $def=null, $unserialize=true) {
        // lire le stockage sous la forme /table/valeur
        // ou valeur qui est en fait implicitement /meta/valeur
        // ou casier/valeur qui est en fait implicitement /meta/casier/valeur

        // traiter en priorite le cas simple et frequent
        // de lecture direct $GLOBALS['meta']['truc'], si $cfg ne contient ni / ni :
        if ($cfg AND strpbrk($cfg,'/:')===false){
                $r = isset($GLOBALS['meta'][$cfg])?
                  ((!$unserialize
                        // ne pas essayer de deserialiser autre chose qu'une chaine
                        OR !is_string($GLOBALS['meta'][$cfg])
                        // ne pas essayer de deserialiser si ce n'est visiblement pas une chaine serializee
                        OR strpos($GLOBALS['meta'][$cfg],':')===false
                        OR ($t=@unserialize($GLOBALS['meta'][$cfg]))===false)?$GLOBALS['met
a'][$cfg]:$t)
                  :$def;
                return $r;
        }

        // Brancher sur methodes externes si besoin
        if ($cfg AND $p=strpos($cfg,'::')){
                $methode = substr($cfg,0,$p);
                $lire_config = charger_fonction($methode, 'lire_config');
                return $lire_config(substr($cfg,$p+2),$def,$unserialize);
        }

        list($table,$casier,$sous_casier) = expliquer_config($cfg);

        if (!isset($GLOBALS[$table]))
                        return $def;

        $r = $GLOBALS[$table];

        // si on a demande #CONFIG{/meta,'',0}
        if (!$casier)
                return $unserialize ? $r : serialize($r);

        // casier principal :
        // le deserializer si demande
        // ou si on a besoin
        // d'un sous casier
        $r = isset($r[$casier])?$r[$casier]:null;
        if (($unserialize OR count($sous_casier)) AND $r AND is_string($r))
                $r = (($t=@unserialize($r))===false?$r:$t);

        // aller chercher le sous_casier
        while(!is_null($r) AND $casier = array_shift($sous_casier))
                $r = isset($r[$casier])?$r[$casier]:null;

        if (is_null($r)) return $def;
        return $r;
}

function expliquer_config($cfg){
        // par defaut, sur la table des meta
        $table = 'meta';
        $casier = null;
        $sous_casier = array();
        $cfg = explode('/',$cfg);

        // si le premier argument est vide, c'est une syntaxe /table/ ou un appel vide ''
        if (!reset($cfg) AND count($cfg)>1) {
                array_shift($cfg);
                $table = array_shift($cfg);
                if (!isset($GLOBALS[$table]))
                        lire_metas($table);
        }

        // si on a demande #CONFIG{/meta,'',0}
        if (count($cfg)) {
                // pas sur un appel vide ''
                if ('' !== ($c = array_shift($cfg))) {
                        $casier = $c;
                }
        }

        if (count($cfg))
                $sous_casier = $cfg;

        return array($table,$casier,$sous_casier);
}
}
?>
