<?php
##########################################################################
#
#           Fichier des fonctions paniers du plugin Amap pour SPIP
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
function table_amap_lister_panier($idSaison, $idDistrib, $idProduit)
{
  $out = '';

  pipeline('exec_init',array('args'=>array('exec'=>'amap_paniers'),'data'=>''));

  if ( isset($idSaison) && isset($idDistrib) && isset($idProduit) )
  {
    // boucle sur la table spip_amap_panier
    $txtQuery = "SELECT id_element, id_famille, id_variete, quantite, poids FROM spip_amap_panier";
    $txtQuery .= " WHERE id_evenement=".$idDistrib;
    $txtQuery .= " AND id_produit=".$idProduit;
    $txtQuery .= " ORDER BY id_element";
    $sqlResult_1 = sql_query($txtQuery);

    $out .= "\t<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
    $out .= "\t\t<tr bgcolor='#DBE1C5'>\n";
    $out .= "\t\t\t<td><strong>famille</strong></td>\n";
    $out .= "\t\t\t<td><strong>vari&eacute;t&eacute;</strong></td>\n";
    $out .= "\t\t\t<td><strong>quantite</strong></td>\n";
    $out .= "\t\t\t<td><strong>poids</strong></td>\n";
    $out .= "\t\t\t<td>&nbsp;</td>\n";
    $out .= "\t\t</tr>\n";

    while ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))
    {
      $out .= "\t\t<tr style='background-color: #eeeeee;'>\n";
      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_paniers', "action=modif&idSaison=".$idSaison."&idDistrib=".$idDistrib."&idProduit=".$idProduit."&idElement=".$tabUnEnregistrement_1['id_element'],"ecrire")." '>\n";

      // début deuxième boucle pour rechercher le label de la famille
      $txtQuery = "SELECT label_famille FROM spip_amap_famille_variete";
      $txtQuery .= " WHERE id_produit=".$idProduit;
      $txtQuery .= " AND id_famille=".$tabUnEnregistrement_1['id_famille'];
      $sqlResult_2 = sql_query($txtQuery);

      if ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2)) {
        $out .= "\t\t\t\t\t".$tabUnEnregistrement_2['label_famille']."\n";
      }
      else {
        $out .= "\t\t\t\t\t".$tabUnEnregistrement_1['id_famille']."\n";
      }

      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";


      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";

      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_paniers', "action=modif&idSaison=".$idSaison."&idDistrib=".$idDistrib."&idProduit=".$idProduit."&idElement=".$tabUnEnregistrement_1['id_element'],"ecrire")." '>\n";

      // début troisième boucle pour rechercher le label de la variété
      $txtQuery = "SELECT label_variete FROM spip_amap_variete";
      $txtQuery .= " WHERE id_famille=".$tabUnEnregistrement_1['id_famille'];
      $txtQuery .= " AND id_variete=".$tabUnEnregistrement_1['id_variete'];
      $sqlResult_2 = sql_query($txtQuery);

      if ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2)) {
        $out .= "\t\t\t\t\t".$tabUnEnregistrement_2['label_variete']."\n";
      }
      else {
        $out .= "\t\t\t\t\t".$tabUnEnregistrement_1['id_variete']."\n";
      }

      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_paniers', "action=modif&idSaison=".$idSaison."&idDistrib=".$idDistrib."&idProduit=".$idProduit."&idElement=".$tabUnEnregistrement_1['id_element'],"ecrire")." '>\n";
      $out .= "\t\t\t\t\t".$tabUnEnregistrement_1['quantite']."\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_paniers', "action=modif&idSaison=".$idSaison."&idDistrib=".$idDistrib."&idProduit=".$idProduit."&idElement=".$tabUnEnregistrement_1['id_element'],"ecrire")." '>\n";
      $out .= "\t\t\t\t\t".$tabUnEnregistrement_1['poids']."\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_paniers', "action=suppr&idSaison=".$idSaison."&idDistrib=".$idDistrib."&idProduit=".$idProduit."&idElement=".$tabUnEnregistrement_1['id_element'],"ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center><img src='"._DIR_PLUGIN_AMAP."img_pack/b_drop.png' /></center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t</tr>\n";
    } // fin while
    $out .= "\t</table>\n";
  } // fin if ( isset($idSaison) && isset($idDistrib) && isset($idProduit) )
  else
  {
    $out .= "choisir une saison, un produit et une distribution";
  }

  return $out;

} // function table_amap_lister_panier


