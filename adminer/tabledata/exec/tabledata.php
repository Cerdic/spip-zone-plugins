<?php

//=====================================================================
// PLUGIN - TABLE DATA -
define( "CSTE_VERSION" , "V2.2.0" );

// fichier : exec/tabledata.php
// version : 2.2.1
// date : 25 mai 2009

//                                           CONTRIBUTION POUR SPIP 2.0
//                                      Christophe BOUTIN - Opalys.info
//=====================================================================
// L'objet de cette contrib est de visualiser, ajouter, modifier
// et supprimer le contenu de tables dites extra.
// La contrib 'La gestion de tables SQL supplémentaires' de Déesse A.
// Celle-ci m'a beaucoup plu mais elle m'a laissé sur ma fin...
// Alors je me suis lancé.
// =====================================================================
// Pour la modification et l'ajout
# Génération automatique d'un formulaire de remplissage d'une table SQL
# à partir de la description fournie par spip_abstract_showtable
# et traitement du formulaire rempli afin d'insérer une nouvelle entrée
# dans la table.
# assimile les petits types à un input et les gros à un textarea
# Traite à part les entrées de type DATETIME, ignore les TIMESTAMP,
// (Commentaires repris de Déesse A.)
// ======================================================================
//
// Espérant que vous apprécirez ce module.
// Bien à vous. - mon mél : christophe {@} cboutin . net -


if (!defined("_ECRIRE_INC_VERSION")) return;

// require_once ("base/abstract_sql.php");
include_spip('inc/presentation');

function tabledata_Page_fin()
{
    echo "\n<br/><br/>\n";
    // echo "<hr/>\n";
    echo "<center><i class='arial1' >"
          ,"Plugin TableDATA ".CSTE_VERSION." pour SPIP 2.0 par Christophe Boutin -Opalys.info- <br/>"
          ,"pour la gestion de 'tables extra'."
          ,"</i></center>\n";
    // echo "<hr/>\n";
    echo fin_gauche();
    echo fin_page(); // affiche bandeau SPIP version, etc.
}

//=========================================================================
//=========================================================================
//
function tabledata_Liste_preparedonnees($table, $serveur, $field, $key,
                            $boolFntModif,$trisens,$trival,$intPremierEnreg)
{

    $intPremierEnreg = intval($intPremierEnreg);
    $max_par_page = 20;

    $sqlResult = tabledata_Cde_select($table, $field,$trisens,$trival, $key);
    $nombre_Enregistrements = sql_count($sqlResult);

    if ($intPremierEnreg <0 )
    {
        $intPremierEnreg = ((int)($nombre_Enregistrements/$max_par_page))*$max_par_page;
    }

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
            if ($i>=$intPremierEnreg AND $i<$intPremierEnreg+$max_par_page)
            {
                $tabLesEnregistrements[] = $tabUnEnregistrement ;
            }
            $i++;
         }

         tabledata_Liste_preparetableau($table,$tabLesEnregistrements, $max_par_page, $nombre_Enregistrements, $boolFntModif,$trisens,$trival,$intPremierEnreg,$key);
    }
}


