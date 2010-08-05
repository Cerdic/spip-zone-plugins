<?php
##########################################################################
#
#           Fichier des fonctions participation du plugin Amap pour SPIP
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
function table_amap_lister_participation_contrat($idSaison, $idProduit)
{
  $out = '';
  $hiddens = '';

  pipeline('exec_init',array('args'=>array('exec'=>'amap_contrats'),'data'=>''));

  if ( ($idSaison!=0) && ($idProduit!=0) )
  { // on affiche l'ensemble des participation disponible pour une saison et un produit donné
    $txtQuery = "SELECT s.id_sortie, DATE_FORMAT(s.date_sortie, '%d-%m-%Y') As dateSortie, p.id_personne, pe.prenom, pe.nom";
    $txtQuery .= " FROM spip_amap_sortie s, spip_amap_participation_sortie p, spip_amap_personne pe";
    $txtQuery .= " WHERE s.id_produit=".$idProduit;
    $txtQuery .= " AND s.id_saison=".$idSaison;
    $txtQuery .= " AND s.id_sortie=p.id_sortie";
    $txtQuery .= " AND p.id_personne=pe.id_personne";
    $txtQuery .= " ORDER BY id_sortie";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
    $out .= "\t\t<tr bgcolor='#DBE1C5'>\n";
    $out .= "\t\t\t<td><strong><center>id sortie</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>date sortie</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>personne en contrat</center></strong></td>\n";
    $out .= "\t\t\t<td>&nbsp;</td>\n";
    $out .= "\t\t</tr>\n";

    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $out .= "\t\t<tr style='background-color: #eeeeee;'>\n";
      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idSortie=".$tabUnEnregistrement['id_sortie']."&idPersonne=".$tabUnEnregistrement['id_personne']."&idSaison=".$idSaison."&idProduit=".$idProduit."&table=spip_amap_participation_sortie","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement['id_sortie']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idSortie=".$tabUnEnregistrement['id_sortie']."&idPersonne=".$tabUnEnregistrement['id_personne']."&idSaison=".$idSaison."&idProduit=".$idProduit."&table=spip_amap_participation_sortie","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement['dateSortie']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idSortie=".$tabUnEnregistrement['id_sortie']."&idPersonne=".$tabUnEnregistrement['id_personne']."&idSaison=".$idSaison."&idProduit=".$idProduit."&table=spip_amap_participation_sortie","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement['prenom']." ".$tabUnEnregistrement['nom']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=suppr&idSortie=".$tabUnEnregistrement['id_sortie']."&idPersonne=".$tabUnEnregistrement['id_personne']."&idSaison=".$idSaison."&idProduit=".$idProduit."&table=spip_amap_participation_sortie","ecrire")." '>\n";
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

} // function table_amap_lister_participation_contrat


//=========================================================================
//=========================================================================
//
function table_amap_getmodif_participation_contrat($page, $table)
{
  $out = '';
  $hiddens = '';

  // on affiche l'ensemble des participation disponible pour une saison et un produit donné
  $txtQuery = "SELECT s.id_sortie, DATE_FORMAT(s.date_sortie, '%d-%m-%Y') As dateSortie FROM spip_amap_sortie s, spip_amap_participation_sortie p";
  $txtQuery .= " WHERE s.id_produit=".$_GET['idProduit'];
  $txtQuery .= " AND s.id_saison=".$_GET['idSaison'];
  $txtQuery .= " AND p.id_personne=".$_GET['idPersonne'];
  $txtQuery .= " AND p.id_sortie=".$_GET['idSortie'];
  $txtQuery .= " AND s.id_sortie=p.id_sortie";
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
    $out .= "\t\t\tdate sortie: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t".$tabUnEnregistrement_1['dateSortie']."\n"; 
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tpersonne en contrat: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";

    // recherche des personnes en contrat pour la saison et le produit donné
    $txtQuery = "SELECT p.id_personne, p.prenom, p.nom FROM spip_amap_contrat c, spip_amap_personne p";
    $txtQuery .= " WHERE c.id_produit=".$_GET['idProduit'];
    $txtQuery .= " AND c.id_saison=".$_GET['idSaison'];
    $txtQuery .= " AND c.id_personne=p.id_personne";
    $sqlResult_2 = sql_query($txtQuery);

    $out .= "\t\t\t<select name='nouveauIdPersonne'>\n";
    while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
    {
      if ( $tabUnEnregistrement_2['id_personne'] == $_GET['idPersonne'])
        $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
      else
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
    }
    $out .= "\t\t\t</select\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";
 
    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$_GET['idSaison']."' />\n";
    $hiddens .= "\t<input type='hidden' name='idProduit' value='".$_GET['idProduit']."' />\n";
    $hiddens .= "\t<input type='hidden' name='idSortie' value='".$_GET['idSortie']."' />\n";
    $hiddens .= "\t<input type='hidden' name='ancienIdPersonne' value='".$_GET['idPersonne']."' />\n";

	return generer_url_ecrire("$page", "table=$table", "action=maj")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";
  }

} // function table_amap_getmodif_participation_contrat()


