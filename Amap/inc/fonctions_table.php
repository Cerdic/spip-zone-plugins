<?php
##########################################################################
#
#           Page de configuration du plugin Amap pour SPIP
#
#
#             Site de documentation du plugin Amap
#                       http://www.dadaprod.org
#
#                        licence GNU/GPL
#	                 2008 - Stéphane Moulinet
//=====================================================================
// adaptation du PLUGIN - TABLE DATA - V0.40
//                                         CONTRIBUTION POUR SPIP 1.9.1
// 19 fév 2007                          Christophe BOUTIN - Opalys.info
//=====================================================================
############################################################################

//=========================================================================
//=========================================================================
//
function table_amap_lister($table, $serveur, $field, $key)
{
    global $table_prefix, $debut, $page;

    $sqlResult = requete_tableamap($table, $field, $key);
    $nombre_Enregistrements = sql_count($sqlResult);

    $max_par_page = 20;
    $debut = intval($debut);

    if ($debut > $nombre_Enregistrements - $max_par_page)
        $debut = max(0,$nombre_Enregistrements - $max_par_page);

    pipeline('exec_init',array('args'=>array('exec'=>$page),'data'=>''));

    if ( $nombre_Enregistrements<1)
    {
         echo "<br/>Cette table est actuellement vide:<br/>"
             ,"Elle ne contient aucun enregistrement.<br/><br/>";
    }
    else
    {
        $tabLesEnregistrements = array();
        $i = 0;

        while ($tabUnEnregistrement = sql_fetch($sqlResult))
        {
            if ($i>=$debut AND $i<$debut+$max_par_page)
            {
                $tabLesEnregistrements[] = $tabUnEnregistrement ;
            }
            $i++;
         }

         affiche_tableamap($table,$tabLesEnregistrements, $max_par_page, $nombre_Enregistrements);
    }
} // fin function table_amap_lister


//=========================================================================
//=========================================================================
//
function affiche_tableamap($table, $tabLesEnregistrements, $max_par_page, $nombre_Enregistrements)
{
    global $debut, $options, $spip_lang_right, $visiteurs, $connect_id_auteur, $connect_statut, $connect_toutes_rubriques, $page;

    //début affichage tableau
    echo "<DIV id='tabledate_tablist'  style='width:498px;overflow:auto'>\n";
    echo "<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=0 WIDTH='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
    echo "<tr bgcolor='#DBE1C5'>";

    // Affichage dynamique des colonnes
    $tabPremiers = $tabLesEnregistrements[0];

    foreach ($tabPremiers as $txtTitre=>$valeur)
    {
        echo "\t<td>";
        echo "\t\t<b>",$txtTitre,"</b>";
        echo "\t</td>\n";     //"../"
    }
    echo "</tr>\n";

    if ($nombre_Enregistrements > $max_par_page)
    {
        echo "<tr bgcolor='white'>"
               ,"<td class='arial1' colspan='",count($tabLesEnregistrements[0]),"'>";

        for ($j=0; $j < $nombre_Enregistrements; $j+=$max_par_page)
        {
            if ($j > 0) echo " | ";

            if ($j == $debut)
                echo "<b>$j</b>";
            else if ($j > 0)
              echo "<a href='"
                    , generer_url_ecrire($page,"debut=".$j."&table=".$table)
                    , "'>$j</a>";
            else
              echo "<a href='"
                    , generer_url_ecrire($page,"table=".$table)
                    , "'>0</a>";

            if ($debut > $j  AND $debut < $j+$max_par_page)
            {
                echo " | <b>$debut</b>";
            }

        }
        echo "</td></tr>\n"; // fin de ligne
        echo "<tr height='5'></tr>"; // ligne espacement
    }

    afficher_n_enregistrements_amap( $tabLesEnregistrements , $table);

    echo "</table>\n"; // FIN DE TABLEAU
    echo "</div>\n"; // id='tabledate_tablist'

    echo "<a name='bas'>";
    echo "<table width='100%' border='0'>";

    $debut_suivant = $debut + $max_par_page;
    if ($visiteurs) $visiteurs = "\n<input type='hidden' name='visiteurs' value='oui' />";
    if ($debut_suivant < $nombre_Enregistrements OR $debut > 0)
    {
        echo "<tr height='10'></tr>";
        echo "<tr bgcolor='white'><td align='left'>";
        if ($debut > 0)
        {
            $debut_prec = max($debut - $max_par_page, 0);
            echo generer_url_post_ecrire($page
                                 ,"&debut=".$debut_prec."&table=".$table),
              "\n<input type='submit' value='&lt;&lt;&lt;' class='fondo' />",
              $visiteurs,
              "\n</form>";
        }

        echo "</td><td style='text-align: $spip_lang_right'>";

        if ($debut_suivant < $nombre_Enregistrements)
        {
            echo generer_url_post_ecrire($page,"tri=".$tri."&debut=".$debut_suivant."&table=".$table),
              "\n<input type='submit' value='&gt;&gt;&gt;' class='fondo' />",
              $visiteurs,
              "\n</form>";
        }
        echo "</td></tr>\n";
    }

    echo "</table>\n"; // FIN DE TABLEAU

} // fin function affiche_tableamap

