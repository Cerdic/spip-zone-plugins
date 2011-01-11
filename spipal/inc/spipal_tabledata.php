<?php
/***************************************************************************\
 *  SPIPAL, Utilitaire de paiement en ligne pour SPIP                      *
 *                                                                         *
 *  Copyright (c) 2007 Thierry Schmit                                      *
 *  Copyright (c) 2011 Emmanuel Saint-James                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

// Retour a l'envoyeur: 
// le code ci-dessous est une modification par Thierry Schmit
// du code du plugin Table Data de Christophe BOUTIN
// qui lui meme etait une modification d'une de mes extensions de SPIP:
// http://www.spip-contrib.net/La-gestion-de-tables-SQL

//gestion de la présentation des clé primaires sous une forme
//compréhensible par un humain
$GLOBALS['auto_form']['plugins'][] = '';
$GLOBALS['auto_form']['plugins'][] = 'spipal_';
 
function mbt_echo_form_table($table_mysql, 
			     $serveur='', 
			     $idLigne='', 
			     $prop_champs = array(), //details sur des champs décrits qui hérite par ce paramètre d'une particularité
			     $nom_plugin='',  // lieu des traductions pour les labels des champs
                            $echo_pk = true)        //est ce que l'on affiche les clés primaires ou pas ?
{
//pour la gestion de l'initialisation des champs par JS.
//un peu gadget, mais ceux qui font de la saisie appréçieront
static $racine_id = 'eztezarteza';
static $cpt_id = 0;

    $trouver_table = charger_fonction('trouver_table', 'base');
    $abstract = $trouver_table($table_mysql);
    if (!$abstract  OR !$idLigne) return '';
    $keys   = $abstract['key'];
    $fields = $abstract['field'];
    $ufields = array_keys($prop_champs);
    $ligne = sql_fetsel($ufields, $table_mysql, $keys['PRIMARY KEY'] ."=" . sql_quote($idLigne));

    $hiddens = '';
    $total   = array();
    foreach ($ufields as $k) {
        $v = $ligne ? $ligne[$k] : '';
        preg_match("/^ *([A-Za-z]+) *(\(([^)]+)\))?(.*default *'(.*)')?/i", $fields[$k], $m);
        $type = strtoupper($m[1]);
        $s = ($m[5] ? " value='$m[5]' " : '');
        $d = ( isset($prop_champs[$k]['size']) )?" size='{$prop_champs[$k]['size']}' ":" size='60' ";
        $t = $m[3];
        if ($m[2]) {
            if (is_numeric($t)) {
                if ($t <= 32)
                    $d = " sizemax='$t' size='" . ($t * 2) . "'";
                else
                    $type = 'BLOB';
            }
            else {
                preg_match("/^ *'?(.*[^'])'? *$/", $t, $m2); $t = $m2[1];
            }
        }
        
	$initScript = $pk = '';
        if (array_search($k, $keys) == "PRIMARY KEY") {
            if ( !$echo_pk )
                continue;
            $pk  = '(*)';
            if ( $type != 'VARCHAR')
                $d .= " readonly='readonly' ";
        }
        
        switch ( $type ) {
            case INT:
            case SMALLINT:
            case INTEGER:
            case BIGINT:
            case CHAR:
            case VARCHAR:
                if ( (strpos($k, 'id_')) === 0 && ($pk == '') ) {
                    foreach ($GLOBALS['auto_form']['plugins'] as $plugin) {
                        $nom_table = 'spip_' . $plugin . substr($k, 3) .'s';
                        $abstract = $trouver_table($nom_table);
                        if ( isset($abstract['field']) ) {
                            $order = '';
                            $what = array();
                            if ( isset($GLOBALS['auto_form']['pk-h'][$nom_table]) ) {
                                $what = $GLOBALS['auto_form']['pk-h'][$nom_table];
                                $order = " ORDER BY $what";
                                $what = explode(',', $what);
                            }
                            $result  = sql_select('*', $nom_table,'', '', $order);
                            $select = "<option value='0'>0</option>";
                            while ( $ligneLoc = sql_fetch($result) ) {
                                $selected = '';
                                if ( $v == $ligneLoc[$k]) {
                                    $selected = " selected='selected' ";
                                }
                                $pk_h = '';
                                foreach ($what as $cle) {
                                    $pk_h .= $ligneLoc[$cle].' ';
                                }
                                $pk_h .= "({$ligneLoc[$k]})";
                                $select .= "<option value='{$ligneLoc[$k]}' $selected>$pk_h</option>";
                            }
                            $s = "<td><select name='$k'>$select</select></td>";
                            break 2;
                        }
                    }
                }
            case TINYTEXT:
            case TINYBLOB:
            CASE FLOAT:
                    $s = " value='{$v}' ";
		    $s = "<td><input type='text'$s $d name='$k' /></td>\n";
                break;
            CASE TINYINT:
            CASE BOOL:
            CASE BOOLEAN:
                $selected_1 = '';
                if ($v == 1 ) {
                    $selected_1 = " selected='selected' ";
                }
                $s = "<td><select name='$k'>".
                          "<option value='0'>0</option>".
                          "<option value='1' $selected_1>1</option>".
                          "</select></td>";
                break;
            case ENUM:
                $s = "<td><select name='$k'>\n";
                foreach (split("'? *, *'?",$t) as $v) {
                    $c = '';
                        if ( $v == $v )
                            $c = " selected='selected' ";
                    $s .= "<option $c >$v</option>\n";
                }
                $s .= "</select></td>\n";
                break;
            case DATETIME:
                $s = '';
                $hiddens .= "<input type='hidden' name='$k' value='NOW()'/>\n";
                break;
            case TIMESTAMP:
                $s = '';
                break;
            case LONGBLOB:
		$s = $v;
                $s = "<td><textarea name='$k' cols='40' rows='20'>$s</textarea></td>\n";
                break;
            default:
		$s = $v;
                $t = floor($t / 40) + 3;
                $id = $racine_id.($cpt_id++);
                $s = "<td><textarea name='$k' cols='40' rows='$t' id='$id'>$s</textarea></td>\n";
                $initScript = "<a href=\"javascript:intialiser_text('$id');\">/i/</a>";
                break;
        }
        if ($s) {
            if ( $nom_plugin ) {
                $label = _T($nom_plugin . ':' . $k);
            } else $label = $k;
            $total[]= "<tr><td>$label $pk $initScript</td>\n$s</tr>";
        }
    }
    
    return $hiddens . '<table>' . join("\n", $total) . '</table>';
}

function mbt_maj_table_depuis_form($table_mysql, $action)
{
    $trouver_table = charger_fonction('trouver_table', 'base');
    $abstract = $trouver_table($table_mysql);
    $fields = $abstract['field'];
    
    $pks    = explode(',', $abstract['key']['PRIMARY KEY']);
    foreach ( $pks as $k => $v) $pks[$k] = trim($v);

    if ($action == 'maj')
      return maj_item($table_data, $fields, $pks);
    
    $args = array();
    foreach ($fields as $k => $v) {
      if ( $v !== 'TIMESTAMP' AND isset($_REQUEST[$k]) ) {
	$args[$k]= $_REQUEST[$k];

      }
    }
    return sql_insertq($table_mysql, $args);
}


function maj_item($table_data, $fields, $pks)
{
	$fields = array();
	foreach ($fields as $k => $v) {
                if ( $v !== 'TIMESTAMP')
		  $fields[$k] = $_REQUEST[$k];
	}

	$and = array();
	foreach ( $pks as $pk ) {
                if ( isset($_REQUEST[$pk]) ) {
		  $and[]="$pk=" . sql_quote($_REQUEST[$pk]);
                }
                else {
		  $and = false;
		  break;
                }
	}

	if ($and !== false)
	  sql_updateq($table_mysql, $fields, join(" AND ", $and));
}

function supprimer_item($table_data, $pk, $id)
{
	sql_delete($table_data, "$pk=" . intval($id));
}
?>
