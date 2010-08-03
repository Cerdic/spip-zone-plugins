<?php
##########################################################################
#
#           Fichier des fonctions vacances du plugin Amap pour SPIP
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
function table_amap_lister_vacance_distribution($idSaison, $idProduit)
{
  $out = '';
  $hiddens = '';

  pipeline('exec_init',array('args'=>array('exec'=>'amap_distributions'),'data'=>''));

  if ( isset($idSaison) && isset($idProduit) )
  { // on affiche l'ensemble des distributions de la saison
    // 1er boucle sur la table spip_amap_vacance
    $txtQuery = "SELECT DATE_FORMAT(e.date_evenement, '%d-%m-%Y') As dateEvenement, v.id_contrat, v.id_evenement, c.id_personne, p1.prenom As cPrenom, p1.nom As cNom, v.id_remplacant, v.remplacant_ext, v.paye";
    $txtQuery .= " FROM spip_amap_vacance v, spip_amap_contrat c, spip_amap_evenements e, spip_amap_personne p1";
    $txtQuery .= " WHERE e.id_evenement=v.id_evenement";
    $txtQuery .= " AND v.id_contrat=c.id_contrat";
    $txtQuery .= " AND e.id_saison=".$idSaison;
    $txtQuery .= " AND c.id_saison=".$idSaison;
    $txtQuery .= " AND c.id_produit=".$idProduit;
    $txtQuery .= " AND p1.id_personne=c.id_personne";
    $txtQuery .= " ORDER BY e.date_evenement";

    $sqlResult_1 = sql_query($txtQuery);
    $out .= "\t<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
    $out .= "\t\t<tr bgcolor='#DBE1C5'>\n";
    $out .= "\t\t\t<td><strong><center>date d&eacute;but</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>nom contrat</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>remplacant</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>remplacant ext.</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>paye</center></strong></td>\n";
    $out .= "\t\t\t<td>&nbsp;</td>\n";
    $out .= "\t\t</tr>\n";

    while ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))
    {
      $out .= "\t\t<tr style='background-color: #eeeeee;'>\n";
      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
     $out .= "\t\t\t\t<a href='".generer_url_entite('amap_distributions', "action=modif&idContrat=".$tabUnEnregistrement_1['id_contrat']."&idSaison=".$idSaison."&idProduit=".$idProduit."&idEvenement=".$tabUnEnregistrement_1['id_evenement']."&table=spip_amap_vacance","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement_1['dateEvenement']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";
      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_distributions', "action=modif&idContrat=".$tabUnEnregistrement_1['id_contrat']."&idSaison=".$idSaison."&idProduit=".$idProduit."&idEvenement=".$tabUnEnregistrement_1['id_evenement']."&table=spip_amap_vacance","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement_1['cPrenom']." ".$tabUnEnregistrement_1['cNom']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";
      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_distributions', "action=modif&idContrat=".$tabUnEnregistrement_1['id_contrat']."&idSaison=".$idSaison."&idProduit=".$idProduit."&idEvenement=".$tabUnEnregistrement_1['id_evenement']."&table=spip_amap_vacance","ecrire")." '>\n";


      $txtQuery = "SELECT p.id_personne, p.prenom, p.nom";
      $txtQuery .= ", (SELECT count(c.id_contrat) FROM spip_amap_contrat c";
      $txtQuery .= "   WHERE c.id_personne=p.id_personne";
      $txtQuery .= "   GROUP BY c.id_personne";
      $txtQuery .= " ) As NbContrat";
      $txtQuery .= ", (SELECT count(o.id_produit) FROM spip_amap_produit o";
      $txtQuery .= "   WHERE o.id_paysan=p.id_personne";
      $txtQuery .= "   GROUP BY o.id_paysan";
      $txtQuery .= " ) As NbProduit";
      $txtQuery .= " FROM spip_amap_personne p";
      $txtQuery .= " WHERE p.id_personne=".$tabUnEnregistrement_1['id_remplacant'];

      $sqlResult_2 = sql_query($txtQuery);
      if ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
      {
        $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."\n";
        if ( ($tabUnEnregistrement_2['NbContrat'] == null) && ($tabUnEnregistrement_2['NbProduit'] == null) )
          $out .= "\t\t\t\t\t (Intermittent)\n";
        $out .= "\t\t\t\t\t</center>\n";
      }
      else
        $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement_1['id_remplacant']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_distributions', "action=modif&idContrat=".$tabUnEnregistrement_1['id_contrat']."&idSaison=".$idSaison."&idProduit=".$idProduit."&idEvenement=".$tabUnEnregistrement_1['id_evenement']."&table=spip_amap_vacance","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement_1['remplacant_ext']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_distributions', "action=modif&idContrat=".$tabUnEnregistrement_1['id_contrat']."&idSaison=".$idSaison."&idProduit=".$idProduit."&idEvenement=".$tabUnEnregistrement_1['id_evenement']."&table=spip_amap_vacance","ecrire")." '>\n";
      if ($tabUnEnregistrement_1['paye'] == 1)
        $out .= "\t\t\t\t\t<center>oui</center>\n";
      else
        $out .= "\t\t\t\t\t<center>&nbsp;</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_distributions', "action=suppr&idContrat=".$tabUnEnregistrement_1['id_contrat']."&idSaison=".$idSaison."&idProduit=".$idProduit."&idEvenement=".$tabUnEnregistrement_1['id_evenement']."&table=spip_amap_vacance","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center><img src='"._DIR_PLUGIN_AMAP."img_pack/b_drop.png' /></center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t</tr>\n";
      
    } // fin while
    $out .= "\t</table>\n";
  } // fin if ( isset($idSaison) && isset($idProduit) )
  else if ( ($idSaison!=0) && ($idProduit==0) )
  {
    return "choisir un produit";
  }
  else
  {
    return "choisir une saison et un produit";
  }
  
  return $out;

} // function table_amap_lister_vacance_distribution