//=========================================================================
//=========================================================================
//
function requete_tableamap($table , $field, $key, $idWhere = false)
{
    global $connect_statut, $spip_lang, $connect_id_auteur;

    if ( isset($key['PRIMARY KEY']))
    {
         $txtQuery = "SELECT `".$key['PRIMARY KEY']."`";
         $boolBesoinSeparateur = true;
    }
    else
    {
         $txtQuery = "SELECT ";
         $boolBesoinSeparateur = false ;
    }

    foreach ($field as $cle=>$txtChamp)
    {
        if ($key['PRIMARY KEY']!=$cle) $txtQuery .= ($boolBesoinSeparateur?", ":"")."`".$cle."`";
        $boolBesoinSeparateur = true;
    }

    $txtQuery .= " FROM ". $table ;

    if ($idWhere!=false)
    {
        $txtQuery .= " WHERE `".$key['PRIMARY KEY']."`='".$idWhere."';" ; // Limitation1
    }
    else if ( isset($key['PRIMARY KEY']))
    {
      $txtQuery .= " ORDER by `".$key['PRIMARY KEY']."`;" ; // Limitation1
    } // if ($idWhere)

    $rows = sql_query($txtQuery);

    return $rows;
} // fin function requete_tableamap


//=========================================================================
//=========================================================================
// Le nombre de champs à afficher est dynamique.
// Par contre l'Identifiant (Id Ligne) doit être le premier champ à gauche
function afficher_n_enregistrements_amap($tabLesEnregistrements, $table)
{
    global $connect_statut, $options, $messagerie, $page;

    foreach ($tabLesEnregistrements as $intNumLigne=>$tabUnEnregistrement)
    {

        echo "\t<tr style='background-color: #eeeeee;'>\n";
        $compteur = 1 ; // limitation 1
        foreach ($tabUnEnregistrement as $txtChamp=>$txtValeur)
        {
            if ( $compteur==1 ) $idLigne= $txtValeur; // limitation 1 : id = 1° champ

            echo "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
            echo "\t\t\t<a href='"
                       ,generer_url_ecrire($page, "action=edit&id_ligne=".$idLigne."&table=".$table)  // limitation 1
                ,"'>\n";
            echo "\t\t\t\t",$txtValeur,"\n" ;
            echo "\t\t\t</a>\n";
            echo "\t\t</td>\n";

            ++$compteur;
        } // fin foreach ($tabUnEnregistrement as $txtChamp=>$txtValeur)

        echo "\t</tr>\n";
    } // fin foreach ($tabLesEnregistrements as $intNumLigne=>$tabUnEnregistrement)

} // fin function afficher_n_enregistrements_amap


