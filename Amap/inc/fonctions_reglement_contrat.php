<?php
##########################################################################
#
#           Fichier des fonctions règlement du plugin Amap pour SPIP
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
function table_amap_lister_reglement_contrat($idSaison, $idProduit, $idContrat)
{
  $out = '';
  $hiddens = '';

  pipeline('exec_init',array('args'=>array('exec'=>'amap_contrats'),'data'=>''));

  if ( ($idSaison!=0) && ($idProduit!=0) && isset($idContrat) )
  { // on affiche l'ensemble des reglement disponible pour une saison et un produit donné
    $txtQuery = "SELECT r.id_cheque, b.label_banque, r.montant_euros, r.ref_cheque";
    $txtQuery .= " FROM spip_amap_reglement r, spip_amap_banque b";
    $txtQuery .= " WHERE r.id_banque=b.id_banque";
    $txtQuery .= " AND r.id_contrat=".$idContrat;
    $txtQuery .= " ORDER BY r.id_cheque";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
    $out .= "\t\t<tr bgcolor='#DBE1C5'>\n";
    $out .= "\t\t\t<td><strong><center>id</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>banque</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>r&eacute;f&eacute;rence</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>montant</center></strong></td>\n";
    $out .= "\t\t\t<td>&nbsp;</td>\n";
    $out .= "\t\t</tr>\n";

    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $out .= "\t\t<tr style='background-color: #eeeeee;'>\n";
      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idCheque=".$tabUnEnregistrement['id_cheque']."&idSaison=".$idSaison."&idProduit=".$idProduit."&idContrat=".$idContrat."&table=spip_amap_reglement","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement['id_cheque']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idCheque=".$tabUnEnregistrement['id_cheque']."&idSaison=".$idSaison."&idProduit=".$idProduit."&idContrat=".$idContrat."&table=spip_amap_reglement","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement['label_banque']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idCheque=".$tabUnEnregistrement['id_cheque']."&idSaison=".$idSaison."&idProduit=".$idProduit."&idContrat=".$idContrat."&table=spip_amap_reglement","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement['ref_cheque']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idCheque=".$tabUnEnregistrement['id_cheque']."&idSaison=".$idSaison."&idProduit=".$idProduit."&idContrat=".$idContrat."&table=spip_amap_reglement","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement['montant_euros']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=suppr&idCheque=".$tabUnEnregistrement['id_cheque']."&idSaison=".$idSaison."&idProduit=".$idProduit."&idContrat=".$idContrat."&table=spip_amap_reglement","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center><img src='"._DIR_PLUGIN_AMAP."img_pack/b_drop.png' /></center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t</tr>\n";
      
    } // fin while ($tabUnEnregistrement = sql_fetch($sqlResult))

    $txtQuery = "SELECT SUM(r.montant_euros) As total, c.nb_distribution, p.prix_distribution";
    $txtQuery .= " FROM spip_amap_reglement r, spip_amap_contrat c, spip_amap_prix p";
    $txtQuery .= " WHERE r.id_contrat=".$idContrat;
    $txtQuery .= " AND c.id_contrat=".$idContrat;
    $txtQuery .= " AND c.id_produit=".$idProduit;
    $txtQuery .= " AND c.id_saison=".$idSaison;
    $txtQuery .= " AND p.id_saison=".$idSaison;
    $txtQuery .= " AND p.id_produit=".$idProduit;
    $txtQuery .= " AND p.id_type=c.id_type";
    $txtQuery .= " GROUP BY c.id_contrat, c.id_produit, c.id_saison";
    $sqlResult = sql_query($txtQuery);

    if ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $diff = $tabUnEnregistrement['total'] - ($tabUnEnregistrement['nb_distribution']*$tabUnEnregistrement['prix_distribution']) ;
      if ( $diff < 0)
      {
        $out .= "\t\t<tr>\n";
        $out .= "\t\t\t<td>&nbsp;</td>\n";
        $out .= "\t\t\t<td bgcolor='#AD0000'>Il manque <strong>".abs($diff)." euros</strong></td>\n";
        $out .= "\t\t\t<td>&nbsp;</td>\n";
        $out .= "\t\t\t<td>&nbsp;</td>\n";
        $out .= "\t\t</tr>\n";
      }
    }
    $out .= "\t</table>\n";

  } // fin   if ( ($idSaison!=0) && ($idProduit!=0) && isset($idContrat) )
  else if ( ($idSaison!=0) && ($idProduit==0) && (!isset($idContrat)) )
  {
    return "choisir un produit et un contrat";
  }
  else if ( ($idSaison!=0) && ($idProduit!=0) && (!isset($idContrat)) )
  {
    return "choisir un contrat";
  }
  else
  {
    return "choisir une saison, un produit et un contrat";
  }

  return $out;

} // function table_amap_lister_reglement_contrat