//=========================================================================
//=========================================================================
//
function table_amap_getmodif_vacance_distribution()
{
  $out = '';
  $hiddens = '';

 $txtQuery = "SELECT DATE_FORMAT(e.date_evenement, '%d-%m-%Y') As dateEvenement, c.id_personne, p1.prenom As cPrenom, p1.nom As cNom, v.id_remplacant, po.label_produit, v.remplacant_ext, v.paye";
  $txtQuery .= " FROM spip_amap_vacance v, spip_amap_contrat c, spip_amap_evenements e, spip_amap_personne p1, spip_amap_produit po";
  $txtQuery .= " WHERE e.id_evenement=v.id_evenement";
  $txtQuery .= " AND v.id_contrat=c.id_contrat";
  $txtQuery .= " AND e.id_saison=".$_GET['idSaison'];
  $txtQuery .= " AND c.id_saison=".$_GET['idSaison'];
  $txtQuery .= " AND c.id_produit=".$_GET['idProduit'];
  $txtQuery .= " AND p1.id_personne=c.id_personne";
  $txtQuery .= " AND v.id_contrat=".$_GET['idContrat'];
  $txtQuery .= " AND v.id_evenement=".$_GET['idEvenement'];
  $txtQuery .= " AND po.id_produit=c.id_produit";

  $sqlResult_1 = sql_query($txtQuery);
 
  if ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tcontrat: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t".$_GET['idContrat']."\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tproduit: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t".$tabUnEnregistrement_1['label_produit']."\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tdate evenement: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t".$tabUnEnregistrement_1['dateEvenement']."\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tnom contrat: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t".$tabUnEnregistrement_1['cPrenom']." ".$tabUnEnregistrement_1['cNom']."\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tnom rempla&ccedil;ant: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
 
    // boucle sur la table spip_amap_personne
    $txtQuery = "SELECT p.id_personne, p.prenom, p.nom";
    $txtQuery .= ", (SELECT count(c.id_contrat) FROM spip_amap_contrat c";
    $txtQuery .= "   WHERE c.id_personne=p.id_personne";
    $txtQuery .= "   GROUP BY c.id_personne";
    $txtQuery .= " ) As NbContrat";
    $txtQuery .= ", (SELECT count(o.id_produit) FROM spip_amap_produit o";
    $txtQuery .= "   WHERE o.id_paysan=p.id_personne";
    $txtQuery .= "   GROUP BY o.id_paysan";
    $txtQuery .= " ) As NbProduit";
    $txtQuery .= ", (SELECT count(po.id_produit) FROM spip_amap_produit po";
    $txtQuery .= "   WHERE po.id_paysan=p.id_personne";
    $txtQuery .= "   AND po.id_produit=".$_GET['idProduit'];
    $txtQuery .= "   GROUP BY po.id_paysan";
    $txtQuery .= " ) As SonProduit";
    $txtQuery .= " FROM spip_amap_personne p";
    $txtQuery .= " WHERE p.id_personne!=".$tabUnEnregistrement_1['id_personne'];
    $txtQuery .= " ORDER BY p.nom";

    $sqlResult_2 = sql_query($txtQuery);

    $out .= "\t\t\t<select name='idRemplacant'>\n";
    $out .= "\t\t\t\t<option value=''>----</option>\n";
    while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
    {
      if ($tabUnEnregistrement_2['SonProduit'] == 0)
      {
        if ( $tabUnEnregistrement_2['id_personne'] == $tabUnEnregistrement_1['id_remplacant'])
          $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."\n";
        else
          $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."\n";
        if ( ($tabUnEnregistrement_2['NbContrat'] == null) && ($tabUnEnregistrement_2['NbProduit'] == null) )
          $out .= "\t\t\t\t (<strong>Intermittent</strong>)\n";
        $out .= "\t\t\t\t</option>\n";
      }
    } // fin while
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\trempla&ccedil;ant externe: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<textarea name='remplacantExt' rows='3' cols='50'>".$tabUnEnregistrement_1['remplacant_ext']."</textarea>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>paye ? </td>\n";
    if ($tabUnEnregistrement_1['paye'] == 1)
      $out .= "\t\t<td><input type='checkbox' name='paye' checked > (cocher si pay&eacute;)</td>\n";
    else
      $out .= "\t\t<td><input type='checkbox' name='paye' > (cocher si pay&eacute;)</td>\n";
    $out .= "\t</tr>";
 
    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$_GET['idSaison']."' />\n";
    $hiddens .= "\t<input type='hidden' name='idProduit' value='".$_GET['idProduit']."' />\n";
    $hiddens .= "\t<input type='hidden' name='idContrat' value='".$_GET['idContrat']."' />\n";
    $hiddens .= "\t<input type='hidden' name='idEvenement' value='".$_GET['idEvenement']."' />\n";

	return generer_url_ecrire("amap_distributions", "table=spip_amap_vacance", "action=maj")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";
  } // fin if ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))

} // function table_amap_getmodif_vacance_distribution()