//=========================================================================
//=========================================================================
//
function tabledata_Liste_preparetableau($table,$tabLesEnregistrements, $max_par_page, $nombre_Enregistrements, $boolFntModif,$trisens,$trival,$intPremierEnreg,$key)
{
//    global $intPremierEnreg, $options, $spip_lang_right, $visiteurs, $connect_id_auteur, $connect_statut,   $connect_toutes_rubriques;

    //début affichage tableau
    echo "<div id='tabledate_tablist'  style='width:498px;height:530px;overflow:auto'>\n";
    echo "<table class=\"spip\">\n";
    echo "<tr class=\"row_first\">";
    echo "\t<td><nbsp;</td>";

    // Affichage dynamique des colonnes
    $tabPremiers = $tabLesEnregistrements[0];

    foreach ($tabPremiers as $txtTitre=>$valeur)
    {
        echo "\t<td>";
        echo "\t\t<b>",$txtTitre,"</b>";
        if ($trival==$txtTitre)
        {
            if ($trisens=="A")
            {  // si A on demande D
                echo "<a href='"
                     , tabledata_url_generer_ecrire('tabledata',
                         "intPremierEnreg=".$j."&table=".$table."&trisens=D&trival=".$txtTitre)
                     , "'>"
                     ,"<IMG src='",_DIR_PLUGIN_TABLEDATA,"/img_pack/tri_asc.png' title='Trier sur ce champ ordre d&#233;croissant'>"
                     ,"</a>";
            }
            else if ($trisens=="D")
            {   // si D on demande aucun
                echo "<a href='"
                     , tabledata_url_generer_ecrire('tabledata',
                         "intPremierEnreg=".$j."&table=".$table)
                     , "'>"
                     ,"<IMG src='",_DIR_PLUGIN_TABLEDATA,"/img_pack/tri_desc.png' title='Arr&#234;ter le tri  sur ce champ'>"
                     ,"</a>";
            }
            else
            {   // si pas (A ouD) alors y en a pas on demande A
                echo "<a href='"
                     , tabledata_url_generer_ecrire('tabledata',
                         "intPremierEnreg=".$j."&table=".$table."&trisens=A&trival=".$txtTitre)
                     , "'>"
                     ,"<IMG src='",_DIR_PLUGIN_TABLEDATA,"/img_pack/tri_off.png' title='Trier sur ce champ ordre croissant'>"
                     ,"</a>";
            }
        }
        else
        {   // Aucun tri n'est déjà proposé
            echo "<a href='"
                 , tabledata_url_generer_ecrire('tabledata',
                     "intPremierEnreg=".$j."&table=".$table."&trisens=A&trival=".$txtTitre)
                 , "'>"
                 ,"<IMG src='",_DIR_PLUGIN_TABLEDATA,"/img_pack/tri_off.png' title='Trier sur ce champ ordre croissant'>"
                 ,"</a>";
        }
        echo "\t</td>\n";     //"../"
    }
    echo "</tr>\n";

    if ($nombre_Enregistrements > $max_par_page)
    {
        echo "<tr bgcolor='white'>"
               ,"<td class='arial1' colspan='",count($tabLesEnregistrements[0])+1,"'>";

        for ($j=0; $j < $nombre_Enregistrements; $j+=$max_par_page)
        {
            if ($j > 0) echo " | ";

            if ($j == $intPremierEnreg)
            {
                echo "<b>$j</b>";
            }
            else if ($j > 0)
            {
              echo "<a href='"
                    , tabledata_url_generer_ecrire('tabledata',"intPremierEnreg=".$j."&table=".$table
                                          ."&trisens=".$trisens."&trival=".$trival)
                    , "'>$j</a>";
            }
            else
            {
              echo "<a href='"
                    , tabledata_url_generer_ecrire('tabledata',"table=".$table
                                          ."&trisens=".$trisens."&trival=".$trival)
                    , "'>0</a>";
            }

            if ($intPremierEnreg > $j  AND $intPremierEnreg < $j+$max_par_page)
            {
                echo " | <b>$intPremierEnreg</b>";
            }

        }
        echo "</td></tr>\n"; // fin de ligne
        echo "<tr height='5'></tr>"; // ligne espacement
    }

    tabledata_Liste_affichedonnees( $tabLesEnregistrements , $table , $boolFntModif,$key);

    echo "</table>\n"; // FIN DE TABLEAU
    echo "</div>\n"; // id='tabledate_tablist'

    echo "<a name='bas'>";
    echo "<table width='100%' border='0'>";

    $intPremierEnreg_suivant = $intPremierEnreg + $max_par_page;
    if ($visiteurs) $visiteurs = "\n<input type='hidden' name='visiteurs' value='oui' />";
    if ($intPremierEnreg_suivant < $nombre_Enregistrements OR $intPremierEnreg > 0)
    {
        echo "<tr height='10'></tr>";
        echo "<tr bgcolor='white'><td align='left'>";
        if ($intPremierEnreg > 0)
        {
            $intPremierEnreg_prec = max($intPremierEnreg - $max_par_page, 0);
            echo "<a href='"
                     , tabledata_url_generer_ecrire('tabledata',
                         "intPremierEnreg=".$intPremierEnreg_prec."&table=".$table)
                     , "' title='Voir la liste pr&#233;c&#233;dente des enregistrements' >"
//préparer icone     ,"<IMG src='",_DIR_PLUGIN_TABLEDATA,"/img_pack/tri_desc.png' title='Arr&#234;ter le tri  sur ce champ'>"
                     ,"<<<"
                     ,"</a>";
        }

        echo "</td><td style='text-align: $spip_lang_right'>";

        if ($intPremierEnreg_suivant < $nombre_Enregistrements)
        {
                echo "<a href='"
                     , tabledata_url_generer_ecrire('tabledata',
                         "intPremierEnreg=".$intPremierEnreg_suivant."&table=".$table)
                     , "' title='Voir la suite des enregistrements' >"
//préparer icone     ,"<IMG src='",_DIR_PLUGIN_TABLEDATA,"/img_pack/tri_desc.png' title='Arr&#234;ter le tri  sur ce champ'>"
                     ,">>>"
                     ,"</a>";
        }
        echo "</td></tr>\n";
    }
    echo "</table>\n"; // FIN DE TABLEAU

} //function tabledata_Liste_preparetableau



//=========================================================================
//=========================================================================
//
function tabledata_Cde_select($table , $field,$trisens,$trival, $key, $idWhere = false)
{
    global $connect_statut, $spip_lang, $connect_id_auteur;

    //echo "table : $table , field : $field, trisens : $trisens, trival $trival, key $key, Where $idWhere";
    //echo "<br/>field : ";print_r($field);
    //echo "<br/>key : ";print_r($key);
    //echo "<br/>Where : ";print_r($idWhere);

    //$boolCleMultiple = (is_array($key['PRIMARY KEY']))?true:false;

    $leschamps = array();
    $lestables = $table;
    $clauseWhere = array();
    $clauseGroupby = array();
    $clauseOrderby = array();
    $clauseLimit = array();
    $clauseHaving = array();

    // Dans la suite : Le nombre de champs à afficher est dynamique.
    // Et : l'Identifiant (Id Ligne) doit être le premier champ retourné par SELECT

    foreach ($field as $cle=>$txtChamp)
    {
        $leschamps [] = $cle;
    } // foreach ($field as $cle=>$txtChamp)

    if ($idWhere!=false)
    {
        $clauseWhere[] = ($idWhere) ; // Limitation1
    } // if ($idWhere)
    else
    {
        if ( isset($trival) && $trival!="")
        {
            $clauseOrderby[] = $trival.($trisens=="D"?" DESC":"");
        }
        else if ( is_array($key['PRIMARY KEY']) && isset($key['PRIMARY KEY']) )
        {
            $clauseOrderby[] = implode(" , ",$key['PRIMARY KEY']) ; // Limitation1
        } //if ( isset($key['PRIMARY KEY']))
    } // if ($idWhere)

    $Rselect = sql_select( $leschamps,$table,$clauseWhere
                                   ,$clauseGroupby,$clauseOrderby
                                   ,$clauseLimit,$clauseHaving);

    $nbenreg = sql_count($Rselect) ;

    //-------- Voir le code de la requête
    //echo "<hr/>",sql_get_select( $leschamps,$table,$clauseWhere,$clauseGroupby,$clauseOrderby,$clauseLimit,$clauseHaving),"<br/> ressource : ";

    echo ($nbenreg==1)?"":" Nb enreg. dans la s&#233;lection : ".$nbenreg."<hr/>";

    return $Rselect;

} //function