//=========================================================================
//=========================================================================
//
function table_amap_getmodif_panier($page, $table)
{
  $out = '';
  $hiddens = '';

  // boucle sur la table spip_amap_panier
  $txtQuery = "SELECT id_famille, id_variete, quantite, poids FROM spip_amap_panier";
  $txtQuery .= " WHERE id_evenement=".$_GET['idDistrib'];
  $txtQuery .= " AND id_produit=".$_GET['idProduit'];
  $txtQuery .= " AND id_element=".$_GET['idElement'];
  $sqlResult_1 = sql_query($txtQuery);

  if ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tfamille: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<select name='idFamille'>\n";

    // deuxième boucle pour afficher le label de la famille
    $txtQuery = "SELECT id_famille, label_famille FROM spip_amap_famille_variete";
    $txtQuery .= " WHERE id_produit=".$_GET['idProduit'];
    $sqlResult_2 = sql_query($txtQuery);

    while($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
    {
      if ($tabUnEnregistrement_2['id_famille'] == $tabUnEnregistrement_1['id_famille']) {
        $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement_2['id_famille']."'>".$tabUnEnregistrement_2['label_famille']."</option>\n";
      }
      else {
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_famille']."'>".$tabUnEnregistrement_2['label_famille']."</option>\n";
      }
    }
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tvari&eacute;t&eacute;: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<select name='idVariete'>\n";
    $out .= "\t\t\t\t<option value=''>------</option>\n";

    // troisième boucle pour afficher le label de la variété
    $txtQuery = "SELECT id_variete, label_variete FROM spip_amap_variete";
    $txtQuery .= " WHERE id_famille=".$tabUnEnregistrement_1['id_famille'];

    $sqlResult_2 = sql_query($txtQuery);

    while($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
    {
      if ($tabUnEnregistrement_2['id_variete']==$tabUnEnregistrement_1['id_variete']) {
        $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement_2['id_variete']."'>".$tabUnEnregistrement_2['label_variete']."</option>\n";
      }
      else {
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_variete']."'>".$tabUnEnregistrement_2['label_variete']."</option>\n";
      }
    }
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tquantit&eacute;: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input type='text' name='quantite' value='".$tabUnEnregistrement_1['quantite']."'>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tpoids: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input type='text' name='poids' value='".$tabUnEnregistrement_1['poids']."'>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";
  } // fin if ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))

  $hiddens .= "\t<input type='hidden' name='idSaison' value='".$_GET['idSaison']."' />\n";
  $hiddens .= "\t<input type='hidden' name='idDistrib' value='".$_GET['idDistrib']."' />\n";
  $hiddens .= "\t<input type='hidden' name='idProduit' value='".$_GET['idProduit']."' />\n";
  $hiddens .= "\t<input type='hidden' name='idElement' value='".$_GET['idElement']."' />\n";

  return generer_url_ecrire("$page", "table=$table", "action=maj")
                            ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";

} // function table_amap_getmodif_panier()


//=========================================================================
//=========================================================================
//
function table_amap_get_panier($page, $table, $idSaison, $idDistrib, $idProduit)
{
  $out = '';
  $hiddens = '';

  if ( isset($idSaison) && isset($idDistrib) && isset($idProduit) )
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tfamille: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<select name='idFamille' onChange='this.form.submit()'>\n";

    // deuxième boucle pour afficher le label de la famille
    $txtQuery = "SELECT id_famille, label_famille FROM spip_amap_famille_variete";
    $txtQuery .= " WHERE id_produit=".$idProduit;
    $sqlResult = sql_query($txtQuery);
 
   $out .= "\t\t\t\t<option value=''>------</option>\n";

    while($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      if ($tabUnEnregistrement['id_famille'] == $_POST['idFamille'])
      {
        $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement['id_famille']."'>".$tabUnEnregistrement['label_famille']."</option>\n";
      }
      else
      {
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_famille']."'>".$tabUnEnregistrement['label_famille']."</option>\n";
      }
    }
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tvari&eacute;t&eacute;: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<select name='idVariete'>\n";
    $out .= "\t\t\t\t<option value=''>------</option>\n";

    // troisième boucle pour afficher le label de la variété
    $txtQuery = "SELECT id_variete, label_variete FROM spip_amap_famille_variete";
    $txtQuery .= " WHERE id_famille=".$_POST['idFamille'];

    $sqlResult = sql_query($txtQuery);

    while($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_variete']."'>".$tabUnEnregistrement['label_variete']."</option>\n";
    }
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";
    
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tquantit&eacute;: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input type='text' name='quantite' value=''>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tpoids: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input type='text' name='poids' value=''>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";


    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$idSaison."' />\n";
    $hiddens .= "\t<input type='hidden' name='idDistrib' value='".$idDistrib."' />\n";
    $hiddens .= "\t<input type='hidden' name='idProduit' value='".$idProduit."' />\n";

	return generer_url_ecrire("$page", "table=$table", "action=add")
                             ."<table>\n".$out
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";
  } // fin   if ( isset($idSaison) && isset($idDistrib) && isset($idProduit) )
  else
  {
    return "choisir une saison, un produit et une distribution";
  }
} // function table_amap_get_panier()