//=========================================================================
//=========================================================================
//
function table_amap_get_vacance_distribution($idSaison, $idProduit)
{
  $out = '';
  $hiddens = '';

  if ( isset($idSaison) && isset($idProduit) )
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tevenement: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";

    // on recherche les dates de distributions correspondant à la saison et au produit donné
    $txtQuery = "SELECT DATE_FORMAT(e.date_evenement, '%d-%m-%Y') As dateEvenement, e.id_evenement";
    $txtQuery .= " FROM spip_amap_evenements e, amap_produit_distribution d";
    $txtQuery .= " WHERE e.id_saison=".$idSaison;
    $txtQuery .= " AND d.id_evenement=e.id_evenement";
    $txtQuery .= " AND d.id_produit=".$idProduit;
    $txtQuery .= " ORDER BY e.date_evenement";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t\t\t<select name='idEvenement'>\n";
    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_evenement']."'>".$tabUnEnregistrement['dateEvenement']."</option>\n";
    } // fin while
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tcontrat: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";

    // on recherche tous les contrats de la saison et du produit donné
    $txtQuery = "SELECT c.id_contrat, p.prenom, p.nom FROM spip_amap_contrat c, spip_amap_personne p";
    $txtQuery .= " WHERE c.id_personne=p.id_personne";
    $txtQuery .= " AND c.id_produit=".$idProduit;
    $txtQuery .= " AND c.id_saison=".$idSaison;
    $txtQuery .= " ORDER BY p.nom";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t\t\t<select name='idContrat'>\n";
    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_contrat']."'>".$tabUnEnregistrement['prenom']." ".$tabUnEnregistrement['nom']."</option>\n";
    } // fin while
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\trempla&ccedil;ant: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
 
    // boucle sur la table spip_amap_personne
    $txtQuery = "SELECT p.id_personne, p.prenom, p.nom";
    $txtQuery .= ", (SELECT count(c.id_contrat) FROM spip_amap_contrat c";
    $txtQuery .= "   WHERE c.id_personne=p.id_personne";
    $txtQuery .= "   GROUP BY c.id_personne";
    $txtQuery .= " ) As NbContrat";
    $txtQuery .= ", (SELECT count(o.id_produit) FROM spip_amap_produit o";
    $txtQuery .= "   WHERE o.id_paysan=p.id_personne";
    $txtQuery .= "   GROUP BY o.id_paysan";
    $txtQuery .= " ) As NbProduit";
    $txtQuery .= ", (SELECT count(po.id_produit) FROM spip_amap_produit po";
    $txtQuery .= "   WHERE po.id_paysan=p.id_personne";
    $txtQuery .= "   AND po.id_produit=".$idProduit;
    $txtQuery .= "   GROUP BY po.id_paysan";
    $txtQuery .= " ) As SonProduit";
    $txtQuery .= " FROM spip_amap_personne p";
    $txtQuery .= " ORDER BY p.nom";

    $sqlResult = sql_query($txtQuery);

    $out .= "\t\t\t<select name='idRemplacant'>\n";
    $out .= "\t\t\t\t<option value=''>----</option>\n";
    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      if ($tabUnEnregistrement['SonProduit'] == 0)
      {
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_personne']."'>".$tabUnEnregistrement['prenom']." ".$tabUnEnregistrement['nom']."\n";
        if ( ($tabUnEnregistrement['NbContrat'] == null) && ($tabUnEnregistrement['NbProduit'] == null) )
          $out .= "\t\t\t\t (<strong>Intermittent</strong>)\n";
        $out .= "\t\t\t\t</option>\n";
      }
    } // fin while
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\trempla&ccedil;ant externe: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<textarea name='remplacantExt' rows='3' cols='50'></textarea>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>paye ? </td>\n";
    if ($tabUnEnregistrement_1['paye'] == 1)
      $out .= "\t\t<td><input type='checkbox' name='paye' checked > (cocher si pay&eacute;)</td>\n";
    else
      $out .= "\t\t<td><input type='checkbox' name='paye' > (cocher si pay&eacute;)</td>\n";
    $out .= "\t</tr>";
 
    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$idSaison."' />\n";
    $hiddens .= "\t<input type='hidden' name='idProduit' value='".$idProduit."' />\n";

	return generer_url_ecrire("amap_distrutions", "table=spip_amap_vacance", "action=add")
                               ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";

  } // fin if ( isset($idSaison) && isset($idProduit) )
  else if ( ($idSaison!=0) && ($idProduit==0) )
  {
    return "choisir un produit";
  }
  else
  {
    return "choisir une saison et un produit";
  }

} // function table_amap_get_vacance_distribution()


