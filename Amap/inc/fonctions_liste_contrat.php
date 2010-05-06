<?php
##########################################################################
#
#           Fichier des fonctions variétés du plugin Amap pour SPIP
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
function table_amap_lister_liste_contrat($idSaison, $idProduit)
{
  $out = '';
  $hiddens = '';

  pipeline('exec_init',array('args'=>array('exec'=>'amap_contrats'),'data'=>''));

  if ( ($idSaison!=0) && ($idProduit!=0) )
  { // on affiche l'ensemble des contrats de la saison pour un produit donné
    $txtQuery = "SELECT DATE_FORMAT(e.date_evenement, '%d-%m-%Y') As dateEvenement, c.id_contrat, c.id_personne, c.demi_panier, c.nb_distribution, p.prenom, p.nom, t.label_type";
    $txtQuery .= " FROM amap_contrat c, amap_personne p, amap_type_contrat t, amap_evenements e";
    $txtQuery .= " WHERE c.id_personne=p.id_personne";
    $txtQuery .= " AND c.id_saison=".$idSaison;
    $txtQuery .= " AND c.id_produit=".$idProduit;
    $txtQuery .= " AND t.id_type=c.id_type";
    $txtQuery .= " AND e.id_evenement=c.debut_contrat";
    $txtQuery .= " ORDER BY c.id_contrat";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
    $out .= "\t\t<tr bgcolor='#DBE1C5'>\n";
    $out .= "\t\t\t<td><strong><center>nom</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>1/2 panier</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>type</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>date d&eacute;but</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>nb distributions</center></strong></td>\n";
    $out .= "\t\t\t<td>&nbsp;</td>\n";
    $out .= "\t\t</tr>\n";

    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $out .= "\t\t<tr style='background-color: #eeeeee;'>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idContrat=".$tabUnEnregistrement['id_contrat']."&idSaison=".$idSaison."&idProduit=".$idProduit,"ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement['prenom']." ".$tabUnEnregistrement['nom']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idContrat=".$tabUnEnregistrement['id_contrat']."&idSaison=".$idSaison."&idProduit=".$idProduit,"ecrire")." '>\n";
      if ($tabUnEnregistrement['demi_panier'] == 1)
        $out .= "\t\t\t\t\t<center>oui</center>\n";
      else
        $out .= "\t\t\t\t\t<center>&nbsp;</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idContrat=".$tabUnEnregistrement['id_contrat']."&idSaison=".$idSaison."&idProduit=".$idProduit,"ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement['label_type']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idContrat=".$tabUnEnregistrement['id_contrat']."&idSaison=".$idSaison."&idProduit=".$idProduit,"ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement['dateEvenement']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=modif&idContrat=".$tabUnEnregistrement['id_contrat']."&idSaison=".$idSaison."&idProduit=".$idProduit,"ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement['nb_distribution']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_contrats', "action=suppr&idContrat=".$tabUnEnregistrement['id_contrat']."&idSaison=".$idSaison."&idProduit=".$idProduit,"ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center><img src='"._DIR_PLUGIN_AMAP."img_pack/b_drop.png' /></center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t</tr>\n";
    } // fin while
    $out .= "\t</table>\n";
  } // fin if ( ($idSaison!=0) && ($idProduit!=0) )
  else if ( ($idSaison!=0) && ($idProduit==0) )
  {
    return "choisir un produit";
  }
  else
  {
    return "choisir une saison et un produit";
  }

  return $out;

} // function table_amap_lister_liste_contrat