//=========================================================================
//=========================================================================
//
function table_amap_getmodif($table, $serveur, $field, $key , $idLigne)
{
    global $page;

    $sqlResult = requete_tableamap($table , $field, $key, $idLigne);
    $nombre_Enregistrements = sql_count($sqlResult);

    if ($nombre_Enregistrements>0)
    {
        $total = '';
        $hiddens = '';
        $tabUnEnregistrement = sql_fetch($sqlResult);

        foreach ($field as $k => $v)
        {
          if (array_search($k, $key) == "PRIMARY KEY")
          {
              $debut = "Modifier l'enregistrement ayant comme clé primaire :<br/><i><b>"
                        .$k."='".$tabUnEnregistrement[$k]."'</b></i><br/>";
          }
          else
          {
              ereg("^ *([A-Za-z]+) *(\(([^)]+)\))?(.*DEFAULT *'(.*)')?", $v, $m);
              $type = $m[1];
              $s = ($m[5] ? " value='$m[5]' " : '');
              $t = $m[3];
              if ($m[2])
              {
                  if (is_numeric($t))
                  {
                    if ($t <= 32)
                    {
                        $s .= " sizemax='$t' size='" . ($t * 2) . "'";
                    }
                    else
                    {
                        $type = 'BLOB';
                    }
                  }
                  else
                  {
                    ereg("^ *'?(.*[^'])'? *$", $t, $m2); $t = $m2[1];
                  }
              }

              switch (strtoupper($type))
              {
// JFM - Debut (Ajout/Modification)
                case TINYINT:
									if ($t==1)
                  {
							      $checked = "";
										if ($tabUnEnregistrement[$k] == 1)
										{
										  $checked = " checked";
										}
										$s = "<td>"
	                       ."<input type='checkbox' name='".$k."'"
	                       ." value='1'".$checked."/>"
	                       ."</td>\n";
										break;
									}
                case BIGINT:
                case CHAR:
                case INT:
                case INTEGER:
                case TEXT:
                case TINYBLOB:
                case TINYINT:
                case TINYTEXT:
                case VARCHAR:
                case YEAR:
                  $s = "<td>"
                       ."<input type='text'".$s." name='".$k."'"
                       ." value='".htmlentities($tabUnEnregistrement[$k], ENT_QUOTES)."'/>"
                       ."</td>\n";
                  break;
                case ENUM:
                case SET:
                  $s = "<td><select name='$k'>\n";
                  foreach (split("'? *, *'?",$t) as $v)
                  {
                     # if (!$v) $v = "''";
                     if ($tabUnEnregistrement[$k]==$v)
                     {
                        $s .= "<option selected>$v</option>\n";
                     }
                     else
                     {
                        $s .= "<option>$v</option>\n";
                     }
                  } //foreach (split("'? *, *'?",$t) as $v)
                  $s .= "</select></td>\n";
                  break;
// JFM - Fin
                case DATETIME:
                  $s = '';
                  $hiddens .= "<input type='hidden' name='$k' value='".$tabUnEnregistrement[$k]."'/>\n";
                  break;
                case TIMESTAMP:
                  $s = '';
                  break;
                case LONGBLOB:
// JFM - Debut (Modification)
                  $s = "<td><textarea name='$k' cols='64' rows='20'>".htmlentities($tabUnEnregistrement[$k])."</textarea></td>\n";
// JFM - Fin
                  break;
                default:
                  $t = floor($t / 64)+1;
// JFM - Debut (Modification)
                  $s = "<td><textarea name='$k' cols='64' rows='$t'>".htmlentities($tabUnEnregistrement[$k])."</textarea></td>\n";
// JFM - Fin
                  break;
              } //switch (strtoupper($type))
              if ($s)
                $total .= "<tr><td>$k</td>\n$s</tr>\n";
          }
        }
        return generer_url_post_ecrire($page
                               , "table=".$table."&serveur=".$serveur
                                   ."&mode=".$mode."&action=maj&id_ligne=".$idLigne)
                                 ."<table>\n".$debut.$total
                                 ."</table>".$hiddens."<input type='submit'/></form>";
    } // if ($nombre_Enregistrements>0)

} // function table_amap_getmodif