//=========================================================================
//=========================================================================
//
function table_amap_modif_post_vacance_distribution()
{
  $txtQuery = "UPDATE spip_amap_vacance SET ";
  if ( ($_POST['idRemplacant']) && ($_POST['idRemplacant']!='') )
  {
    $txtQuery .= "id_remplacant='".$_POST['idRemplacant']."', ";
    $txtQuery .= "remplacant_ext=null, ";
  }
  else
  {
    $txtQuery .= "id_remplacant=null, ";
    if ($_POST['remplacantExt'])
      $txtQuery .= "remplacant_ext='".$_POST['remplacantExt']."', ";
    else
      $txtQuery .= "remplacant_ext=null, ";
  }
  if ($_POST['paye'] == 'on')
    $txtQuery .= "paye='1' ";
  else
    $txtQuery .= "paye=null ";
  $txtQuery .= " WHERE id_contrat=".$_POST['idContrat'];
  $txtQuery .= " AND id_evenement=".$_POST['idEvenement'];

  $sqlResult = sql_query($txtQuery);

  return "Mise à jour dans la table spip_amap_vacance " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_POST['idContrat'].", ".$_POST['idEvenement'].") "));
} //function table_amap_modif_post_vacance_distribution


//=========================================================================
//=========================================================================
//
function table_amap_post_vacance_distribution()
{
  // on recherche l'identifiant de la personne du contrat
  $txtQuery = "SELECT id_personne FROM spip_amap_contrat";
  $txtQuery .= " WHERE id_contrat=".$_POST['idContrat'];

  $sqlResult = sql_query($txtQuery);
  if (	  ($tabUnEnregistrement = sql_fetch($sqlResult))
       && ($tabUnEnregistrement['id_personne'] != $_POST['idRemplacant']) )
   {
  $txtQuery = "INSERT INTO spip_amap_vacance VALUES (";
  $txtQuery .= "'".$_POST['idContrat']."', ";
  $txtQuery .= "'".$_POST['idEvenement']."', ";
  if ( ($_POST['idRemplacant']) && ($_POST['idRemplacant']!='') )
  {
    $txtQuery .= "'".$_POST['idRemplacant']."', null, ";
  }
  else
  {
    if ($_POST['remplacantExt'])
    $txtQuery .= "null, '".$_POST['remplacantExt']."', ";
    else
      $txtQuery .= "null, null, ";
  }
  if ($_POST['paye'] == 'on')
  {
    $txtQuery .= "'1')";
  }
  else
  {
    $txtQuery .= "null)";
  }
  $sqlResult = sql_query($txtQuery);

  return "Insertion dans la table spip_amap_vacance " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_POST['idContrat'].", ".$_POST['idEvenement'].") "));
  }
  else
    return "Vous ne pouvez pas remplacer une personne par elle m&ecirc;me...";
} //function table_amap_post_vacance_distribution

//=========================================================================
//=========================================================================
//
function table_amap_suppr_vacance_distribution()
{
  $txtQuery = "DELETE FROM spip_amap_vacance";
  $txtQuery .= " WHERE id_contrat=".$_GET['idContrat'];
  $txtQuery .= " AND id_evenement=".$_GET['idEvenement'];

  $sqlResult = sql_query($txtQuery);

  return "Suppression dans la table spip_amap_vacance " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_GET['idContrat'].", ".$_GET['idEvenement'].") "));
} //function table_amap_suppr_vacance_distribution

?>
