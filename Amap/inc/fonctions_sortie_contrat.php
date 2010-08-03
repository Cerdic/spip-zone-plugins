<?php
##########################################################################
#
#           Fichier des fonctions sorties du plugin Amap pour SPIP
#
#
#             Site de documentation du plugin Amap
#                       http://www.dadaprod.org/amap-spip
#
#                        licence GNU/GPL
#	                 2008 - Stéphane Moulinet
############################################################################

//=========================================================================
//=========================================================================
//
function table_amap_lister_sortie_contrat($idSaison, $idProduit)
{
  $out = '';
  $hiddens = '';

  pipeline('exec_init',array('args'=>array('exec'=>'amap_contrats'),'data'=>''));

  if ( ($idSaison!=0) && ($idProduit!=0) )
  { // on affiche l'ensemble des sorties disponible pour une saison et un produit donné
    $txtQuery = "SELECT id_sortie, DATE_FORMAT(date_sortie, '%d-%m-%Y') As dateSortie FROM spip_amap_sortie";
    $txtQuery .= " WHERE id_produit=".$idProduit;
    $txtQuery .= " AND id_saison=".$idSaison;
    $txtQuery .= " ORDER BY id_sortie";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
    $out .= "\t\t<tr bgcolor='#DBE1C5'>\n";
    $out .= "\t\t\t<td><strong><center>id sortie</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>date sortie</center></strong></td>\n";
    $out .= "\t\t\t<td>&nbsp;</td>\n";
    $out .= "\t\t</tr>\n";

    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $out .= "\t\t<tr style='background-color: #eeeeee;'>\n";
      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idSortie=".$tabUnEnregistrement['id_sortie']."&idSaison=".$idSaison."&idProduit=".$idProduit."&table=spip_amap_sortie","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement['id_sortie']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idSortie=".$tabUnEnregistrement['id_sortie']."&idSaison=".$idSaison."&idProduit=".$idProduit."&table=spip_amap_sortie","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement['dateSortie']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=suppr&idSortie=".$tabUnEnregistrement['id_sortie']."&idSaison=".$idSaison."&idProduit=".$idProduit."&table=spip_amap_sortie","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center><img src='"._DIR_PLUGIN_AMAP."img_pack/b_drop.png' /></center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t</tr>\n";
      
    } // fin while
    $out .= "\t</table>\n";
  } // fin   if ( ($idSaison!=0) && ($idProduit!=0) )
  else if ( ($idSaison!=0) && ($idProduit==0) )
  {
    return "choisir un produit";
  }
  else
  {
    return "choisir une saison et un produit";
  }

  return $out;

} // function table_amap_lister_sortie_contrat


//=========================================================================
//=========================================================================
//
function table_amap_getmodif_sortie_contrat()
{
  $out = '';
  $hiddens = '';

  // on affiche l'ensemble des sorties disponible pour une saison et un produit donné
  $txtQuery = "SELECT date_sortie FROM spip_amap_sortie";
  $txtQuery .= " WHERE id_sortie=".$_GET['idSortie'];
  $sqlResult_1 = sql_query($txtQuery);

  if ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tsaison: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t".$_GET['idSaison']."\n"; 
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tproduit: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";

    // recherche du label du produit
    $txtQuery = "SELECT label_produit FROM spip_amap_produit";
    $txtQuery .= " WHERE id_produit=".$_GET['idProduit'];
    $sqlResult_2 = sql_query($txtQuery);

    if ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
      $out .= "\t\t".$tabUnEnregistrement_2['label_produit']."\n";
    else
      $out .= "\t\t".$_GET['idProduit']."\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tid sortie: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t".$_GET['idSortie']."\n"; 
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tdate sortie (AAAA-MM-JJ): \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input type='text' name='dateSortie' value='".$tabUnEnregistrement_1['date_sortie']."' size='10' maxlength='10' >\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";
 
    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$_GET['idSaison']."' />\n";
    $hiddens .= "\t<input type='hidden' name='idProduit' value='".$_GET['idProduit']."' />\n";
    $hiddens .= "\t<input type='hidden' name='idSortie' value='".$_GET['idSortie']."' />\n";

	return generer_url_ecrire("amap_contrats", "table=spip_amap_sortie", "action=maj")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";
  }

} // function table_amap_getmodif_sortie_contrat()


//=========================================================================
//=========================================================================
//
function table_amap_get_sortie_contrat($idSaison, $idProduit)
{
  $out = '';
  $hiddens = '';

  if ( ($idSaison!=0) && ($idProduit!=0) )
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tdate sortie (AAAA-MM-JJ): \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input type='text' name='dateSortie' value='' size='10' maxlength='10' >\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";
 
    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$idSaison."' />\n";
    $hiddens .= "\t<input type='hidden' name='idProduit' value='".$idProduit."' />\n";

	return generer_url_ecrire("amap_contrats", "table=spip_amap_sortie", "action=add")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";

  } // fin   if ( ($idSaison!=0) && ($idProduit!=0) )
  else if ( ($idSaison!=0) && ($idProduit==0) )
  {
    return "choisir un produit";
  }
  else
  {
    return "choisir une saison et un produit";
  }

} // function table_amap_get_sortie_contrat()


//=========================================================================
//=========================================================================
//
function table_amap_modif_post_sortie_contrat()
{
  $txtQuery = "UPDATE spip_amap_sortie SET ";
  $txtQuery .= "date_sortie='".$_POST['dateSortie']."' ";
  $txtQuery .= " WHERE id_sortie=".$_POST['idSortie'];

  $sqlResult = sql_query($txtQuery);

  return "Mise à jour dans la table spip_amap_sortie " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_POST['idSortie'].") "));
} //function table_amap_modif_post_sortie_contrat


//=========================================================================
//=========================================================================
//
function table_amap_post_sortie_contrat()
{
  $description = array();
  $contenu = array();

  if ($_POST['dateSortie']) { $description[]='date_sortie'; $contenu[]="'".$_POST['dateSortie']."'"; }
  if ($_POST['idSaison']) { $description[]='id_saison'; $contenu[]="'".$_POST['idSaison']."'"; }
  if ($_POST['idProduit']) { $description[]='id_produit'; $contenu[]="'".$_POST['idProduit']."'"; }

  $sqlResult = sql_insert('spip_amap_sortie',
                 "(" . join(', ', $description) . ")",
                 "(" . join(', ', $contenu) . ")");

  return "Insertion dans la table spip_amap_sortie " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: ".$sqlResult." "));
} //function table_amap_post_sortie_contrat

//=========================================================================
//=========================================================================
//
function table_amap_suppr_sortie_contrat()
{
  $txtQuery = "DELETE FROM spip_amap_sortie";
  $txtQuery .= " WHERE id_sortie=".$_GET['idSortie'];

  $sqlResult = sql_query($txtQuery);

  return "Suppression dans la table spip_amap_sortie " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_GET['idSortie'].") "));
} //function table_amap_suppr_sortie_contrat

?>
