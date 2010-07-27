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
// adaptation du PLUGIN - TABLE DATA - V2.2.1
//                                         CONTRIBUTION POUR SPIP 2.0
// 25 mai 2009                          Christophe BOUTIN - Opalys.info
//=====================================================================
############################################################################

//=========================================================================
//=========================================================================
//
function table_amap_lister($table, $serveur, $field, $key, $intPremierEnreg)
{
    $intPremierEnreg = intval($intPremierEnreg);
    $max_par_page = 20;

    $sqlResult = requete_tableamap($table, $field, $key);
    $nombre_Enregistrements = sql_count($sqlResult);

	if ($intPremierEnreg <0 )
	{
		$intPremierEnreg = ((int)($nombre_Enregistrements/$max_par_page))*$max_par_page;
	}

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
            if ($i>=$intPremierEnreg AND $i<$intPremierEnreg+$max_par_page)
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

            if ($j == $intPremierEnreg)
                echo "<b>$j</b>";
            else if ($j > 0)
              echo "<a href='"
                    , amap_url_generer_ecrire($page,"intPremierEnreg=".$j."&table=".$table)
                    , "'>$j</a>";
            else
              echo "<a href='"
                    , amap_url_generer_ecrire($page,"table=".$table)
                    , "'>0</a>";

            if ($intPremierEnreg > $j  AND $intPremierEnreg < $j+$max_par_page)
            {
                echo " | <b>$intPremierEnreg</b>";
            }

        }
        echo "</td></tr>\n"; // fin de ligne
        echo "<tr height='5'></tr>"; // ligne espacement
    }

    afficher_n_enregistrements_amap( $tabLesEnregistrements, $table, $key);

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
				, tabledata_url_generer_ecrire($page
				, "intPremierEnreg=".$intPremierEnreg_prec."&table=".$table)
				, "' title='Voir la liste pr&#233;c&#233;dente des enregistrements' >"
				, "\n<input type='submit' value='&lt;&lt;&lt;' class='fondo' />"
				, "</a>";
        }

        echo "</td><td style='text-align: $spip_lang_right'>";

        if ($intPremierEnreg_suivant < $nombre_Enregistrements)
        {
			echo "<a href='"
				, amap_url_generer_ecrire($page
				, "tri=".$tri."&intPremierEnreg=".$intPremierEnreg_suivant."&table=".$table)
				, "' title='Voir la suite des enregistrements' >"
				,"\n<input type='submit' value='&gt;&gt;&gt;' class='fondo' />"
				,"</a>";
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

	$leschamps = array();
	$lestables = $table;
	$clauseWhere = array();
	$clauseGroupby = array();
	$clauseOrderby = array();
	$clauseLimit = array();
	$clauseHaving = array();

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

	echo ($nbenreg==1)?"":" Nombre(s) d'enregistrement dans la s&#233;lection : ".$nbenreg."<hr/>";

	return $Rselect;

} //function // fin function requete_tableamap


//=========================================================================
//=========================================================================
// Le nombre de champs à afficher est dynamique.
// Par contre l'Identifiant (Id Ligne) doit être le premier champ à gauche
function afficher_n_enregistrements_amap($tabLesEnregistrements, $table, $key)
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
					,amap_url_generer_ecrire($page
					,"tdaction=suplig&id_ligne=".$idLigne."&table=".$table)  // limitation 1
					, "'>"
					,"</a>";
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
        $sqlResult = requete_tableamap($table , $field,"","", $key, $idLigne);
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

        return "\n\n\n".amap_url_generer_post_ecrire($page
                               , "<table>\n".$strDebut.$total
                                 ."</table>".$hiddens,$txtbouton);
    } // if ($nombre_Enregistrements>0)

} // function tabledata_Fiche



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
          $intPremierEnreg = "<i>'".$k."'</i> est la cl&#233; primaire de la table <i>'".$table."'</i><br/>";
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
  return amap_url_generer_post_ecrire($page
                           , "table=".$table."&serveur=".$serveur."&mode=".$mode)
                             ."<table>\n".$intPremierEnreg.$total
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

//-------------------------
function amap_url_generer_ecrire ($fonction,$txt_var_get, $boolVoirSPIP=false)
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
//function  amap_url_generer_post_ecrire ($fonction,$txt_var_post, $boolVoirSPIP=false)
function amap_url_generer_post_ecrire ($fonction,$txt_var_post, $txtbtnsubmit="Enregistrer",$boolVoirSPIP=true)
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
    return generer_form_ecrire ($fonction, $txt_var_post,"",$txtbtnsubmit);
}
?>
