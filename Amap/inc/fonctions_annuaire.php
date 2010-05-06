<?php
##########################################################################
#
#           Fichier de fonctions annuaire du plugin Amap pour SPIP
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
function table_amap_lister_annuaire($statut)
{
  $out = '';

  pipeline('exec_init',array('args'=>array('exec'=>'amap_annuaire'),'data'=>''));

  if ( isset($statut) )
  {
    // boucle sur la table amap_personne
    $txtQuery = "SELECT p.id_personne, p.prenom, p.nom, p.fixe, p.portable, p.adhesion";
    $txtQuery .= ", (SELECT count(c.id_contrat) FROM amap_contrat c";
    $txtQuery .= "   WHERE c.id_personne=p.id_personne";
    $txtQuery .= "   GROUP BY c.id_personne";
    $txtQuery .= " ) As NbContrat";
    $txtQuery .= ", (SELECT count(o.id_produit) FROM amap_produit o";
    $txtQuery .= "   WHERE o.id_paysan=p.id_personne";
    $txtQuery .= "   GROUP BY o.id_paysan";
    $txtQuery .= " ) As NbProduit";
    $txtQuery .= " FROM amap_personne p ";
    $sqlResult = sql_query($txtQuery);

    $out .= "\t<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
    $out .= "\t\t<tr bgcolor='#DBE1C5'>\n";
    $out .= "\t\t\t<td><strong>pr&eacute;nom</strong></td>\n";
    $out .= "\t\t\t<td><strong>nom</strong></td>\n";
    $out .= "\t\t\t<td><strong>tel. fixe</strong></td>\n";
    $out .= "\t\t\t<td><strong>tel. portable</strong></td>\n";
    $out .= "\t\t\t<td><strong>adh&eacute;sion</strong></td>\n";
    $out .= "\t\t\t<td>&nbsp;</td>\n";
    $out .= "\t\t</tr>\n";

    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      if (	( ($statut==2) && ($tabUnEnregistrement['NbContrat'] == null) && ($tabUnEnregistrement['NbProduit'] == null) )
	    ||	( ($statut==3) && ($tabUnEnregistrement['NbProduit'] != null) )
	    ||	($statut==1)
         )
      {
      $out .= "\t\t<tr style='background-color: #eeeeee;'>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_annuaire', "action=modif&statut=".$statut."&idPersonne=".$tabUnEnregistrement['id_personne'],"ecrire")." '>\n";
      $out .= "\t\t\t\t\t".$tabUnEnregistrement['prenom']."\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_annuaire', "action=modif&statut=".$statut."&idPersonne=".$tabUnEnregistrement['id_personne'],"ecrire")." '>\n";
      $out .= "\t\t\t\t\t".$tabUnEnregistrement['nom']."\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_annuaire', "action=modif&statut=".$statut."&idPersonne=".$tabUnEnregistrement['id_personne'],"ecrire")." '>\n";
      $out .= "\t\t\t\t\t".$tabUnEnregistrement['fixe']."\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_annuaire', "action=modif&statut=".$statut."&idPersonne=".$tabUnEnregistrement['id_personne'],"ecrire")." '>\n";
      $out .= "\t\t\t\t\t".$tabUnEnregistrement['portable']."\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_annuaire', "action=modif&statut=".$statut."&idPersonne=".$tabUnEnregistrement['id_personne'],"ecrire")." '>\n";
      $out .= "\t\t\t\t\t".$tabUnEnregistrement['adhesion']."\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_annuaire', "action=suppr&statut=".$statut."&idPersonne=".$tabUnEnregistrement['id_personne'],"ecrire")." '>\n";
      $out .= "\t\t\t\t\t<center><img src='"._DIR_PLUGIN_AMAP."img_pack/b_drop.png' /></center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t</tr>\n";
      } // fin if ( ($statut==2) && ($tabUnEnregistrement['NbContrat']==null) )
    } // fin while
    $out .= "\t</table>\n";
  } // fin if ( isset($statut) )
  else
  {
    $out .= "choisir un statut";
  }

  return $out;

} // function table_amap_lister_annuaire


//=========================================================================
//=========================================================================
//
function table_amap_getmodif_annuaire()
{
  $out = '';
  $hiddens = '';

  // boucle sur la table amap_personne
  $txtQuery = "SELECT * FROM amap_personne";
  $txtQuery .= " WHERE id_personne=".$_GET['idPersonne'];
  $sqlResult = sql_query($txtQuery);

  if ($tabUnEnregistrement = sql_fetch($sqlResult))
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>pr&eacute;nom : </td>\n";
    $out .= "\t\t<td><input type='text' size='20' maxlength='20' name='prenom' value='".$tabUnEnregistrement['prenom']."' /></td>\n";
    $out .= "\t</tr>";  
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>nom : </td>\n";
    $out .= "\t\t<td><input type='text' size='30' maxlength='30' name='nom' value='".$tabUnEnregistrement['nom']."' /></td>\n";
    $out .= "\t</tr>";
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>tel. fixe : </td>\n";
    $out .= "\t\t<td><input type='text' size='10' maxlength='10' name='fixe' value='".$tabUnEnregistrement['fixe']."' /></td>\n";
    $out .= "\t</tr>";
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>tel. portable : </td>\n";
    $out .= "\t\t<td><input type='text' size='10' maxlength='10' name='portable' value='".$tabUnEnregistrement['portable']."' /></td>\n";
    $out .= "\t</tr>";
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>adh&eacute;sion (ex 2008) : </td>\n";
    $out .= "\t\t<td><input type='text' size='4' maxlength='4' name='adhesion' value='".$tabUnEnregistrement['adhesion']."' /></td>\n";
    $out .= "\t</tr>";
  } // fin  if ($tabUnEnregistrement = sql_fetch($sqlResult))   

  $hiddens .= "\t<input type='hidden' name='statut' value='".$_GET['statut']."' />\n";
  $hiddens .= "\t<input type='hidden' name='idPersonne' value='".$_GET['idPersonne']."' />\n";

  return generer_url_entite("amap_annuaire"
                           , "&action=maj","post_ecrire")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";

} // function table_amap_getmodif_annuaire

