<?php
##########################################################################
#
#           Fichier de fonctions choix du plugin Amap pour SPIP
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
function table_amap_lister_choix_saison($idSaison, $table, $page)
{
  $out = '';
  $hiddens = '';
  
  // on affiche le formulaire de sélection de la saison
  // boucle sur la table spip_amap_saison
  $txtQuery = "SELECT id_saison FROM spip_amap_saison ";
  $sqlResult = sql_query($txtQuery);

  $out .= "\t choix <select name='idSaison'>\n";

  while ($tabUnEnregistrement = sql_fetch($sqlResult))
  {
    if ($tabUnEnregistrement['id_saison'] == $idSaison)
      $out .= "\t\t<option selected value='".$tabUnEnregistrement['id_saison']."'> saison ".$tabUnEnregistrement['id_saison']."\n";
    else
      $out .= "\t\t<option value='".$tabUnEnregistrement['id_saison']."'> saison ".$tabUnEnregistrement['id_saison']."\n";
  } // fin while
  $out .= "\t</select>\n";

  $res = generer_url_entite($page, "table=".$table."&action=saison","post_ecrire")
         .$out
         ."<input type='submit'/></form>";

  return $res;

} // function table_amap_lister_choix_saison

//=========================================================================
//=========================================================================
//
function table_amap_lister_choix_produit($idSaison, $idProduit, $table, $page)
{
  $out = '';
  $res = '';
  
  if ( isset($idSaison) && ($idSaison != 0) )
  {
    // on affiche le formulaire de sélection de la saison
    // boucle sur la table spip_amap_produit
    $txtQuery = "SELECT id_produit, label_produit FROM spip_amap_produit";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t choix <select name='idProduit'>\n";

    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      if ($tabUnEnregistrement['id_produit'] == $idProduit)
        $out .= "\t\t<option selected value='".$tabUnEnregistrement['id_produit']."' >".$tabUnEnregistrement['label_produit']."\n";
      else
        $out .= "\t\t<option value='".$tabUnEnregistrement['id_produit']."' >".$tabUnEnregistrement['label_produit']."\n";
    } // fin while
    $out .= "\t</select>\n";

    $out .= "\t<input type='hidden' name='idSaison' value='".$idSaison."' />\n";

    $res = generer_url_entite($page, "table=".$table."&action=produit","post_ecrire")
           .$out
           ."<input type='submit'/></form>";
  }
  else
    $res = "vous devez d'abord choisir la saison";

  return $res;

} // function table_amap_lister_choix_produit

//=========================================================================
//=========================================================================
//
function table_amap_lister_choix_distribution($idSaison, $idProduit, $idDistrib, $table, $page)
{
  $out = '';
  $res = '';
  
  if ( (isset($idSaison)) && (isset($idProduit)) && ($idSaison != 0) )
  {
    // on affiche le formulaire de sélection de la saison
    // boucle sur la table spip_amap_date_dsitribution
    $txtQuery = "SELECT DATE_FORMAT(e.date_evenement, '%d-%m-%Y') As dateintPremierEnreg, e.id_evenement FROM spip_amap_evenements e, spip_amap_produit_distribution p";
    $txtQuery .= " WHERE e.id_saison=".$idSaison;
    $txtQuery .= " AND p.id_produit=".$idProduit;
    $txtQuery .= " AND p.id_evenement=e.id_evenement";
    $txtQuery .= " ORDER BY e.date_evenement";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t choix <select name='idDistrib'>\n";

    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      if ($tabUnEnregistrement['id_evenement'] == $idDistrib)
        $out .= "\t\t<option selected value='".$tabUnEnregistrement['id_evenement']."' >".$tabUnEnregistrement['dateintPremierEnreg']."\n";
      else
        $out .= "\t\t<option value='".$tabUnEnregistrement['id_evenement']."' >".$tabUnEnregistrement['dateintPremierEnreg']."\n";
    } // fin while
    $out .= "\t</select>\n";

    $out .= "\t<input type='hidden' name='idSaison' value='".$idSaison."' />\n";
    $out .= "\t<input type='hidden' name='idProduit' value='".$_POST['idProduit']."' />\n";

    $res = generer_url_entite($page, "table=".$table."&action=distribution","post_ecrire")
         .$out
           ."<input type='submit'/></form>";
  }
  else if (isset($idSaison))
    $res = "vous devez choisir le produit";
  else
    $res = "vous devez choisir une saison et un produit";

  return $res;

} // function table_amap_lister_choix_distribution

