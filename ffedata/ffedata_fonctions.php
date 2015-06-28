<?php
/**
 * Fonctions utiles au plugin Data FFE
 *
 * @plugin     Data FFE
 * @copyright  2015
 * @author     Jacques
 * @licence    GNU/GPL
 * @package    SPIP\Ffedata\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function inc_echecs_to_array_dist($u) {
    $obj = simplexml_load_string($u);
    // gestion du namespace spécifique au wsdl microsoft :
    // <diffgr:diffgram xmlns:msdata="urn:schemas-microsoft-com:xml-msdata" xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1">
    $data = $obj->children('urn:schemas-microsoft-com:xml-diffgram-v1')->children('')->NewDataSet;
    // transformation de l'objet en array
    $array = json_decode(json_encode((array)$data),1);
    // suppression récursive des arrays vides (plus propre)
    $array = echecs_array_remove_empty($array);
    return $array;
}

function echecs_array_remove_empty($haystack) {
    foreach ($haystack as $key => $value) {
        if (is_array($value)) {
            $haystack[$key] = echecs_array_remove_empty($haystack[$key]);
        }
        if (empty($haystack[$key])) {
            unset($haystack[$key]);
        }
    }
    return $haystack;
}

//pour afficher le tableau des joueurs dans le modèle des équipes PV 

function echec_ligne_tableau($vals, $nb, $njoueurs='10') {
    static $liste = array('Blanc', 'Noir', 'Resultat');
 
    $ligne = '';
	if ($nb % 2 == 0) {
	$ligne .= '<tr class="row_odd odd">';
	}
	else {
	$ligne .= '<tr class="row_even even">';
	}

    if (isset($vals['Blanc' . $nb])) {

        foreach ($liste as $cellule) {
            $v = isset($vals[$cellule . $nb]) ? $vals[$cellule . $nb] : '';
						
			if ($cellule == 'Resultat') {
              switch ($v) {
            case 1:
                 $result="X-X";
				break;	
              case 2:
			     if ($njoueurs==10 && $nb <=6) $result="2-0";
			     else $result="1-0";
                 break;
              case 3:
			     if ($njoueurs==10 && $nb <=6) $result="0-2";
                 else $result="0-1";
                 break;
			case 4:
				$result="0-0";
				break;	;
			case 5:
				$result="1-F";
				break;	
			case 6:
				$result="F-1";
				break;	
			case 9:
				$result="F-F";
				break;				 
            case 15:
				$result="A-1";
				break;
			  default:
				$result="1-F";
              }
				$ligne .= '<td style="width:35px;">' . $result . '</td>';   
			}
			else {
				   $ligne .= '<td>' . $v . '</td>';
			}
        }
    }
	else {		
	foreach ($liste as $cellule) {
            $v = isset($vals[$cellule . $nb]) ? $vals[$cellule . $nb] : '';
			if ($cellule == 'Resultat') {
					$ligne .= '<td> F-1 </td>';
				}
				else {
					$ligne .= '<td>' . $v . '</td>';
				}
			}
		}
        
        $ligne .= "</tr>";
    return $ligne;
}

		
		    // {va_chercher #TITRE}
    function critere_va_chercher_dist($idb, &$boucles, $crit) {
            $boucle = &$boucles[$idb];
            $table = $boucle->id_table;
            $not = $crit->not;
     
            // chercher quoi ?
            if (isset($crit->param[0])) {
                    $quoi = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
            } else {
                    // rendons obligatoire ce parametre
                    return (array('zbug_critere_necessite_parametre', array('critere' => $crit->op )));
            }
     
            $c = array("'OR'",
                    array("'LIKE'", "'$table.titre'", "sql_quote('%' . $quoi . '%')"),
                    array("'LIKE'", "'$table.texte'", "sql_quote('%' . $quoi . '%')")
            );
     
            // Inversion de la condition ?
            if ($crit->not) {
                    $c = array("'NOT'", $c);
            }
           
            $boucle->where[] = $c;
    }

	//Une fonction addition, pour additionner les licences A et B
	//http://contrib.spip.net/Balises-arithmetiques,3124
	
	function balise_ADDITION_dist($p)
{
  $a = interprete_argument_balise(1, $p);
  $b = interprete_argument_balise(2, $p);

  if ($a == '' || $b == '')
  {
     $p->code = '\'#ADDITION[Manque argument]\'';
  }
  else
  {
     $p->code = '(' . $a . '+' . $b . ')';
  }

  return $p;
}