//=========================================================================
//=========================================================================
//
function table_amap_get_annuaire($statut)
{
  $out = '';
  $hiddens = '';

  // 1er boucle sur la table spip_auteurs
  $txtQuery = "SELECT id_auteur, nom, email FROM spip_auteurs ";
  $txtQuery .= "WHERE id_auteur NOT IN ( ";
  $txtQuery .= "SELECT id_personne FROM amap_personne ";
  $txtQuery .= ") ORDER BY id_auteur";
  $sqlResult = sql_query($txtQuery);

  $out .= "\t<tr bgcolor='#DBE1C5'>";
  $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
  $out .= "\t\t\tauteur : \n";
  $out .= "\t\t</td>\n";
  $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
  $out .= "\t\t\t<select name='id_auteur'>\n";
  $out .= "\t\t\t\t<option value=''>-------</option>\n";

  while ($tabUnEnregistrement = sql_fetch($sqlResult))
  {
    $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_auteur']."'>\n";
    $out .= "\t\t\t\t\t".$tabUnEnregistrement['nom']." (".$tabUnEnregistrement['email'].")\n";
    $out .= "\t\t\t\t</option>\n";
  } // fin while
  $out .= "\t\t\t</select>\n";
  $out .= "\t\t</td>\n";
  $out .= "\t</tr>\n";

  // on affiche les élément à compléter dans la table amap_personne
  $out .= "\t<tr bgcolor='#DBE1C5'>";
  $out .= "\t\t<td>pr&eacute;nom : </td>\n";
  $out .= "\t\t<td><input type='text' size='20' maxlength='20' name='prenom'/></td>\n";
  $out .= "\t</tr>";
  $out .= "\t<tr bgcolor='#DBE1C5'>";
  $out .= "\t\t<td>nom : </td>\n";
  $out .= "\t\t<td><input type='text' size='30' maxlength='30' name='nom'/></td>\n";
  $out .= "\t</tr>";
  $out .= "\t<tr bgcolor='#DBE1C5'>";
  $out .= "\t\t<td>tel. fixe : </td>\n";
  $out .= "\t\t<td><input type='text' size='10' maxlength='10' name='fixe'/></td>\n";
  $out .= "\t</tr>";
  $out .= "\t<tr bgcolor='#DBE1C5'>";
  $out .= "\t\t<td>tel. portable : </td>\n";
  $out .= "\t\t<td><input type='text' size='10' maxlength='10' name='portable'/></td>\n";
  $out .= "\t</tr>";
  $out .= "\t<tr bgcolor='#DBE1C5'>";
  $out .= "\t\t<td>adh&eacute;sion (ex 2008) : </td>\n";
  $out .= "\t\t<td><input type='text' size='4' maxlength='4' name='adhesion'/></td>\n";
  $out .= "\t</tr>";

  $hiddens .= "\t<input type='hidden' name='statut' value='".$statut."' />\n";

  return generer_url_entite("amap_annuaire"
                           , "&action=add","post_ecrire")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";

} // function table_amap_get_annuaire

//=========================================================================
//=========================================================================
//
function table_amap_modif_post_annuaire()
{
  $txtQuery = "UPDATE amap_personne SET ";
  $txtQuery .= "prenom='".$_POST['prenom']."', ";
  $txtQuery .= "nom='".$_POST['nom']."', ";
  $txtQuery .= "fixe='".$_POST['fixe']."', ";
  $txtQuery .= "portable='".$_POST['portable']."', ";
  $txtQuery .= "adhesion='".$_POST['adhesion']."' ";
  $txtQuery .= " WHERE id_personne='".$_POST['idPersonne']."' ";

  $sqlResult = sql_query($txtQuery);

  return "Mise à jour dans la table amap_personne " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: ".$_POST['idPersonne']." "));
} // function table_amap_modif_post_annuaire

//=========================================================================
//=========================================================================
//
function table_amap_post_annuaire()
{
  $description = array();
  $contenu = array();

  if ($_POST['id_auteur']) { $description[]='id_personne'; $contenu[]="'".$_POST['id_auteur']."'"; }
  if ($_POST['prenom']) { $description[]='prenom'; $contenu[]="'".$_POST['prenom']."'"; }
  if ($_POST['nom']) { $description[]='nom'; $contenu[]="'".$_POST['nom']."'"; }
  if ($_POST['fixe']) { $description[]='fixe'; $contenu[]="'".$_POST['fixe']."'"; }
  if ($_POST['portable']) { $description[]='portable'; $contenu[]="'".$_POST['portable']."'"; }
  if ($_POST['adhesion']) { $description[]='adhesion'; $contenu[]="'".$_POST['adhesion']."'"; }

  $sqlResult = sql_insert('amap_personne',
                 "(" . join(', ', $description) . ")",
                 "(" . join(', ', $contenu) . ")");

  return "Insertion dans la table amap_personne " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: ".$sqlResult." "));
} // function table_amap_post_annuaire

//=========================================================================
//=========================================================================
//
function table_amap_suppr_annuaire()
{
  $txtQuery = "DELETE FROM amap_personne";
  $txtQuery .= " WHERE id_personne='".$_GET['idPersonne']."' ";

  $sqlResult = sql_query($txtQuery);

  return "Suppression dans la table amap_personne " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: ".$_GET['idPersonne']." "));
} // function table_amap_suppr_annuaire

?>