//=========================================================================
//=========================================================================
//
function table_amap_modif_post_panier()
{
  $txtQuery = "UPDATE spip_amap_panier SET ";
  $txtQuery .= "id_famille='".$_POST['idFamille']."', ";
  if ($_POST['idVariete'])
    $txtQuery .= "id_variete='".$_POST['idVariete']."', ";
  else
      $txtQuery .= "id_variete=null, ";
  if ($_POST['quantite'] != '')
    $txtQuery .= "quantite='".$_POST['quantite']."', ";
  else
      $txtQuery .= "quantite=null, ";
  if ($_POST['poids'] != '')
    $txtQuery .= "poids='".$_POST['poids']."' ";
  else
      $txtQuery .= "poids=null ";
  $txtQuery .= " WHERE id_evenement=".$_POST['idDistrib'];
  $txtQuery .= " AND id_produit=".$_POST['idProduit'];
  $txtQuery .= " AND id_element=".$_POST['idElement'];

  $sqlResult = sql_query($txtQuery);

  return "Mise à jour dans la table spip_amap_panier " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_POST['idProduit'].", ".$_POST['idDistrib'].", ".$_POST['idElement'].") "));
} //function table_amap_modif_post_panier


//=========================================================================
//=========================================================================
//
function table_amap_post_panier()
{
  $nbElement = 0;
  // on calcule la position d'insertion dans la table
  $txtQuery = "SELECT count(id_element) As NbElement FROM spip_amap_panier 
		WHERE id_produit=".$_POST['idProduit']."
		AND id_evenement=".$_POST['idDistrib'];
  $sqlResult_1 = sql_query($txtQuery);

  if ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))
  {
    $nbElement = $tabUnEnregistrement_1['NbElement'] + 1;

    $txtQuery = "INSERT INTO spip_amap_panier VALUES (";
    $txtQuery .= "'".$_POST['idProduit']."', ";
    $txtQuery .= "'".$_POST['idDistrib']."', ";
    $txtQuery .= "'".$nbElement."', ";
    $txtQuery .= "'".$_POST['idFamille']."', ";
    if ($_POST['idVariete'])
      $txtQuery .= "'".$_POST['idVariete']."', ";
    else
      $txtQuery .= "null, ";
    if ($_POST['quantite'] != '')
      $txtQuery .= "'".$_POST['quantite']."', ";
    else
      $txtQuery .= "null, ";
    if ($_POST['poids'] != '')
      $txtQuery .= "'".$_POST['poids']."')";
    else
      $txtQuery .= "null)";

    $sqlResult_2 = sql_query($txtQuery);

    return "Insertion dans la table spip_amap_panier " .
      (!$sqlResult_2 ? ': erreur !!' : ("sous le numero: (".$_POST['idProduit'].", ".$_POST['idDistrib'].", ".$nbElement.") "));
  }
} //function table_amap_post_panier

//=========================================================================
//=========================================================================
//
function table_amap_suppr_panier()
{
  $txtQuery = "DELETE FROM spip_amap_panier";
  $txtQuery .= " WHERE id_evenement=".$_GET['idDistrib'];
  $txtQuery .= " AND id_produit=".$_GET['idProduit'];
  $txtQuery .= " AND id_element=".$_GET['idElement'];

  $sqlResult = sql_query($txtQuery);

  return "Suppression dans la table spip_amap_panier " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_GET['idProduit'].", ".$_GET['idDistrib'].", ".$_GET['idElement'].") "));
} //function table_amap_suppr_panier

?>
