<?php
##########################################################################
#
#           Fichier des fonctions produits du plugin Amap pour SPIP
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
function table_amap_lister_produit_distributions($idSaison)
{
  $out = '';
  $hiddens = '';

  pipeline('exec_init',array('args'=>array('exec'=>'amap_distributions'),'data'=>''));

  if ($idSaison!=0)
  { // on affiche l'ensemble des distributions de la saison
    // 1er boucle sur la table spip_amap_produit_distribution
    $txtQuery = "SELECT DATE_FORMAT(e.date_evenement, '%d-%m-%Y') As dateEvenement, d.id_evenement, d.id_produit";
    $txtQuery .= " FROM spip_amap_produit_distribution d, spip_amap_evenements e";
    $txtQuery .= " WHERE e.id_evenement=d.id_evenement";
    $txtQuery .= " AND e.id_saison=".$idSaison;
    $txtQuery .= " ORDER BY e.date_evenement";
    $sqlResult_1 = sql_query($txtQuery);

    $out .= "\t<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
    $out .= "\t\t<tr bgcolor='#DBE1C5'>\n";
    $out .= "\t\t\t<td><strong><center>date distribution</center></strong></td>\n";
    $out .= "\t\t\t<td><strong>produit</strong></td>\n";
    $out .= "\t\t\t<td>&nbsp;</td>\n";
    $out .= "\t\t</tr>\n";

    while ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))
    {
      $out .= "\t\t<tr style='background-color: #eeeeee;'>\n";
      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_distributions', "action=modif&idDistrib=".$tabUnEnregistrement_1['id_evenement']."&idSaison=".$idSaison."&idProduit=".$tabUnEnregistrement_1['id_produit']."&table=spip_amap_produit","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement_1['dateEvenement']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_distributions', "action=modif&idDistrib=".$tabUnEnregistrement_1['id_evenement']."&idSaison=".$idSaison."&idProduit=".$tabUnEnregistrement_1['id_produit']."&table=spip_amap_produit","ecrire")." '>\n";

      // on recherche le label du produit
      $txtQuery = "SELECT label_produit FROM spip_amap_produit ";
      $txtQuery .= "WHERE id_produit=".$tabUnEnregistrement_1['id_produit'];
      $sqlResult_2 = sql_query($txtQuery);

      if ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
        $out .= "\t\t\t\t\t".$tabUnEnregistrement_2['label_produit']."\n";
      else
        $out .= "\t\t\t\t\t".$tabUnEnregistrement_1['id_produit']."\n";

      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_distributions', "action=suppr&idDistrib=".$tabUnEnregistrement_1['id_evenement']."&idSaison=".$idSaison."&idProduit=".$tabUnEnregistrement_1['id_produit']."&table=spip_amap_produit","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center><img src='"._DIR_PLUGIN_AMAP."img_pack/b_drop.png' /></center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t</tr>\n";
      
    } // fin while
    $out .= "\t</table>\n";
  } // fin   if ($idSaison!=0)
  else
  {
    return "choisir une saison";
  }
  
  return $out;

} // function table_amap_lister_produit_distributions


//=========================================================================
//=========================================================================
//
function table_amap_getmodif_produit_distribution()
{
  $out = '';
  $hiddens = '';

  $out .= "\t<tr bgcolor='#DBE1C5'>";
  $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
  $out .= "\t\t\tn° saison: \n";
  $out .= "\t\t</td>\n";
  $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
  $out .= "\t\t\t".$_GET['idSaison']."\n";
  $out .= "\t\t</td>\n";
  $out .= "\t</tr>\n";

  $out .= "\t<tr bgcolor='#DBE1C5'>";
  $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
  $out .= "\t\t\tn° distribution: \n";
  $out .= "\t\t</td>\n";
  $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
  $out .= "\t\t\t".$_GET['idDistrib']."\n";
  $out .= "\t\t</td>\n";
  $out .= "\t</tr>\n";

  $out .= "\t<tr bgcolor='#DBE1C5'>";
  $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
  $out .= "\t\t\tproduit: \n";
  $out .= "\t\t</td>\n";
  $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
 
  // boucle sur la table spip_amap_produit
  $txtQuery = "SELECT * FROM spip_amap_produit";
  $sqlResult = sql_query($txtQuery);

  $out .= "\t\t\t<select name='nouvIdProduit'>\n";
  while ($tabUnEnregistrement = sql_fetch($sqlResult))
  {
    if ($tabUnEnregistrement['id_produit'] == $_GET['idProduit'])
      $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement['id_produit']."'>".$tabUnEnregistrement['label_produit']."</option>\n";
    else
      $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_produit']."'>".$tabUnEnregistrement['label_produit']."</option>\n";
  } // fin while
  $out .= "\t\t\t</select>\n";
  $out .= "\t\t</td>\n";
  $out .= "\t</tr>\n";
 
  $hiddens .= "\t<input type='hidden' name='idSaison' value='".$_GET['idSaison']."' />\n";
  $hiddens .= "\t<input type='hidden' name='idDistrib' value='".$_GET['idDistrib']."' />\n";
  $hiddens .= "\t<input type='hidden' name='ancIdProduit' value='".$_GET['idProduit']."' />\n";

  return generer_url_entite('amap_distributions', "table=spip_amap_produit&action=maj","post_ecrire")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";

} // function table_amap_getmodif_produit_distribution()