//=========================================================================
//=========================================================================
// Le nombre de champs à afficher est dynamique.
// Par contre l'Identifiant (Id Ligne) doit être le premier champ à gauche
function tabledata_Liste_affichedonnees($tabLesEnregistrements, $table, $boolFntModif,$key)
{
    global $connect_statut, $options, $messagerie;

    foreach ($tabLesEnregistrements as $intNumLigne=>$tabUnEnregistrement)
    {

        echo "\t<tr style='background-color: #eeeeee;'>\n";
        $compteur = 1 ; // limitation 1
        foreach ($tabUnEnregistrement as $txtChamp=>$txtValeur)
        {
            if ( $compteur==1 )
            {
                    if ($boolFntModif)
                    {
                    if (is_array($key['PRIMARY KEY']))
                    {
                         $idLigne=" ";
                         foreach($key['PRIMARY KEY'] as $k)
                         {
                            $idLigne .= $k."='".addslashes($tabUnEnregistrement[$k]). "' AND "; // limitation 1 : id = 1° champ
                         }
                         $idLigne = substr($idLigne,0,strlen($idLigne)-4);
                         $idLigne = urlencode($idLigne) ;
                    }
                    else
                    {    // normalement ici on a pas de tableau
                         //$idLigne= $txtValeur; // limitation 1 : id = 1° champ
                         $idLigne= urlencode($k."='".addslashes($txtValeur). "' ");
                    }

                    echo "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>";
                        echo "\t\t\t<a href='"
                               ,tabledata_url_generer_ecrire('tabledata'
                                           ,"tdaction=suplig&id_ligne=".$idLigne."&table=".$table)  // limitation 1
                             , "'>"
                             ,"<IMG src='",_DIR_PLUGIN_TABLEDATA,"/img_pack/delon.png' title='Supprimer enregistrement ".$idLigne."'>"
                             ,"</a>";

                    echo "\t\t</td>\n";
                }
                else
                {
                    echo "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>";
                        echo "\t\t\t<IMG src='",_DIR_PLUGIN_TABLEDATA,"/img_pack/deloff.png' title='Supprimer d&#233;sactiv&#233;'>";

                    echo "\t\t</td>\n";
                }

            }
            echo "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
            if ($boolFntModif)
            {
                echo "\t\t\t<a href='"
                       ,tabledata_url_generer_ecrire('tabledata'
                                   ,"tdaction=edit&id_ligne=".$idLigne."&table=".$table)  // limitation 1
                ,"'>\n";
                echo "\t\t\t\t",$txtValeur,"\n" ;
                echo "\t\t\t</a>\n";
            }
            else
            {
                echo "\t\t\t\t",$txtValeur,"\n" ;
            }
            echo "\t\t</td>\n";

            ++$compteur;
        } // foreach ($tabUnEnregistrement as $txtChamp=>$txtValeur)

        echo "\t</tr>\n";
    } ///foreach ($tabLesEnregistrements as $intNumLigne=>$tabUnEnregistrement)

} // function tabledata_Liste_affichedonnees