//=========================================================================
//=========================================================================
//
function table_amap_get_participation_contrat($page, $table, $idSaison, $idProduit, $idSortie)
{
  $out = '';
  $hiddens = '';

  if ( ($idSaison!=0) && ($idProduit!=0) )
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tdate sortie: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";

    // recherche des dates de sorties pour la saison et le produit donné
    $txtQuery = "SELECT id_sortie, DATE_FORMAT(date_sortie, '%d-%m-%Y') As dateSortie FROM spip_amap_sortie";
    $txtQuery .= " WHERE id_produit=".$idProduit;
    $txtQuery .= " AND id_saison=".$idSaison;
    $sqlResult = sql_query($txtQuery);

    $out .= "\t\t\t<select name='idSortie' onChange='this.form.submit()'>\n";
    $out .= "\t\t\t\t<option value=''>------</option>\n";
    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      if ( $tabUnEnregistrement['id_sortie'] == $idSortie )
        $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement['id_sortie']."'>".$tabUnEnregistrement['dateSortie']."</option>\n";
      else
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_sortie']."'>".$tabUnEnregistrement['dateSortie']."</option>\n";
    }
    $out .= "\t\t\t</select\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tpersonne en contrat: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";

    // recherche des personnes en contrat pour la saison et le produit donné
    $txtQuery = "SELECT p.id_personne, p.prenom, p.nom FROM spip_amap_contrat c, spip_amap_personne p";
    $txtQuery .= " WHERE c.id_produit=".$idProduit;
    $txtQuery .= " AND c.id_saison=".$idSaison;
    $txtQuery .= " AND c.id_personne=p.id_personne";
    $txtQuery .= " AND p.id_personne NOT IN (";
    $txtQuery .= "   SELECT id_personne FROM spip_amap_participation_sortie";
    $txtQuery .= "   WHERE id_sortie=".$idSortie;
    $txtQuery .= " ) ORDER BY p.nom";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t\t\t<select name='idPersonne'>\n";
    $out .= "\t\t\t\t<option value=''>------</option>\n";
    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_personne']."'>".$tabUnEnregistrement['prenom']." ".$tabUnEnregistrement['nom']."</option>\n";
    }
    $out .= "\t\t\t</select\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";
 
    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$idSaison."' />\n";
    $hiddens .= "\t<input type='hidden' name='idProduit' value='".$idProduit."' />\n";

	return generer_url_ecrire("$page", "table=$table", "action=add")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";

  } // fin if ( ($idSaison!=0) && ($idProduit!=0) )
  else if ( ($idSaison!=0) && ($idProduit==0) )
  {
    return "choisir un produit";
  }
  else
  {
    return "choisir une saison et un produit";
  }

} // function table_amap_get_participation_contrat()


//=========================================================================
//=========================================================================
//
function table_amap_modif_post_participation_contrat()
{
  $txtQuery = "UPDATE spip_amap_participation_sortie SET ";
  $txtQuery .= "id_personne='".$_POST['nouveauIdPersonne']."' ";
  $txtQuery .= " WHERE id_sortie=".$_POST['idSortie'];
  $txtQuery .= " AND id_personne=".$_POST['ancienIdPersonne'];

  $sqlResult = sql_query($txtQuery);

  return "Mise à jour dans la table spip_amap_participation_sortie " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_POST['idSortie'].", ".$_POST['nouveauIdPersonne'].") "));
} //function table_amap_modif_post_participation_contrat

//=========================================================================
//=========================================================================
//
function table_amap_post_participation_contrat()
{
  $txtQuery = "INSERT INTO spip_amap_participation_sortie VALUES (";
  $txtQuery .= "'".$_POST['idSortie']."', ";
  $txtQuery .= "'".$_POST['idPersonne']."')";

  $sqlResult = sql_query($txtQuery);

  return "Insertion dans la table spip_amap_participation_sortie " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_POST['idSortie'].", ".$_POST['idPersonne'].") "));
} //function table_amap_post_participation_contrat

//=========================================================================
//=========================================================================
//
function table_amap_suppr_post_participation_contrat()
{
  $txtQuery = "DELETE FROM spip_amap_participation_sortie";
  $txtQuery .= " WHERE id_personne='".$_GET['idPersonne']."' ";
  $txtQuery .= " AND id_sortie=".$_GET['idSortie'];

  $sqlResult = sql_query($txtQuery);

  return "Suppression dans la table spip_amap_participation_sortie " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_GET['idSortie'].", ".$_GET['idPersonne'].") "));
} //function table_amap_suppr_post_participation_contrat

?>