//=========================================================================
//=========================================================================
//
function table_amap_getmodif_liste_contrat()
{
  $out = '';
  $hiddens = '';
  $idLieu = 0;

  $txtQuery = "SELECT c.debut_contrat, c.id_contrat, c.id_personne, c.demi_panier, c.nb_distribution, p.prenom, p.nom, c.id_type FROM amap_contrat c, amap_personne p";
  $txtQuery .= " WHERE c.id_personne=p.id_personne";
  $txtQuery .= " AND c.id_saison=".$_GET ['idSaison'];
  $txtQuery .= " AND c.id_produit=".$_GET['idProduit'];
  $txtQuery .= " AND c.id_contrat=".$_GET['idContrat'];
  $sqlResult_1 = sql_query($txtQuery);

  if ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>id contrat : </td>\n";
    $out .= "\t\t<td>".$_GET['idContrat']."</td>\n";
    $out .= "\t</tr>";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>nom : </td>\n";
    $out .= "\t\t<td>\n";
    $out .= "\t\t\t<select name='idPersonne' >\n";

    // on fait une boucle de recherche sur les différentes personnes
    $txtQuery = "SELECT p.id_personne, p.prenom, p.nom";
    $txtQuery .= ", (SELECT count(po.id_produit) FROM amap_produit po";
    $txtQuery .= "   WHERE po.id_paysan=p.id_personne";
    $txtQuery .= "   AND po.id_produit=".$_GET['idProduit'];
    $txtQuery .= "   GROUP BY po.id_paysan";
    $txtQuery .= " ) As SonProduit";
    $txtQuery .= " FROM amap_personne p";
    $sqlResult_2 = sql_query($txtQuery);

    while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
    {
      if ($tabUnEnregistrement_2['SonProduit'] == 0)
      {
        if ($tabUnEnregistrement_2['id_personne'] == $tabUnEnregistrement_1['id_personne'])
          $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
        else
          $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
      }
    } 
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>panier (ou demi-panier) ? </td>\n";
    if ($tabUnEnregistrement_1['demi_panier'] == 1)
      $out .= "\t\t<td><input type='checkbox' name='panier' checked > (cocher pour un demi-panier)</td>\n";
    else
      $out .= "\t\t<td><input type='checkbox' name='panier' > (cocher pour un demi-panier)</td>\n";
    $out .= "\t</tr>";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>type de contrat: </td>\n";
    $out .= "\t\t<td>\n";
    $out .= "\t\t\t<select name='idType' >\n";

    // on fait une boucle de recherche sur les différentes personnes
    $txtQuery = "SELECT * FROM amap_type_contrat";
    $sqlResult_2 = sql_query($txtQuery);

    while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
    {
      if ($tabUnEnregistrement_2['id_type'] == $tabUnEnregistrement_1['id_type'])
        $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement_2['id_type']."'>".$tabUnEnregistrement_2['label_type']."</option>\n";
      else
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_type']."'>".$tabUnEnregistrement_2['label_type']."</option>\n";
    } 
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>date debut : </td>\n";
    $out .= "\t\t<td>\n";
    $out .= "\t\t\t<select name='debutContrat' >\n";

    // boucle sur les amap_evenements
    $txtQuery = "SELECT e.id_evenement, DATE_FORMAT(e.date_evenement, '%d-%m-%Y') As dateEvenement";
    $txtQuery .= " FROM amap_evenements e, amap_produit_distribution d";
    $txtQuery .= " WHERE d.id_evenement=e.id_evenement";
    $txtQuery .= " AND e.id_saison=".$_GET ['idSaison'];
    $txtQuery .= " AND d.id_produit=".$_GET['idProduit'];
    $txtQuery .= " ORDER BY e.date_evenement";
    $sqlResult_2 = sql_query($txtQuery);

    while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
    {
      if ($tabUnEnregistrement_2['id_evenement'] == $tabUnEnregistrement_1['debut_contrat'])
        $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement_2['id_evenement']."'>".$tabUnEnregistrement_2['dateEvenement']."</option>\n";
      else
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_evenement']."'>".$tabUnEnregistrement_2['dateEvenement']."</option>\n";
    }
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>nb distributions : </td>\n";
    $out .= "\t\t<td><input type='text' name='nbDistribution' value='".$tabUnEnregistrement_1['nb_distribution']."' size='5' maxlength='5' ></td>\n";
    $out .= "\t</tr>";

    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$_GET['idSaison']."' />\n";
    $hiddens .= "\t<input type='hidden' name='idProduit' value='".$_GET['idProduit']."' />\n";
    $hiddens .= "\t<input type='hidden' name='idContrat' value='".$_GET['idContrat']."' />\n";
 
    return generer_url_entite('amap_contrats', "action=maj","post_ecrire")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";
  }

} // function table_amap_getmodif_liste_contrat()