//=========================================================================
//=========================================================================
//
// function tabledata_Fiche
function tabledata_Fiche($table, $serveur, $field, $key , $idLigne, $modeFiche ="voir")
{

    $nombre_Enregistrements = 0;

    // $mode : possible ajout, modif, voir , effacer
    switch ($modeFiche)
    {
    case "ajout" :
            $boolSQL = false;
            $txtReadonly = "";
            $nombre_Enregistrements = 1; // pour forcer passage car pas de requete
            break;
    case "effacer" :
            $boolSQL = true;
            $txtReadonly = " READONLY ";
            break;
    case "modif" :
            $boolSQL = true;
            $txtReadonly = "";
            break;
    case "voir" :
            $boolSQL = true;
            $txtReadonly = " READONLY ";
            break;
    default :
            $boolSQL = false;
            $txtReadonly = " READONLY ";
    }

    if ($boolSQL)
    {
        $sqlResult = tabledata_Cde_select($table , $field,"","", $key, $idLigne);
        $nombre_Enregistrements = sql_count($sqlResult); //2.0
    }

    if ($nombre_Enregistrements>0)
    {
        $total = '';
        $hiddens = '';

        if ($boolSQL)
        {
            $tabUnEnregistrement = sql_fetch($sqlResult);
        }
        else
        {
            foreach ($field as $k => $v)
            {
                $tabUnEnregistrement[$k] = "";
            }
        }

        foreach ($field as $k => $v)
        {
          if (array_search($k, $key) == "PRIMARY KEY")
          {
              if ($boolSQL)
              {
                  $strDebut = "Enregistrement ayant comme cl&#233; primaire :<br/><i><b>"
                            .$k."='".$tabUnEnregistrement[$k]."'</b></i><br/>";
              }
          }
          else
          {
              preg_match("/^ *([A-Za-z]+) *(\(([^)]+)\))?(.*DEFAULT *'(.*)')?/", $v, $m);
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
                    preg_match("/^ *'?(.*[^'])'? *$/", $t, $m2); $t = $m2[1];
                  }
              }

              switch (strtoupper($type))
              {
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
                       ." value='1'".$checked.$txtReadonly."/>"
                       ."</td>\n";
                       break;
                  }
                case INT:
                case INTEGER:
                case BIGINT:
                case TINYINT:
                case CHAR:
                case VARCHAR:
                case TEXT:
                case TINYTEXT:
                case TINYBLOB:
                case YEAR:
                case DATETIME:
                case DATE:
                case TIME:
                  $s = "<td>"
                       ."<input type='text'".$s." name='".$k."'"
                       ." value='".htmlentities(utf8_decode($tabUnEnregistrement[$k]), ENT_QUOTES)
                       ."'".$txtReadonly."/>"
                       ."</td>\n";
                  break;
                case ENUM:
                case SET:    //ajout JFM
                  $s = "<td><select name='".$k."'".$txtReadonly.">\n";
                  foreach (preg_split("/'? *, *'?/",$t) as $v)
                  {
                     if ($tabUnEnregistrement[$k]==$v)
                     {
                        $s .= "<option selected>".$v."</option>\n";
                     }
                     else
                     {
                        $s .= "<option>".$v."</option>\n";
                     }
                  } //foreach (preg_split("/'? *, *'?/",$t) as $v)
                  $s .= "</select></td>\n";
                  break;
                case TIMESTAMP:
                  $s = '';
                  if ($mode=="ajout")
                  {
                        $hiddens .= "<input type='hidden' name='".$k."' value='NOW()'/>\n";
                  }
                  else
                  {
                        $hiddens .= "<input type='hidden' name='".$k."' value='".$v."'/>\n";
                  }
                  break;
                case LONGBLOB:
                  $s = "<td><textarea name='$k' cols='45' rows='20'".$txtReadonly.">".htmlentities(utf8_decode($tabUnEnregistrement[$k]), ENT_QUOTES )."</textarea></td>\n"; //modif. JFM
                  break;
                default:
                  $t = floor($t / 45)+1;
                  $s = "<td><textarea name='$k' cols='45' rows='$t'".$txtReadonly.">".htmlentities(utf8_decode($tabUnEnregistrement[$k]), ENT_QUOTES )."</textarea></td>\n";
                  break;
              } //switch (strtoupper($type))
              if ($s)
                $total .= "<tr><td>$k</td>\n$s</tr>\n";
          }
        }
        $hiddens .= "<input type='hidden' name='serveur' value='".$serveur."'/>\n";
        $hiddens .= "<input type='hidden' name='table' value='".$table."'/>\n";
        $hiddens .= "<input type='hidden' name='mode' value='".$mode."'/>\n";


        // $idLigne = htmlentities(stripcslashes($idLigne), ENT_QUOTES );
        $idLigne = htmlentities($idLigne, ENT_QUOTES );

        switch ($modeFiche)
        {
        case "ajout" :
                $txtbouton ="Ajouter";
                break;
        case "effacer" :
                $hiddens .= "<input type='hidden' name='id_ligne' value='".$idLigne."'/>\n";
                $hiddens .= "<input type='hidden' name='tdaction' value='ordresuplig'/>\n";
                $txtbouton ="Effacer d&#233;finitivement";
                break;
        case "modif" :
                $hiddens .= "<input type='hidden' name='id_ligne' value='".$idLigne."'/>\n";
                $hiddens .= "<input type='hidden' name='tdaction' value='maj'/>\n";
                $txtbouton ="Modifier";
                break;
        case "voir" :
                $hiddens .= "<input type='hidden' name='id_ligne' value='".$idLigne."'/>\n";
                $hiddens .= "<input type='hidden' name='tdaction' value='AUCUN'/>\n";
                $txtbouton ="--";
                break;
        default:
                $hiddens .= "<input type='hidden' name='tdaction' value='AUCUN'/>\n";
                $txtbouton ="AUCUN";
        }

        return "\n\n\n".tabledata_url_generer_post_ecrire(
                                 'tabledata'
                               , "<table>\n".$strDebut.$total
                                 ."</table>".$hiddens,$txtbouton);
    } // if ($nombre_Enregistrements>0)

} // function tabledata_Fiche


//===========================================================
function tabledata_Cde_inserer($table, $serveur)
{
  unset($_POST['table']);
  unset($_POST['serveur']);
  unset($_POST['mode']);
  unset($_POST['exec']);
  $n = array();
  $i = array();
  $insertvalues = array();

  foreach ($_POST as $k => $v)
  {
        if ($k!="debrid" && ($v != 'NOW()'))
        {
            $insertvalues [$k] = $v ;
        }
  }

  $r = sql_insertq($table,$insertvalues );

  return "Insertion dans la table ".$table." "
                .(!$r ? '' : ("sous le numero: ".$r));
}


//===========================================================
function tabledata_Cde_miseajour($table , $field, $key, $serveur, $idWhere)
{
  unset($_POST['table']);
  unset($_POST['serveur']);
  unset($_POST['mode']);
  unset($_POST['exec']);

    $tabQueryvalue=array();
    foreach ( $field as $cle=>$txtChamp )
    {
        if ( $key['PRIMARY KEY']!=$cle )
        {
            if ( isset($_POST[$cle]) )
            $tabQueryvalue [$cle] = $_POST[$cle] ;
        }
    } // foreach $field

    $idWhere = utf8_decode($idWhere);

    //tabledata_debug("Nbre lignes en retour de la query ci-dessous :".sql_countsel($table,$idWhere)." - ".sql_get_select("*",$table,$idWhere));

    $retourQuery = sql_updateq ($table, $tabQueryvalue , $idWhere);

    return "" .
        (!$retourQuery ?
           '<br/><b>Erreur dans la requete &#224; la base...</b><br/>'
           : ("<br/>Modification de la ligne : ".$idWhere
               .", dans la table ".$table."<br/>"));
}

//===========================================================
function tabledata_Cde_effacer($table , $field, $key, $serveur, $idWhere)
{
  unset($_POST['table']);
  unset($_POST['serveur']);
  unset($_POST['mode']);
  unset($_POST['exec']);


    $idWhere = utf8_decode($idWhere);

    //tabledata_debug("Nbre lignes en retour de la query ci-dessous :".sql_countsel($table,$where)." - ".sql_get_select("*",$table,$where));

    $retourQuery = sql_delete($table, $idWhere);

    return "" .
        (!$retourQuery ?
           '<br/><b>Erreur dans la requete &#224; la base...</b><br/>'
           : ("<br/>dans la table <b>".$table."</b>, la ligne : <b>".$idWhere
               ."</b> est effac&#233;e<br/>"));
}


