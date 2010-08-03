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
function table_amap_lister_variete($idProduit, $idFamille)
{
  $out = '';

  if ( isset($idProduit) && isset($idFamille) )
  {
    // boucle sur la table spip_amap_famille_variete
    $txtQuery = "SELECT id_variete, label_variete FROM spip_amap_variete";
    $txtQuery .= " WHERE id_famille=".$idFamille;
    $txtQuery .= " ORDER BY id_variete";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
      $out .= "\t\t<tr bgcolor='#DBE1C5'>\n";
      $out .= "\t\t\t<td><strong>id_variete</strong></td>\n";
      $out .= "\t\t\t<td><strong>label_variete</strong></td>\n";
    $out .= "\t\t\t<td>&nbsp;</td>\n";
      $out .= "\t\t</tr>\n";

    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $out .= "\t\t<tr style='background-color: #eeeeee;'>\n";
      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_paniers', "action=modif&idProduit=".$idProduit."&idFamille=".$idFamille."&idVariete=".$tabUnEnregistrement['id_variete']."&table=spip_amap_variete","ecrire")." '>\n";
      $out .= "\t\t\t\t\t".$tabUnEnregistrement['id_variete']."\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_paniers', "action=modif&idProduit=".$idProduit."&idFamille=".$idFamille."&idVariete=".$tabUnEnregistrement['id_variete']."&table=spip_amap_variete","ecrire")." '>\n";
      $out .= "\t\t\t\t\t".$tabUnEnregistrement['label_variete']."\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_paniers', "action=suppr&idProduit=".$idProduit."&idFamille=".$idFamille."&idVariete=".$tabUnEnregistrement['id_variete']."&table=spip_amap_variete","ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center><img src='"._DIR_PLUGIN_AMAP."img_pack/b_drop.png' /></center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t</tr>\n";
    } // fin while
    $out .= "\t</table>\n";
  } // fin if ( isset($idSaison) && isset($idDistrib) && isset($idProduit) )
  else
  {
    $out .= "choisir un produit et une famille";
  }

  return $out;

} // function table_amap_lister_variete


//=========================================================================
//=========================================================================
//
function table_amap_getmodif_variete()
{
  $out = '';
  $hiddens = '';

  // boucle sur la table spip_amap_famille_variete
  $txtQuery = "SELECT label_variete FROM spip_amap_variete";
  $txtQuery .= " WHERE id_famille=".$_GET['idFamille'];
  $txtQuery .= " AND id_variete=".$_GET['idVariete'];
  $sqlResult_1 = sql_query($txtQuery);

  if ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tproduit: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";

    // première boucle pour afficher le label du produit
    $txtQuery = "SELECT label_produit FROM spip_amap_produit";
    $txtQuery .= " WHERE id_produit=".$_GET['idProduit'];
    $sqlResult_2 = sql_query($txtQuery);

    if($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))  
      $out .= "\t\t\t\t".$tabUnEnregistrement_2['label_produit']."\n";
    else
      $out .= "\t\t\t\t".$_GET['idProduit']."\n";

    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tfamille: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";

    // deuxième boucle pour afficher le label de la famille
    $txtQuery = "SELECT label_famille FROM spip_amap_famille_variete";
    $txtQuery .= " WHERE id_produit=".$_GET['idProduit'];
    $txtQuery .= " AND id_famille=".$_GET['idFamille'];
    $sqlResult_2 = sql_query($txtQuery);

    if($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))  
      $out .= "\t\t\t\t".$tabUnEnregistrement_2['label_famille']."\n";
    else
      $out .= "\t\t\t".$_GET['idFamille']."\n";

    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";

    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tid_variete: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input type='text' name='nouvIdVariete' value='".$_GET['idVariete']."' size='3' maxlength='3' >\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tlabel: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input type='text' name='labelVariete' value='".$tabUnEnregistrement_1['label_variete']."' size='30' maxlength='30' >\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

  } // fin if ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))

  $hiddens .= "\t<input type='hidden' name='idProduit' value='".$_GET['idProduit']."' />\n";
  $hiddens .= "\t<input type='hidden' name='idFamille' value='".$_GET['idFamille']."' />\n";
  $hiddens .= "\t<input type='hidden' name='ancIdVariete' value='".$_GET['idVariete']."' />\n";
  $hiddens .= "\t<input type='hidden' name='table' value='varietes' />\n";

  return generer_url_ecrire("amap_paniers", "table=spip_amap_famille_variete", "action=maj")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";

} // function table_amap_getmodif_variete()


//=========================================================================
//=========================================================================
//
function table_amap_get_variete($idProduit, $idFamille)
{
  $out = '';
  $hiddens = '';

  if ( isset($idProduit) && isset($idFamille) )
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\tlabel de la vari&eacute;t&eacute;: \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input type='text' name='labelVariete' value='' size='30' maxlength='30' >\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $hiddens .= "\t<input type='hidden' name='idProduit' value='".$idProduit."' />\n";
    $hiddens .= "\t<input type='hidden' name='idFamille' value='".$idFamille."' />\n";
    $hiddens .= "\t<input type='hidden' name='table' value='varietes' />\n";

	return generer_url_ecrire("amap_paniers", "table=spip_amap_famille_variete", "action=add")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";
  } // fin   if ( isset($idSaison) && isset($idDistrib) && isset($idProduit) )
  else
  {
    return "choisir un produit";
  }
} // function table_amap_get_variete()


//=========================================================================
//=========================================================================
//
function table_amap_modif_post_variete()
{
  $txtQuery = "UPDATE spip_amap_variete SET ";
  $txtQuery .= "id_variete='".$_POST['nouvIdVariete']."', ";
  $txtQuery .= "label_variete='".$_POST['labelVariete']."' ";
  $txtQuery .= " WHERE id_famille=".$_POST['idFamille'];
  $txtQuery .= " AND id_variete=".$_POST['ancIdVariete'];

  $sqlResult = sql_query($txtQuery);

  return "Mise à jour dans la table spip_amap_variete " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_POST['idProduit'].", ".$_POST['idFamille'].", ".$_POST['ancIdVariete'].") "));
} //function table_amap_modif_post_variete


//=========================================================================
//=========================================================================
//
function table_amap_post_variete()
{
  $description = array();
  $contenu = array();

  if ($_POST['labelVariete']) { $description[]='label_variete'; $contenu[]="'".$_POST['labelVariete']."'"; }
  if ($_POST['idFamille']) { $description[]='id_famille'; $contenu[]="'".$_POST['idFamille']."'"; }

  $sqlResult = sql_insert('spip_amap_variete',
                 "(" . join(', ', $description) . ")",
                 "(" . join(', ', $contenu) . ")");

  return "Insertion dans la table spip_amap_variete " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: ".$sqlResult." "));
} //function table_amap_post_variete

//=========================================================================
//=========================================================================
//
function table_amap_suppr_variete()
{
  $txtQuery = "DELETE FROM spip_amap_variete";
  $txtQuery .= " WHERE id_famille=".$_GET['idFamille'];
  $txtQuery .= " AND id_variete=".$_GET['idVariete'];

  $sqlResult = sql_query($txtQuery);

  return "Suppression dans la table spip_amap_variete " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: (".$_GET['idProduit'].", ".$_GET['idFamille'].", ".$_GET['idVariete'].") "));
} //function table_amap_suppr_variete

?>
