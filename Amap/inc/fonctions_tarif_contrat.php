<?php
##########################################################################
#
#           Fichier des fonctions tarifs du plugin Amap pour SPIP
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
function table_amap_lister_tarif_contrat($idSaison, $idProduit)
{
  $out = '';
  $hiddens = '';

  pipeline('exec_init',array('args'=>array('exec'=>'amap_contrats'),'data'=>''));

  if ( ($idSaison!=0) && ($idProduit!=0) )
  { // on affiche l'ensemble des tarifs disponible pour une saison et un produit donné
    $txtQuery = "SELECT t.id_type, t.label_type, p.prix_distribution FROM spip_amap_prix p, spip_amap_type_contrat t";
    $txtQuery .= " WHERE t.id_type=p.id_type";
    $txtQuery .= " AND p.id_produit=".$idProduit;
    $txtQuery .= " AND p.id_saison=".$idSaison;
    $txtQuery .= " ORDER BY p.id_produit";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
    $out .= "\t\t<tr bgcolor='#DBE1C5'>\n";
    $out .= "\t\t\t<td><strong><center>type de contrat</center></strong></td>\n";
    $out .= "\t\t\t<td><strong>prix par distribution</strong></td>\n";
    $out .= "\t\t\t<td>&nbsp;</td>\n";
    $out .= "\t\t</tr>\n";

    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $out .= "\t\t<tr style='background-color: #eeeeee;'>\n";
      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idType=".$tabUnEnregistrement['id_type']."&idSaison=".$idSaison."&idProduit=".$idProduit."&table=spip_amap_prix","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement['label_type']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idType=".$tabUnEnregistrement['id_type']."&idSaison=".$idSaison."&idProduit=".$idProduit."&table=spip_amap_prix","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement['prix_distribution']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=suppr&idType=".$tabUnEnregistrement['id_type']."&idSaison=".$idSaison."&idProduit=".$idProduit."&table=spip_amap_prix","ecrire")." '>\n";
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

} // function table_amap_lister_tarif_contrat


//=========================================================================
//=========================================================================
//
function table_amap_getmodif_tarif_contrat()
{
  $out = '';
  $hiddens = '';

  // on affiche l'ensemble des tarifs disponible pour une saison et un produit donné
  $txtQuery = "SELECT t.label_type, p.prix_distribution FROM spip_amap_prix p, spip_amap_type_contrat t";
  $txtQuery .= " WHERE t.id_type=p.id_type";
  $txtQuery .= " AND p.id_produit=".$_GET['idProduit'];
  $txtQuery .= " AND p.id_saison=".$_GET['idSaison'];
  $txtQuery .= " AND p.id_type=".$_GET['idType'];
  $sqlResult = sql_query($txtQuery);

  if ($tabUnEnregistrement = sql_fetch($sqlResult))
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\ttype de contrat: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t".$tabUnEnregistrement['label_type']."\n"; 
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tprix par distribution: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input type='text' name='prixDistribution' value='".$tabUnEnregistrement['prix_distribution']."' size='5' maxlength='5' >\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";
 
    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$_GET['idSaison']."' />\n";
    $hiddens .= "\t<input type='hidden' name='idProduit' value='".$_GET['idProduit']."' />\n";
    $hiddens .= "\t<input type='hidden' name='idType' value='".$_GET['idType']."' />\n";

	return generer_url_ecrire("amap_contrats", "table=spip_amap_prix", "action=maj")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";
  }

} // function table_amap_getmodif_tarif_contrat()


//=========================================================================
//=========================================================================
//
function table_amap_get_tarif_contrat($idSaison, $idProduit)
{
  $out = '';
  $hiddens = '';

  if ( ($idSaison!=0) && ($idProduit!=0) )
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\ttype de contrat: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
 
   // boucle sur la table spip_amap_type_contrat
    $txtQuery = "SELECT * FROM spip_amap_type_contrat";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t\t\t<select name='idType'>\n";
    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_type']."'>".$tabUnEnregistrement['label_type']."</option>\n";
    } // fin while
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tprix par distribution: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input type='text' name='prixDistribution' value='' size='5' maxlength='5' >\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$idSaison."' />\n";
    $hiddens .= "\t<input type='hidden' name='idProduit' value='".$idProduit."' />\n";

	return generer_url_ecrire("amap_contrats", "table=spip_amap_prix", "action=add")
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

} // function table_amap_get_tarif_contrat()


//=========================================================================
//=========================================================================
//
function table_amap_modif_post_tarif_contrat()
{
  $txtQuery = "UPDATE spip_amap_prix SET ";
  $txtQuery .= "prix_distribution='".$_POST['prixDistribution']."' ";
  $txtQuery .= " WHERE id_produit=".$_POST['idProduit'];
  $txtQuery .= " AND id_saison=".$_POST['idSaison'];
  $txtQuery .= " AND id_type=".$_POST['idType'];

  $sqlResult = sql_query($txtQuery);

  return "Mise à jour dans la table spip_amap_prix " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_POST['idProduit'].", ".$_POST['idSaison'].", ".$_POST['idType'].") "));
} //function table_amap_modif_post_tarif_contrat


//=========================================================================
//=========================================================================
//
function table_amap_post_tarif_contrat()
{
  $txtQuery = "INSERT INTO spip_amap_prix VALUES (";
  $txtQuery .= "'".$_POST['idProduit']."', ";
  $txtQuery .= "'".$_POST['idSaison']."', ";
  $txtQuery .= "'".$_POST['idType']."', ";
  $txtQuery .= "'".$_POST['prixDistribution']."')";

  $sqlResult = sql_query($txtQuery);

  return "Insertion dans la table spip_amap_prix " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_POST['idProduit'].", ".$_POST['idSaison'].", ".$_POST['idType'].") "));
} //function table_amap_post_tarif_contrat

//=========================================================================
//=========================================================================
//
function table_amap_suppr_tarif_contrat()
{
  $txtQuery = "DELETE FROM spip_amap_prix";
  $txtQuery .= " WHERE id_produit=".$_GET['idProduit'];
  $txtQuery .= " AND id_saison=".$_GET['idSaison'];
  $txtQuery .= " AND id_type=".$_GET['idType'];

  $sqlResult = sql_query($txtQuery);

  return "Suppression dans la table spip_amap_prix " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_GET['idProduit'].", ".$_GET['idSaison'].", ".$_GET['idType'].") "));
} //function table_amap_suppr_tarif_contrat

?>