//===========================================================
//===========================================================
//
function tabledata_Cadre_InfoInserer($table, $serveur)
{
    // Afficher info concernant insertion
    echo "<br>";
    echo gros_titre(_T('tabledata:insertion')." : <I>'".$table."'</I>",'',false);
    echo debut_cadre_relief("../"._DIR_PLUGIN_TABLEDATA."/img_pack/tabledata-add.gif",true,'',"");
    echo "<br/>",tabledata_Cde_inserer($table, $serveur),"<br/><br/>";
    echo fin_cadre_relief(true);
}

//===========================================================
//===========================================================
//
function tabledata_Cadre_InfoEffacer($table, $serveur, $field, $key,$idLigne)
{
    echo "<br>";
    echo gros_titre("Effacer dans : <I>'".$table."'</I>",'',false);
    echo debut_cadre_relief("../"._DIR_PLUGIN_TABLEDATA."/img_pack/tabledata.gif");
    echo "<br/>".tabledata_Cde_effacer($table, $serveur, $field, $key,$idLigne)."<br/>";
    echo fin_cadre_relief(true);
}



//===========================================================
//===========================================================
//
function tabledata_Cadre_InfoModifier($table, $serveur, $field, $key,$idLigne)
{
    echo "<br>";
    echo gros_titre("Modification : <I>'".$table."'</I>",'',false);
    echo debut_cadre_relief("../"._DIR_PLUGIN_TABLEDATA."/img_pack/tabledata.gif");
    echo "<br/>".tabledata_Cde_miseajour($table, $serveur, $field, $key,$idLigne)."<br/>";
    echo fin_cadre_relief(true);
}


//===========================================================
//===========================================================
//
function tabledata_Cadre_Supprimer($table, $serveur, $field, $key, $idLigne)
{
    echo "<br>";
    echo gros_titre("Suppression dans : <I>'".$table."'</I>",'',false);
    echo debut_cadre_relief("../"._DIR_PLUGIN_TABLEDATA."/img_pack/tabledata-del.gif");
    echo tabledata_Fiche($table, $serveur, $field, $key , $idLigne,"effacer");
    echo fin_cadre_relief(true);
    echo "<center><a href='"
                      , tabledata_url_generer_ecrire('tabledata',"table=".$table)
                      , "'>- Annuler -</a></center>";
}

//===========================================================
//===========================================================
//
function tabledata_Cadre_Lister($table, $serveur, $field, $key
                                 , $boolFntModif,$trisens,$trival,$intPremierEnreg)
{
    echo "<br>";
    echo gros_titre('Info sur la table : <I>'.$table.'</I>','',false);
    echo "<p>";
    echo debut_cadre_relief("../"._DIR_PLUGIN_TABLEDATA."/img_pack/tabledata.gif");

    echo tabledata_Liste_preparedonnees($table, $serveur, $field, $key
                               ,$boolFntModif,$trisens,$trival,$intPremierEnreg);
    echo fin_cadre_relief(true);
}


//===========================================================
//===========================================================
//
function tabledata_Cadre_Ajouter($table, $serveur, $field, $key)
{
    echo gros_titre('<br/>Ajouter un nouvel enregistrement :','',false);
    echo "<p>";
    echo debut_cadre_relief("../"._DIR_PLUGIN_TABLEDATA."/img_pack/tabledata-add.gif");
//    echo table_extra_get($table, $serveur, $field, $key);
    echo tabledata_Fiche($table, $serveur, $field, $key , $idLigne, $mode ="ajout");
    echo fin_cadre_relief(true);
}

//===========================================================
//===========================================================
//
function tabledata_Cadre_Modifier($table, $serveur, $field, $key , $idLigne)
{
    echo gros_titre('<br/>Modifier un enregistrement :','',false);
    echo "<p>";
    echo debut_cadre_relief("../"._DIR_PLUGIN_TABLEDATA."/img_pack/tabledata.gif");
    echo tabledata_Fiche($table, $serveur, $field, $key , $idLigne,"modif");
    echo fin_cadre_relief(true);
    echo "<center><a href='"
          , tabledata_url_generer_ecrire('tabledata',"table=".$table)
          , "'>- Retour &#224; la liste -</a></center>";
}

// EXPORT
function tabledata_cadre($table){
	 // Afficher info concernant export des données 
	echo "<br>";
    echo gros_titre(_T('tabledata:export')." <em>".$table."</em>",'',false);
    echo debut_cadre_relief("../"._DIR_PLUGIN_TABLEDATA."/img_pack/tabledata-add.gif",true,'',"");
    echo "<br />";
	echo tabledata_url_generer_post_ecrire(
											'export_table',
											'Exporte la table <strong><input type="hidden" name="table_exporter" value="'.$table.'" /></strong> au format csv.', 
											'Exporter Table',''
											);	
	
	echo "(en cours de test. Commentaires bienvenus.)";
    echo fin_cadre_relief(true);
}
// FIN DE EXPORT




//===========================================================
//===========================================================
//
function tabledata_Cadre_voirlestables ($serveur,$boolSPIP=false)
{
    global $connect_statut, $spip_lang, $connect_id_auteur;

    $tables_extra = array();
    $tables_spip = array();
    $intNbExtra = 0 ;

    $sqlResultTables = sql_showbase('%');
    while ($tabNomTable = sql_fetch($sqlResultTables))
    {
       foreach ($tabNomTable as $key => $val)
       {
          if (preg_match('#^'.tabledata_table_prefix().'_#', $val))
          {
             $tables_spip[] = $val;
          }
          else
          {
             $tables_extra[] = $val;
             ++$intNbExtra ;
          }
       }
    }

    // affichage
    echo "Choisir une table Extra parmis celle ci-dessous:\n";
    if ($intNbExtra<1)
    {
        echo "Aucune table extra ne semble disponible.";
    }
    else
    {
        echo tabledata_table_HTML ($tables_extra);
    }
    if ($boolSPIP)
    {
        echo "<hr/>";
        echo "Les tables de SPIP :\n";
        echo tabledata_table_HTML ($tables_spip);
    }
    return;
} // function tabledata_Cadre_voirlestables