//=========================================================================
//=========================================================================
//
function table_amap_get_liste_contrat($idSaison, $idProduit)
{
  $out = '';
  $hiddens = '';

  if ( ($idSaison!=0) && ($idProduit!=0) )
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>nom personne: </td>\n";
    $out .= "\t\t<td>\n";
    $out .= "\t\t\t<select name='idPersonne' >\n";

    // on fait une boucle de recherche sur les différentes personnes
    $txtQuery = "SELECT p1.id_personne, p1.prenom, p1.nom";
    $txtQuery .= ", (SELECT count(po.id_produit) FROM amap_produit po";
    $txtQuery .= "   WHERE po.id_paysan=p1.id_personne";
    $txtQuery .= "   AND po.id_produit=".$idProduit;
    $txtQuery .= "   GROUP BY po.id_paysan";
    $txtQuery .= " ) As SonProduit";
    $txtQuery .= " FROM amap_personne p1";
    $txtQuery .= " WHERE p1.id_personne NOT IN (";
    $txtQuery .= "   SELECT p2.id_personne FROM amap_personne p2, amap_contrat c";
    $txtQuery .= "   WHERE p2.id_personne=c.id_personne";
    $txtQuery .= "   AND c.id_saison=".$idSaison;
    $txtQuery .= "   AND c.id_produit=".$idProduit;
    $txtQuery .= " ) ORDER BY nom";
    $sqlResult = sql_query($txtQuery);

    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      if ($tabUnEnregistrement['SonProduit'] == 0)
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_personne']."'>".$tabUnEnregistrement['prenom']." ".$tabUnEnregistrement['nom']."</option>\n";
    } 
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>panier (ou demi-panier) ? </td>\n";
    $out .= "\t\t<td><input type='checkbox' name='panier' > (cocher pour un demi-panier)</td>\n";
    $out .= "\t</tr>";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>type de contrat: </td>\n";
    $out .= "\t\t<td>\n";
    $out .= "\t\t\t<select name='idType' >\n";

    // on fait une boucle de recherche sur les différentes personnes
    $txtQuery = "SELECT * FROM amap_type_contrat";
    $sqlResult = sql_query($txtQuery);

    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_type']."'>".$tabUnEnregistrement['label_type']."</option>\n";
    } 
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>date debut : </td>\n";
    $out .= "\t\t<td>\n";
    $out .= "\t\t\t<select name='debutContrat' >\n";

    // boucle sur les amap_evenements
    $txtQuery = "SELECT e.id_evenement, DATE_FORMAT(e.date_evenement, '%d-%m-%Y') As dateEvenement";
    $txtQuery .= " FROM amap_evenements e, amap_produit_distribution d";
    $txtQuery .= " WHERE d.id_evenement=e.id_evenement";
    $txtQuery .= " AND e.id_saison=".$idSaison;
    $txtQuery .= " AND d.id_produit=".$idProduit;
    $txtQuery .= " ORDER BY e.date_evenement";
    $sqlResult_2 = sql_query($txtQuery);

    while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
    {
      $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_evenement']."'>".$tabUnEnregistrement_2['dateEvenement']."</option>\n";
    }
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>nb distributions : </td>\n";
    $out .= "\t\t<td><input type='text' name='nbDistribution' size='5' maxlength='5' ></td>\n";
    $out .= "\t</tr>";

    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$idSaison."' />\n";
    $hiddens .= "\t<input type='hidden' name='idProduit' value='".$idProduit."' />\n";

    return generer_url_entite('amap_contrats', "action=add","post_ecrire")
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
} // function table_amap_get_liste_contrat()


//=========================================================================
//=========================================================================
//
function table_amap_modif_post_liste_contrat()
{
  $txtQuery = "UPDATE amap_contrat SET ";
  $txtQuery .= "id_personne='".$_POST['idPersonne']."', ";
  if ($_POST['panier'] == 'on')
    $txtQuery .= "demi_panier='1', ";
  else
    $txtQuery .= "demi_panier='0' ,";
  $txtQuery .= "id_type='".$_POST['idType']."', ";
  $txtQuery .= "debut_contrat='".$_POST['debutContrat']."', ";
  $txtQuery .= "nb_distribution='".$_POST['nbDistribution']."' ";
  $txtQuery .= " WHERE id_saison=".$_POST['idSaison'];
  $txtQuery .= " AND id_produit=".$_POST['idProduit'];
  $txtQuery .= " AND id_contrat=".$_POST['idContrat'];

  $sqlResult = sql_query($txtQuery);

  return "Mise à jour dans la table amap_contrat " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: ".$_POST['idContrat']." "));
} //function table_amap_modif_post_liste_contrat


//=========================================================================
//=========================================================================
//
function table_amap_post_liste_contrat()
{
  $description = array();
  $contenu = array();

  if ($_POST['idProduit']) { $description[]='id_produit'; $contenu[]="'".$_POST['idProduit']."'"; }
  if ($_POST['idSaison']) { $description[]='id_saison'; $contenu[]="'".$_POST['idSaison']."'"; }
  if ($_POST['idPersonne']) { $description[]='id_personne'; $contenu[]="'".$_POST['idPersonne']."'"; }
  if ($_POST['panier'] == 'on') { $description[]='demi_panier'; $contenu[]="'1'"; }
  if ($_POST['idType']) { $description[]='id_type'; $contenu[]="'".$_POST['idType']."'"; }
  if ($_POST['debutContrat']) { $description[]='debut_contrat'; $contenu[]="'".$_POST['debutContrat']."'"; }
  if ($_POST['nbDistribution']) { $description[]='nb_distribution'; $contenu[]="'".$_POST['nbDistribution']."'"; }

  $sqlResult = sql_insert('amap_contrat',
                 "(" . join(', ', $description) . ")",
                 "(" . join(', ', $contenu) . ")");

  return "Insertion dans la table amap_contrat " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: ".$sqlResult." "));
} //function table_amap_post_liste_contrat

//=========================================================================
//=========================================================================
//
function table_amap_suppr_liste_contrat()
{
  $txtQuery = "DELETE FROM amap_contrat";
  $txtQuery .= " WHERE id_contrat=".$_GET['idContrat'];

  $sqlResult = sql_query($txtQuery);

  return "Suppression dans la table amap_contrat " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_GET['idContrat'].") "));
} //function table_amap_suppr_liste_contrat

?>