//=========================================================================
//=========================================================================
//
function table_amap_get($table, $serveur, $field, $key)
{
  global $page;
  $total = '';
  $hiddens = '';

  foreach ($field as $k => $v)
  {
      if (array_search($k, $key) == "PRIMARY KEY")
      {
          $debut = "<i>'".$k."'</i> est la clé primaire de la table <i>'".$table."'</i><br/>";
      }
      else
      {
          ereg("^ *([A-Za-z]+) *(\(([^)]+)\))?(.*default *'(.*)')?", $v, $m);
          // Si majuscule ne marche pas (test avec mysql 3.23)
          // ereg("^ *([A-Za-z]+) *(\(([^)]+)\))?(.*default *'(.*)')?", $v, $m);

          $type = $m[1];
          $s = ($m[5] ? " value='$m[5]' " : '');
          $t = $m[3];
          if ($m[2])
          {
              if (is_numeric($t))
              {
                if ($t <= 32)
                    $s .= " sizemax='$t' size='" . ($t * 2) . "'";
                else
                    $type = 'BLOB';
              }
              else
              {
                ereg("^ *'?(.*[^'])'? *$", $t, $m2); $t = $m2[1];
              }
          }
          switch (strtoupper($type))
          {
            case TINYINT:
	      if ($t==1)
              {
                $checked = "";
		if ($tabUnEnregistrement[$k] == 1) {
		  $checked = " checked";
		}
		$s = "<td><input type='checkbox' name='".$k."' value='1'".$checked."/></td>\n";
		break;
	      }
            case BIGINT:
            case CHAR:
            case INT:
            case INTEGER:
            case TEXT:
            case TINYTEXT:
            case TINYINT:
            case TINYBLOB:
            case VARCHAR:
              $s = "<td><input type='text'$s name='$k'/></td>\n";
              break;
             case ENUM:
             case SET:
              $s = "<td><select name='$k'>\n";
              foreach (split("'? *, *'?",$t) as $v)
              {
                    #if (!$v) $v = "''";
                    $s .= "<option>$v</option>\n";
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
              $s = "<td><textarea name='$k' cols='64' rows='20'>".htmlentities($m[5])."</textarea></td>\n";
               break;
            default:
              $t = floor($t / 64)+1;
              $s = "<td><textarea name='$k' cols='64' rows='$t'>".htmlentities($m[5])."</textarea></td>\n";
               break;
          } // if ($m[2])

          if ($s)
            $total .= "<tr><td>$k</td>\n$s</tr>\n";
      } // fin if (array_search
  } // fin foreach
  return generer_url_post_ecrire($page
                           , "table=".$table."&serveur=".$serveur."&mode=".$mode)
                             ."<table>\n".$debut.$total
                             ."</table>$hiddens<input type='submit'/></form>";

} // function table_amap_get

//===========================================================
function table_amap_modif_post($table , $serveur, $field, $key, $idWhere)
{
  unset($_POST['table']);
  unset($_POST['serveur']);
  unset($_POST['mode']);
  unset($_POST['exec']);
  $n = array();
  $i = array();

    $txtQuery = "UPDATE ". $table." SET ";

    $txtSeparation="";
    foreach ( $field as $cle=>$txtChamp )
    {
        if ( $key['PRIMARY KEY']!=$cle )
        {
            if ( isset($_POST[$cle]) )
                $txtQuery .= $txtSeparation.$cle."= '".addslashes($_POST[$cle])."'" ;
            $txtSeparation=", ";
        }
    } // foreach $field

    $txtQuery .= " WHERE ".$key['PRIMARY KEY']."='".$idWhere."';" ; // Limitation1

    $retourQuery = sql_query($txtQuery);

    return "" .
        (!$retourQuery ?
           '<br/><b>Erreur dans la requete ('.$txtQuery.') à la base...</b><br/>'
           : ("<br/>Modification de la ligne : ".$idWhere
               .", dans la table ".$table."<br/>"));
} // function table_amap_modif_post

//===========================================================
function table_amap_post($table, $serveur)
{
  unset($_POST['table']);
  unset($_POST['serveur']);
  unset($_POST['mode']);
  unset($_POST['exec']);
  $n = array();
  $i = array();

  echo "id_paysan: ".$_POST['id_paysan'].", label_produit: ".$_POST['label_produit'];

  foreach ($_POST as $k => $v)
  {
        if ($v && ($v != 'NOW()')) $v = "'" . addslashes($v) . "'";
        if ($v) { $n[] = $k; $i[] = $v;}
  }
  $r = sql_insert($table,
                 "(" . join(', ', $n) . ")",
                 "(" . join(', ', $i) . ")",
                 $serveur);

  return "Insertion dans la table $table " .
    (!$r ? '' : ("sous le numero: $r"));
} // function table_amap_post

?>