//-------------------------
function tabledata_table_HTML ($tabtables)
{
    $intNb=1;
    $txtHTML = "<table>\n";
    foreach ($tabtables as $nomtable)
    {
        $txtHTML .= "\t<tr><td>".$intNb++
                   .". <a href='"
                   .tabledata_url_generer_ecrire('tabledata',"table=".$nomtable)
                   ."'>"
                   .$nomtable
                   ."</a></td></tr>\n";
    } //while
    $txtHTML .= "</table>\n";
    return $txtHTML;
}


//-------------------------
function  tabledata_url_generer_ecrire ($fonction,$txt_var_get, $boolVoirSPIP=false)
{
    global $boolDebrid;

    if ($boolVoirSPIP != "masquer")
    {
        if ($boolVoirSPIP=="voir" || $boolDebrid)
            $txt_var_get= $txt_var_get."&debrid=aGnFJwE" ;
    }
    return generer_url_ecrire ($fonction,$txt_var_get);
}


//-------------------------
//function  tabledata_url_generer_post_ecrire ($fonction,$txt_var_post, $boolVoirSPIP=false)
function  tabledata_url_generer_post_ecrire ($fonction,$txt_var_post, $txtbtnsubmit="Enregistrer",$boolVoirSPIP=false)
{
    global $boolDebrid;

    if ($boolVoirSPIP != "masquer")
    {
        if ($boolVoirSPIP=="voir" || $boolDebrid)
        {
            $txt_var_post .= "<input type='hidden' name='debrid' value='aGnFJwE'/>\n";
        }
    }
    //    if ($boolVoirSPIP || $boolDebrid) $txt_var_post= $txt_var_post."&debrid=aGnFJwE" ;
//    return generer_url_post_ecrire ($fonction,$txt_var_post);
    return generer_form_ecrire($fonction, $txt_var_post,"",$txtbtnsubmit);
}