//=========================================================================
//=========================================================================
//
function table_amap_lister_choix_contrat($idSaison, $idProduit, $idContrat, $table, $page)
{
  $out = '';
  $res = '';
  
  if ( isset($idSaison) && isset($idProduit) && ($idSaison != 0) && ($idProduit != 0) )
  {
    // on affiche le formulaire de sélection de la saison
    // boucle sur la table spip_amap_produit
    $txtQuery = "SELECT c.id_contrat, p.prenom, p.nom FROM spip_amap_contrat c, spip_amap_personne p";
    $txtQuery .= " WHERE c.id_personne=p.id_personne";
    $txtQuery .= " AND c.id_produit=".$idProduit;
    $txtQuery .= " AND c.id_saison=".$idSaison;
    $txtQuery .= " ORDER BY p.nom";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t choix <select name='idContrat'>\n";

    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      if ($tabUnEnregistrement['id_contrat'] == $idContrat)
        $out .= "\t\t<option selected value='".$tabUnEnregistrement['id_contrat']."' >".$tabUnEnregistrement['prenom']." ".$tabUnEnregistrement['nom']."\n";
      else
        $out .= "\t\t<option value='".$tabUnEnregistrement['id_contrat']."' >".$tabUnEnregistrement['prenom']." ".$tabUnEnregistrement['nom']."\n";
    }
    $out .= "\t</select>\n";

    $out .= "\t<input type='hidden' name='idSaison' value='".$idSaison."' />\n";
    $out .= "\t<input type='hidden' name='idProduit' value='".$idProduit."' />\n";

    $res = generer_url_entite($page, "table=".$table."&action=contrat","post_ecrire")
           .$out
           ."<input type='submit'/></form>";
  }
  else if ( isset($idSaison) && ($idSaison != 0) )
    $res = "vous devez d'abord choisir le produit se rapportant au contrat";
  else
    $res = "vous devez d'abord choisir la saison et le produit se rapportant au contrat";

  return $res;

} // function table_amap_lister_choix_contrat

//=========================================================================
//=========================================================================
//
function table_amap_lister_choix_famille($idProduit, $idFamille, $table, $page)
{
  $out = '';
  $res = '';
  
  if ( isset($idProduit) )
  {
    // on affiche le formulaire de sélection de la saison
    // boucle sur la table spip_amap_produit
    $txtQuery = "SELECT id_famille, label_famille FROM spip_amap_famille_variete";
    $txtQuery .= " WHERE id_produit=".$idProduit;
    $sqlResult = sql_query($txtQuery);

    $out .= "\t choix <select name='idFamille'>\n";

    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      if ($tabUnEnregistrement['id_famille'] == $idFamille)
        $out .= "\t\t<option selected value='".$tabUnEnregistrement['id_famille']."' >".$tabUnEnregistrement['label_famille']."\n";
      else
        $out .= "\t\t<option value='".$tabUnEnregistrement['id_famille']."' >".$tabUnEnregistrement['label_famille']."\n";
    } // fin while
    $out .= "\t</select>\n";

    $out .= "\t<input type='hidden' name='idProduit' value='".$idProduit."' />\n";

    $res = generer_url_entite($page, "table=".$table."&action=famille","post_ecrire")
           .$out
           ."<input type='submit'/></form>";
  }
  else
  {
      $res = "vous devez d'abord choisir le produit";
  }
  return $res;

} // function table_amap_lister_choix_famille

//=========================================================================
//=========================================================================
//
function table_amap_lister_choix_statut($statut, $table, $page)
{
  $out = '';
  $res = '';
 
  $out .= "\t choix <select name='statut'>\n";
  //$out .= "\t\t<option value='' >------</option>\n";

  if ($statut == 1)
    $out .= "\t\t<option selected value='1' > tous </option>\n";
  else
    $out .= "\t\t<option value='1' > tous </option>\n";
  if ($statut == 2)
    $out .= "\t\t<option selected value='2' > intermittent </option>\n";
  else
    $out .= "\t\t<option value='2' > intermittent </option>\n";
  if ($statut == 3)
    $out .= "\t\t<option selected value='3' > paysan </option>\n";
  else
    $out .= "\t\t<option value='3' > paysan </option>\n";

  $out .= "\t</select>\n";
  
  $res = generer_url_entite($page, "table=".$table."&action=statut","post_ecrire")
         .$out
         ."<input type='submit'/></form>";
  
  return $res;

} // function table_amap_lister_choix_famille

?>