//=========================================================================
//=========================================================================
//
function table_amap_get_produit_distribution($idSaison)
{
  $out = '';
  $hiddens = '';

  if ($idSaison!=0)
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tdate distribution: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
 
    // boucle sur la table spip_amap_evenements
    $txtQuery = "SELECT DATE_FORMAT(e.date_evenement, '%d-%m-%Y') As dateEvenement, e.id_evenement FROM spip_amap_evenements e";
    $txtQuery .= " WHERE e.id_saison=".$idSaison;
    $txtQuery .= " AND e.id_evenement NOT IN (";
    $txtQuery .= "  SELECT id_evenement FROM spip_amap_produit_distribution";
    $txtQuery .= "  GROUP BY id_evenement";
    $txtQuery .= "  HAVING count(id_produit) = (";
    $txtQuery .= "    SELECT count(id_produit) FROM spip_amap_produit";
    $txtQuery .= "  ) ";
    $txtQuery .= ") ";
    $txtQuery .= " ORDER BY e.date_evenement";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t\t\t<select name='idDistrib'>\n";

    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_evenement']."'>".$tabUnEnregistrement['dateEvenement']."\n";
    } // fin while
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tproduit: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
 
   // boucle sur la table spip_amap_produit
    $txtQuery = "SELECT * FROM spip_amap_produit";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t\t\t<select name='idProduit'>\n";
    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_produit']."'>".$tabUnEnregistrement['label_produit']."</option>\n";
    } // fin while
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$idSaison."' />\n";

    return generer_url_entite('amap_distributions', "table=spip_amap_produit&action=add","post_ecrire")
                               ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";

  } // fin   if ($idSaison!=0)
  else
  {
    return "choisir une saison";
  }

} // function table_amap_get_produit_distribution()


//=========================================================================
//=========================================================================
//
function table_amap_modif_post_produit_distribution()
{
  $txtQuery = "UPDATE spip_amap_produit_distribution SET ";
  $txtQuery .= "id_produit='".$_POST['nouvIdProduit']."' ";
  $txtQuery .= " WHERE id_evenement=".$_POST['idDistrib'];
  $txtQuery .= " AND id_produit=".$_POST['ancIdProduit'];

  $sqlResult = sql_query($txtQuery);

  return "Mise à jour dans la table spip_amap_produit_distribution " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_POST['idDistrib'].", ".$_POST['ancIdProduit'].") "));
} //function table_amap_modif_post_produit_distribution


//=========================================================================
//=========================================================================
//
function table_amap_post_produit_distribution()
{
  $txtQuery = "INSERT INTO spip_amap_produit_distribution VALUES (";
  $txtQuery .= "'".$_POST['idDistrib']."', ";
  $txtQuery .= "'".$_POST['idProduit']."')";

  $sqlResult = sql_query($txtQuery);

  return "Insertion dans la table spip_amap_produit_distribution " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_POST['idDistrib'].", ".$_POST['idProduit'].") "));
} //function table_amap_post_produit_distribution

//=========================================================================
//=========================================================================
//
function table_amap_suppr_produit_distribution()
{
  $txtQuery = "DELETE FROM spip_amap_produit_distribution";
  $txtQuery .= " WHERE id_evenement=".$_GET['idDistrib'];
  $txtQuery .= " AND id_produit=".$_GET['idProduit'];

  $sqlResult = sql_query($txtQuery);

  return "Suppression dans la table spip_amap_produit_distribution " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_GET['idDistrib'].", ".$_GET['idProduit'].") "));
} //function table_amap_suppr_produit_distribution

?>