//===========================================================
//===========================================================
//
//function exec_tabledata_dist()
function exec_tabledata()
{
/*    global $intPremierEnreg, $options, $spip_lang_right, $visiteurs, $connect_id_auteur
         , $connect_statut,   $connect_toutes_rubriques;
*/
    global $boolDebrid;

    // récupérer les données de la requete HTTP
    $table = _request('table');
    $intPremierEnreg = _request('intPremierEnreg');
    $serveur = _request('serveur');
    $mode = _request('mode');
    $idLigne = _request('id_ligne');
    $trisens = _request('trisens');
    $trival = _request('trival');
    $get_Debrid = _request('debrid');
    if ($get_Debrid=="aGnFJwE") $boolDebrid=true;

    // Vérifier login : Si administrateur = OK
    if (!autoriser('administrer','zone',0))
    {
        echo minipres();
        exit;
    }

    $commencer_page = charger_fonction('commencer_page', 'inc');
    echo $commencer_page(_T('tabledata:tabledata'));

    // Affichage menu de la page
    echo debut_gauche('TableDATA', true);

    echo debut_boite_info(true);
        // Par la suite, c'est ici qu'il faudrait inscrire les messages pour l'utilisateur...
        // mais il faut remanier tout le code.
    echo "<p class='arial1'>"
           ,"<b>Cde & Informations...</b>"
           ,"<hr/>"
           ,"<a href='"
           , tabledata_url_generer_ecrire('tabledata',"table")
           , "'>- Voir toutes les tables -</a>";

    echo "<hr/>";
    echo "table s&#233;lectionn&#233;e:<br/>";
    echo "<center><b>";
    if ($table=="")
    {
        echo "Aucune n'est s&#233;lectionn&#233;e";
    }
    else
    {
        echo "<a href='",tabledata_url_generer_ecrire('tabledata',"table=".$table)."'>"
               ,$table,"</a>";
    }
    echo "</b></center>";

    // Afficher "Voir tables SPIP"
    if (autoriser('webmestre'))
    {
        if ($boolDebrid)
        {
            $texte_tablespip= "<hr/>"
                     ."<a href='"
                     .tabledata_url_generer_ecrire('tabledata',"table","masquer")
                     ."'>SPIP</a>"
                     ." : Masquer les tables SPIP. (pr&#233;fixe : ".tabledata_table_prefix().")<br/>";
        }
        else
        {
            $texte_tablespip=  "<hr/>"
                                      ."<a href='"
                                      .tabledata_url_generer_ecrire('tabledata',"table","voir")
                                      ."'>SPIP</a>"
                                      ." : Voir les tables SPIP. (pr&#233;fixe : ".tabledata_table_prefix().")<br/>";
        }
        echo $texte_tablespip;
    }
    //    echo propre(_T('tabledata:tabledata'));
    echo fin_boite_info(true);


    // Par la suite, c'est ici qu'il faudrait mettre l'aide des commandes.
    $texte_bloc_des_raccourcis = "<p class='arial1'>"
           ."<b>LISTER</b> : Affichage par groupe de 20 enregistrements."
           ." (Tri&#233;s sur la clef primaire si elle existe)"
           ."<br/><br/><u>Les Qq Commandes</u><br/>"
           ."<br/><b>TRIER</b> : Cliquer sur l'icone ^v dans l'ent&#234;te du champ"
           ." (bascule &#224; chaque clic Ascendant, Decsendant, Aucun)<br/>"
           ."<br/><b>AJOUTER</b> : Pour ajouter un nouvel enregistrement, voir le formulaire du bas de page<br/>"
           ."<br/><b>SUPPRIMER</b> : Lister le contenu de la table et cliquer sur la croix rouge &#224; gauche de la ligne.<br/>"
           ."<br/><b>MODIFIER</b> : Cliquer sur l'enregistement<br/>";


    echo bloc_des_raccourcis($texte_bloc_des_raccourcis);

    // Afficher page centrale
    echo debut_droite('TableDATA', true);

    // ======== Teste si le nom de la table est mentionné
    if ($table=="")
    {
        if ($boolDebrid)
        {
                echo gros_titre('<br/>ATTENTION :','',false);
                echo "La modification du contenu des tables de SPIP peut engendrer"
                      ," des dysfontionnements graves.<br/>"
                      ,"Faite une sauvegarde de la base, elle "
                      ,"permettra une r&#233;paration en cas de probl&#232;me.<br/>";
        }
        echo gros_titre('<br/>Choisir une table :','',false);
        echo debut_cadre_relief("../"._DIR_PLUGIN_TABLEDATA."/img_pack/tabledata.gif",true,'','');
        echo "Le nom de la table est manquant.<br/><br/>";
        echo tabledata_Cadre_voirlestables ($serveur,$boolDebrid);
        echo fin_cadre_relief(true);
        tabledata_Page_fin();
        exit;
    } // if ($table=="")

    // ======== Recherche si la table existe ? ===========
    if (!preg_match('/^[\w_]+$/', $table))
    {
        echo debut_cadre_relief("../"._DIR_PLUGIN_TABLEDATA."/img_pack/tabledata.gif");
        echo "'".$table."': nom incorrect";
        echo fin_cadre_relief(true);
       echo "<center><a href='"
                      , tabledata_url_generer_ecrire('tabledata',"table")
                      , "'>- Retour &#224; la liste des tables -</a></center>";
        tabledata_Page_fin();
        exit;
    } // if (!preg_match('/^[\w_]+$/', $table))

    if (strpos($table,tabledata_table_prefix(),0)===0) // cas d'une table SPIP
    {
        if ($boolDebrid)
        {   // cas table SPIP autorisée
            echo gros_titre('<br/>ATTENTION :','',false);
            echo "Vous intervenez sur une table interne de SPIP."
                  ," Agissez avec pr&#233;cautions.<br/>";
        }
        else
        {   // protection active et table SPIP !!
            echo debut_cadre_relief("../"._DIR_PLUGIN_TABLEDATA."/img_pack/tabledata.gif");
            echo "<br/>Le param&#232;tre <i>table</i> (valeur: '"
              ,$table
              ,"') indique une table prot&#233;g&#233;e de SPIP.<br/><br/>";
            echo fin_cadre_relief(true);
            echo "<center><a href='"
                          , tabledata_url_generer_ecrire('tabledata',"table")
                          , "'>- Retour &#224; la liste des tables -</a></center>";
            tabledata_Page_fin();
            exit;
        }
    } // if (!$description)

    $description = sql_showtable($table, $serveur);

    $table2 = "";
    if ($boolDebrid && !$description)
    {
        // recherche avec l'extention définie pour SPIP tabledata_table_prefix()
        $table2 = tabledata_table_prefix().'_'. $table;
        $description = sql_showtable($table2, $serveur);
    } // if (!$description)

    spip_log("description ".$description);
    $field = $description['field'];
    $key = $description['key'];
    //$intClefPrimaireNbChamp= (count($key)>0?count(explode(",",$key["PRIMARY KEY"])):0);

    $intNbClef = count($key);
    if (array_key_exists("PRIMARY KEY",$key))
    {
        $intClefPrimaireNbChamp= count(explode(",",$key["PRIMARY KEY"]));
    }
    else
    {
        $intClefPrimaireNbChamp = 0 ;
    }

    //tabledata_debug("Nombre de clef :".$intNbClef." Primaire : ".$intClefPrimaireNbChamp, $key) ;

    //    if (! ($field && $key))
    if (! ($field))
    {
        // la table n'existe pas !!
        echo debut_cadre_relief("../"._DIR_PLUGIN_TABLEDATA."/img_pack/tabledata.gif");
        echo "Le param&#232;tre <i>table</i> (valeur: '"
          ,$table
          ,"') n'indique pas une table exploitable par le serveur SQL ",$serveur;
        echo fin_cadre_relief(true);
        echo "<center><a href='"
                      , tabledata_url_generer_ecrire('tabledata',"table")
                      , "'>- Retour &#224; la liste des tables -</a></center>";
    } // if (! ($field && $key))
    else
    {
       // la table existe.
        if ($table2) $table = $table2;

        switch ($intClefPrimaireNbChamp)
        {
            case 0 :
            // Il n'y a pas de clef primaire => désactiver modification...
                // désactiver modif
                $boolFntAjout = true;
                $boolFntModif = true ; // false;
                $txtMsgInfoTable= "<br/>Votre table ne contient pas de clef primaire. "
                                 ."<I>La modification des enregistrements est <!--d&#233;s-->activ&#233;e"
                                 ."<I>(&#224; vos risques et p&#233;rils, surtout si plusieurs enregistrement sont &#233;gaux)"
                                 ."</i><br/>";

                foreach($field as $k=>$d)
                {
                    $key["PRIMARY KEY"][] = $k;
                }

            break;

            case 1 :
            // il y a une clé primaire sur champ unique =OK
                if (strpos(strtoupper($field[$key["PRIMARY KEY"]]),"AUTO_INCREMENT",0)===False)
                {
                    // Si pas d'autoincrement : désactiver ajout
                    // $boolFntAjout = false;
                    $txtMsgInfoTable= "<br/>La clef primaire de la table n'est pas"
                                       ." autoincr&#233;ment&#233;e. "
                             //."<I>(L'insertion d'un nouvel enregistrement est d&#233;sactiv&#233;e)"
                             ."</i><br/>";
                } //if (strpos($field[$key["PRIMARY KEY"]]
                // else
                // {
                    // Si autoincrement : Activer ajout
                $boolFntAjout = true;
                $txtMsgInfoTable= "";
                //} //if (strpos($field[$key["PRIMARY KEY"]]
                $boolFntModif = true;
                $key["PRIMARY KEY"] = explode(",",$key["PRIMARY KEY"]);
            break;

            default :
                // il y a une clé primaire sur champs multiple
                //$boolFntAjout = false;
                $boolFntAjout = true;
                $boolFntModif = true;

                //$txtMsgInfoTable= "<br/>La clef primaire contient plusieurs champs: ".$key["PRIMARY KEY"]
                //             ."<br/><I>(L'insertion est d&#233;sactiv&#233;e)</i><br/>";
                $txtMsgInfoTable= "<br/>La clef primaire contient plusieurs champs: ".$key["PRIMARY KEY"];

                $key["PRIMARY KEY"] = explode(",",$key["PRIMARY KEY"]);
        } //switch ($intClefPrimaireNbChamp)

        // CHOIX DE L'ACTION A REALISER => de l'affichage

        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            // la page est arrivée en POST
            switch ($_POST['tdaction'])
            {
            case "ordresuplig" :
                // la page est arrivée en POST avec action=ordresuplig
                //  ==> Demande d'effacer la fiche (enregistrement)
                tabledata_Cadre_InfoEffacer($table , $field, $key, $serveur, $idLigne) ;
                echo "<center><a href='"
                      , tabledata_url_generer_ecrire('tabledata',"table")
                      , "'>- Retour &#224; la liste des tables -</a></center>";
                echo $txtMsgInfoTable;
                tabledata_Cadre_Lister($table, $serveur, $field, $key, $boolFntModif,$trisens,$trival,$intPremierEnreg);
                if ($boolFntAjout) tabledata_Cadre_Ajouter($table, $serveur, $field, $key);
                echo "<center><a href='"
                      , tabledata_url_generer_ecrire('tabledata',"table")
                      , "'>- Retour &#224; la liste des tables -</a></center>";
                break;
            case "maj" :
                // la page est arrivée en POST avec action=maj
                //  ==> Demande Enregistrement des valeurs.
                tabledata_Cadre_InfoModifier($table , $field, $key, $serveur, $idLigne) ;
                echo "<center><a href='"
                      , tabledata_url_generer_ecrire('tabledata',"table")
                      , "'>- Retour &#224; la liste des tables -</a></center>";
                echo $txtMsgInfoTable;
                tabledata_Cadre_Lister($table, $serveur, $field, $key, $boolFntModif,$trisens,$trival,$intPremierEnreg);
                if ($boolFntAjout) tabledata_Cadre_Ajouter($table, $serveur, $field, $key);
                echo "<center><a href='"
                      , tabledata_url_generer_ecrire('tabledata',"table")
                      , "'>- Retour &#224; la liste des tables -</a></center>";
                break;
            default:
                // la page est arrivée en POST sans action ou autre
                //  ==> Demande Insertion des valeurs.
                tabledata_Cadre_InfoInserer($table, $serveur);
                // Afficher la liste
                echo "<center><a href='"
                      , tabledata_url_generer_ecrire('tabledata',"table")
                      , "'>- Retour &#224; la liste des tables -</a></center>";

                echo $txtMsgInfoTable;
                $intPremierEnreg = "-1";
                tabledata_Cadre_Lister($table, $serveur, $field, $key, $boolFntModif,$trisens,$trival,$intPremierEnreg);
                // Afficher cadre Ajout
                if ($boolFntAjout) tabledata_Cadre_Ajouter($table, $serveur, $field, $key);
                echo "<center><a href='"
                      , tabledata_url_generer_ecrire('tabledata',"table")
                      , "'>- Retour &#224; la liste des tables -</a></center>";
                break;
            } // switch ($_POST['tdaction'])
        }  // if ($_SERVER['REQUEST_METHOD'] == 'POST')
        else
        {
            // la page n'est pas arrivée en POST => en GET
            switch ($_GET['tdaction'])
            {
            case "edit" :
                // avec action=maj
                //  ==> Affichage formulaire de modification
                tabledata_Cadre_Modifier($table, $serveur, $field, $key, $idLigne);
                break;
            case "suplig" :
                // avec action=sup
                //  ==> Affichage formulaire de modification

                tabledata_Cadre_Supprimer($table, $serveur, $field, $key, $idLigne);
                break;
            default :
                // sans action ou autre
                //  ==> Affichage de la liste
                echo "<center><a href='"
                      , tabledata_url_generer_ecrire('tabledata',"table")
                      , "'>- Retour &#224; la liste des tables -</a></center>";
                echo $txtMsgInfoTable;
                echo tabledata_Cadre_Lister($table, $serveur, $field, $key, $boolFntModif,$trisens,$trival,$intPremierEnreg);
                if ($boolFntAjout) tabledata_Cadre_Ajouter($table, $serveur, $field, $key);
                echo "<center><a href='"
                      , tabledata_url_generer_ecrire('tabledata',"table")
                      , "'>- Retour &#224; la liste des tables -</a></center>";
                break;
            } // switch ($_GET['action'])
       } // if ($_SERVER['REQUEST_METHOD'] == 'POST')
    } // if (! ($field && $key))

	// inclusion du bouton d'export
	tabledata_cadre($table);

    tabledata_Page_fin();

} // function exec_tabledata_dist()









function tabledata_debug($txt = NULL, $tab = NULL, $commentaire = false )
{
    echo "\n\n",($commentaire)?"<!--":($txt)?"<hr/>":"","\n\n";
    echo $txt."\n";
    if ($tab)
    {
         echo "<hr/>Array=".$tab."<br/>\n\n";
         print_r($tab);
    }
    echo "\n\n",($commentaire)?"-->":"<hr/>","\n\n";
}

function tabledata_table_prefix()
{
    return $GLOBALS["connexions"][0]["prefixe"];
}



?>