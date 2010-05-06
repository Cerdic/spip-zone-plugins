<?php
##########################################################################
#
#           Fichier des fonctions paysans du plugin Amap pour SPIP
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
function table_amap_lister_paysan()
{
  $out = '';
  $hiddens = '';

  pipeline('exec_init',array('args'=>array('exec'=>'amap_distributions'),'data'=>''));

  // on affiche l'ensemble des distributions de la saison
  // 1er boucle sur la table amap_produit_distribution
  $txtQuery = "SELECT * FROM amap_produit";
  $sqlResult_1 = sql_query($txtQuery);

  $trad_id_produit = _T('amap:id_produit');
  $trad_paysan = _T('amap:paysan');
  $trad_label_produit = _T('amap:label_produit');

  $out .= "\t<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
  $out .= "\t\t<tr bgcolor='#DBE1C5'>\n";
  $out .= "\t\t\t<td><strong><center>$trad_id_produit</center></strong></td>\n";
  $out .= "\t\t\t<td><strong><center>$trad_paysan</center></strong></td>\n";
  $out .= "\t\t\t<td><strong><center>$trad_label_produit</center></strong></td>\n";
  $out .= "\t\t</tr>\n";

  while ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))
  {
    $out .= "\t\t<tr style='background-color: #eeeeee;'>\n";
    $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t\t<a href='".generer_url_entite('amap_config', "action=edit&id_ligne=".$tabUnEnregistrement_1['id_produit']."&table=amap_produit", "ecrire")." '>\n";
    $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement_1['id_produit']."</center>\n";
    $out .= "\t\t\t\t</a>\n";
    $out .= "\t\t\t</td>\n";
    $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t\t<a href='".generer_url_entite('amap_config', "action=edit&id_ligne=".$tabUnEnregistrement_1['id_produit']."&table=amap_produit", "ecrire")." '>\n";

    // on recherche le nom du paysan dans la table amap_personne...
    $txtQuery = "SELECT prenom, nom FROM amap_personne ";
    $txtQuery .= "WHERE id_personne=".$tabUnEnregistrement_1['id_paysan'];
    $sqlResult_2 = sql_query($txtQuery);

    if ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</center> \n";
    else
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement_1['id_paysan']."</center> \n";

    $out .= "\t\t\t\t</a>\n";
    $out .= "\t\t\t</td>\n";

    $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t\t<a href='".generer_url_entite('amap_config', "action=edit&id_ligne=".$tabUnEnregistrement_1['id_produit']."&table=amap_produit", "ecrire")." '>\n";
    $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement_1['label_produit']."</center>\n";
    $out .= "\t\t\t\t</a>\n";
    $out .= "\t\t\t</td>\n";
    $out .= "\t\t</tr>\n";
      
  } // fin while
  $out .= "\t</table>\n";
  
  return $out;

} // function table_amap_lister_produit_distributions


//=========================================================================
//=========================================================================
//
function table_amap_getmodif_paysan()
{
  $out = '';
  $hiddens = '';

  // boucle sur la table amap_produit
  $txtQuery = "SELECT id_paysan, label_produit FROM amap_produit";
  $txtQuery .= " WHERE id_produit=".$_GET['id_ligne'];
  $sqlResult_1 = sql_query($txtQuery);

  while ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))
  {
  $trad_id_produit = _T('amap:id_produit');
  $trad_paysan = _T('amap:paysan');

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t $trad_id_produit : \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t".$_GET['idProduit']."\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\ $trad_paysan : \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";

    // boucle sur la table personne    
    $out .= "\t\t\t<select name='id_paysan'>\n";
    $out .= "\t\t\t\t<option value=''>------</option>\n";

    // on recherche le nom du paysan dans la table amap_personne...
    $txtQuery = "SELECT id_personne, prenom, nom FROM amap_personne ";
    $sqlResult_2 = sql_query($txtQuery);

    while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
    {
      if ($tabUnEnregistrement_2['id_personne'] == $tabUnEnregistrement_1['id_paysan'])
        $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
      else
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
    }
    $trad_label_produit = _T('amap:label_produit');
  
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";

    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t $trad_label_produit : \n";
    $out .= "\t\t</td>\n";
    $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
    $out .= "\t\t\t<input type='text' name='label_produit' value='".$tabUnEnregistrement_1['label_produit']."' size='20' maxlength='20' \n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>\n";
 
    $hiddens .= "\t<input type='hidden' name='id_ligne' value='".$_GET['id_ligne']."' />\n";

    return generer_url_entite('amap_config', "table=amap_produit&action=maj","post_ecrire")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";
  } // fin  while ($tabUnEnregistrement_1 = sql_fetch($sqlResult))

} // function table_amap_getmodif_paysan()


//=========================================================================
//=========================================================================
//
function table_amap_get_paysan()
{
  $out = '';
  $hiddens = '';

  $trad_paysan = _T('amap:paysan');

  $out .= "\t<tr bgcolor='#DBE1C5'>";
  $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
  $out .= "\t\t\t $trad_paysan : \n";
  $out .= "\t\t</td>\n";
  $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";

  // boucle sur la table personne    
  $out .= "\t\t\t<select name='id_paysan'>\n";
  $out .= "\t\t\t\t<option value=''>------</option>\n";

  // on recherche le nom du paysan dans la table amap_personne...
  $txtQuery = "SELECT id_personne, prenom, nom FROM amap_personne ";
  $sqlResult_2 = sql_query($txtQuery);

  while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
  {
    $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
  }
  $trad_label_produit = _T('amap:label_produit');

  $out .= "\t\t\t</select>\n";
  $out .= "\t\t</td>\n";
  $out .= "\t</tr>\n";

  $out .= "\t<tr bgcolor='#DBE1C5'>";
  $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
  $out .= "\t\t\t $trad_label_produit : \n";
  $out .= "\t\t</td>\n";
  $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
  $out .= "\t\t\t<input type='text' name='label_produit' value='".$tabUnEnregistrement_1['label_produit']."' size='20' maxlength='20' \n";
  $out .= "\t\t</td>\n";
  $out .= "\t</tr>\n";

  return generer_url_entite('amap_config', "table=amap_produit&action=add","post_ecrire")
                               ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";

} // function table_amap_get_paysan()


//=========================================================================
//=========================================================================
//
function table_amap_modif_post_paysan()
{
  $txtQuery = "UPDATE amap_produit SET ";
  if ($_POST['id_paysan'])
    $txtQuery .= "id_paysan='".$_POST['id_paysan']."', ";
  else
    $txtQuery .= "id_paysan=null, ";
  $txtQuery .= "label_produit='".$_POST['label_produit']."' ";
  $txtQuery .= " WHERE id_produit=".$_POST['id_ligne'];

  $sqlResult = sql_query($txtQuery);

  return "Mise à jour dans la table amap_produit " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: ".$_POST['id_ligne']." "));
} //function table_amap_modif_post_paysan

//=========================================================================
//=========================================================================
//
function table_amap_post_paysan()
{
  $description = array();
  $contenu = array();

  if ($_POST['id_paysan']) { $description[]='id_paysan'; $contenu[]="'".$_POST['id_paysan']."'"; }
  if ($_POST['label_produit']) { $description[]='label_produit'; $contenu[]="'".$_POST['label_produit']."'"; }

  $sqlResult = sql_insert('amap_produit',
                 "(" . join(', ', $description) . ")",
                 "(" . join(', ', $contenu) . ")");

  return "Insertion dans la table amap_produit " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: ".$sqlResult." "));
 
} //function table_amap_post_paysan

?>