//=========================================================================
//=========================================================================
//
function table_amap_getmodif_reglement_contrat()
{
  $out = '';
  $hiddens = '';

  // on affiche l'ensemble des reglement disponible pour une saison et un produit donné
  $txtQuery = "SELECT id_banque, montant_euros, ref_cheque FROM spip_amap_reglement";
  $txtQuery .= " WHERE id_contrat=".$_GET['idContrat'];
  $txtQuery .= " AND id_cheque=".$_GET['idCheque'];
  $sqlResult_1 = sql_query($txtQuery);

  if ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tcontrat: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t".$_GET['idContrat']."\n"; 
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tch&egrave;que: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t".$_GET['idCheque']."\n"; 
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tbanque: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";

    $out .= "\t\t\t<select name='idBanque'>\n";
    // deuxième boucle pour afficher les label des banques
    $txtQuery = "SELECT * FROM spip_amap_banque";
    $sqlResult_2 = sql_query($txtQuery);

    while($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
    {
      if ($tabUnEnregistrement_2['id_banque'] == $tabUnEnregistrement_1['id_banque']) {
        $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement_2['id_banque']."'>".$tabUnEnregistrement_2['label_banque']."</option>\n";
      }
      else {
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_banque']."'>".$tabUnEnregistrement_2['label_banque']."</option>\n";
      }
    }
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tr&eacute;f&eacute;rence: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input name='ref' size='12' maxlength='12' value='".$tabUnEnregistrement_1['ref_cheque']."'> \n"; 
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tmontant (euros): \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input name='montant' size='5' maxlength='5' value='".$tabUnEnregistrement_1['montant_euros']."'> \n"; 
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";
 
    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$_GET['idSaison']."' />\n";
    $hiddens .= "\t<input type='hidden' name='idProduit' value='".$_GET['idProduit']."' />\n";
    $hiddens .= "\t<input type='hidden' name='idContrat' value='".$_GET['idContrat']."' />\n";
    $hiddens .= "\t<input type='hidden' name='idCheque' value='".$_GET['idCheque']."' />\n";

    return generer_url_entite('amap_contrats', "table=spip_amap_reglement&action=maj","post_ecrire")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";
  }

} // function table_amap_getmodif_reglement_contrat()


//=========================================================================
//=========================================================================
//
function table_amap_get_reglement_contrat($idSaison, $idProduit, $idContrat)
{
  $out = '';
  $hiddens = '';

  if ( ($idSaison!=0) && ($idProduit!=0) && isset($idContrat) )
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tbanque: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";

    $out .= "\t\t\t<select name='idBanque'>\n";
    // deuxième boucle pour afficher les label des banques
    $txtQuery = "SELECT * FROM spip_amap_banque";
    $sqlResult = sql_query($txtQuery);

    while($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_banque']."'>".$tabUnEnregistrement['label_banque']."</option>\n";
    }
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tr&eacute;f&eacute;rence: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input name='ref' size='12' maxlength='12' value=''> \n"; 
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tmontant (euros): \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input name='montant' size='5' maxlength='5' value='' > \n"; 
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$idSaison."' />\n";
    $hiddens .= "\t<input type='hidden' name='idProduit' value='".$idProduit."' />\n";
    $hiddens .= "\t<input type='hidden' name='idContrat' value='".$idContrat."' />\n";

    return generer_url_entite('amap_contrats', "table=spip_amap_reglement&action=add","post_ecrire")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";

  } // fin   if ( ($idSaison!=0) && ($idProduit!=0) && isset($idContrat) )
  else if ( ($idSaison!=0) && ($idProduit==0) && (!isset($idContrat)) )
  {
    return "choisir un produit et un contrat";
  }
  else if ( ($idSaison!=0) && ($idProduit!=0) && (!isset($idContrat)) )
  {
    return "choisir un contrat";
  }
  else
  {
    return "choisir une saison, un produit et un contrat";
  }

} // function table_amap_get_reglement_contrat()


//=========================================================================
//=========================================================================
//
function table_amap_modif_post_reglement_contrat()
{
  $txtQuery = "UPDATE spip_amap_reglement SET";
  $txtQuery .= " id_banque='".$_POST['idBanque']."', ";
  $txtQuery .= " ref_cheque='".$_POST['ref']."', ";
  $txtQuery .= " montant_euros='".$_POST['montant']."' ";
  $txtQuery .= " WHERE id_cheque=".$_POST['idCheque'];

  $sqlResult = sql_query($txtQuery);

  return "Mise à jour dans la table spip_amap_reglement " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_POST['idCheque'].") "));
} //function table_amap_modif_post_reglement_contrat


//=========================================================================
//=========================================================================
//
function table_amap_post_reglement_contrat()
{
  $txtQuery = "INSERT INTO spip_amap_reglement(id_contrat, id_banque, ref_cheque, montant_euros) VALUES (";
  $txtQuery .= "'".$_POST['idContrat']."', ";
  $txtQuery .= "'".$_POST['idBanque']."', ";
  $txtQuery .= "'".$_POST['ref']."', ";
  $txtQuery .= "'".$_POST['montant']."')";
  $sqlResult = sql_query($txtQuery);

  return "Insertion dans la table spip_amap_reglement " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: () "));
} //function table_amap_post_reglement_contrat

//=========================================================================
//=========================================================================
//
function table_amap_suppr_reglement_contrat()
{
  $txtQuery = "DELETE FROM spip_amap_reglement";
  $txtQuery .= " WHERE id_cheque=".$_GET['idCheque'];

  $sqlResult = sql_query($txtQuery);

  return "Suppression dans la table spip_amap_reglement " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_GET['idCheque'].") "));
} //function table_amap_suppr_reglement_contrat

?>
