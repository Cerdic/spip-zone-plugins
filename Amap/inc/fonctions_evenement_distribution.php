<?php
##########################################################################
#
#           Fichier des fonctions evenements du plugin Amap pour SPIP
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
function table_amap_lister_evenement_distribution($idSaison)
{
  $out = '';
  $hiddens = '';

  pipeline('exec_init',array('args'=>array('exec'=>'amap_distributions'),'data'=>''));

  if ($idSaison!=0)
  { // on affiche l'ensemble des distributions de la saison
    // 1er boucle sur la table spip_amap_evenements
    $txtQuery = "SELECT DATE_FORMAT(date_evenement, '%d-%m-%Y') As dateEvenement, id_evenement, id_lieu, id_personne1, id_personne2, id_personne3";
    $txtQuery .= " FROM spip_amap_evenements";
    $txtQuery .= " WHERE id_saison=".$idSaison;
    $txtQuery .= " ORDER BY date_evenement";
    $sqlResult_1 = sql_query($txtQuery);

    $out .= "\t<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
    $out .= "\t\t<tr bgcolor='#DBE1C5'>\n";
    $out .= "\t\t\t<td><strong><center>date distribution</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>nom lieu</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>resp. 1</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>resp. 2</center></strong></td>\n";
    $out .= "\t\t\t<td><strong><center>resp. 3</center></strong></td>\n";
    $out .= "\t\t\t<td>&nbsp;</td>\n";
    $out .= "\t\t</tr>\n";

    while ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))
    {
      $out .= "\t\t<tr style='background-color: #eeeeee;'>\n";
      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_distributions', "action=modif&idEvenement=".$tabUnEnregistrement_1['id_evenement'],"ecrire")."&idSaison=".$idSaison." '>\n";
      $out .= "\t\t\t\t\t<center>".$tabUnEnregistrement_1['dateEvenement']."</center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_distributions', "action=modif&idEvenement=".$tabUnEnregistrement_1['id_evenement'],"ecrire")."&idSaison=".$idSaison." '>\n";
      $txtQuery = "SELECT nom_lieu";
      $txtQuery .= " FROM spip_amap_lieu";
      $txtQuery .= " WHERE id_lieu=".$tabUnEnregistrement_1['id_lieu'];
      $sqlResult_2 = sql_query($txtQuery);
      if ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
         $out .= "\t\t\t\t\t".$tabUnEnregistrement_2['nom_lieu']."\n";
      else
         $out .= "\t\t\t\t\t".$tabUnEnregistrement_1['id_lieu']."\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

     $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_distributions', "action=modif&idEvenement=".$tabUnEnregistrement_1['id_evenement'],"ecrire")."&idSaison=".$idSaison." '>\n";

      // on recherche le nom de la première personne dans la table spip_amap_personne...
      $txtQuery = "SELECT prenom, nom FROM spip_amap_personne ";
      $txtQuery .= "WHERE id_personne=".$tabUnEnregistrement_1['id_personne1'];
      $sqlResult_2 = sql_query($txtQuery);

      if ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
        $out .= "\t\t\t\t\t".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."\n";
      else
        $out .= "\t\t\t\t\t".$tabUnEnregistrement_1['id_personne1']."\n";

      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";
      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_distributions', "action=modif&idEvenement=".$tabUnEnregistrement_1['id_evenement'],"ecrire")."&idSaison=".$idSaison." '>\n";

      // on recherche le nom de la deuxième personne dans la table spip_amap_personne...
      $txtQuery = "SELECT prenom, nom FROM spip_amap_personne ";
      $txtQuery .= "WHERE id_personne=".$tabUnEnregistrement_1['id_personne2'];
      $sqlResult_2 = sql_query($txtQuery);

      if ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
        $out .= "\t\t\t\t\t".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."\n";
      else
        $out .= "\t\t\t\t\t".$tabUnEnregistrement_1['id_personne2']."\n";

      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";
      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_distributions', "action=modif&idEvenement=".$tabUnEnregistrement_1['id_evenement'],"ecrire")."&idSaison=".$idSaison." '>\n";

      // on recherche le nom de la troisième personne dans la table spip_amap_personne...
      $txtQuery = "SELECT prenom, nom FROM spip_amap_personne ";
      $txtQuery .= "WHERE id_personne=".$tabUnEnregistrement_1['id_personne3'];
      $sqlResult_2 = sql_query($txtQuery);

      if ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
        $out .= "\t\t\t\t\t".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."\n";
      else
        $out .= "\t\t\t\t\t".$tabUnEnregistrement_1['id_personne3']."\n";

      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\t\t<a href='".generer_url_entite('amap_distributions', "action=suppr&idEvenement=".$tabUnEnregistrement_1['id_evenement'],"ecrire")."&idSaison=".$idSaison." '>\n";
      $out .= "\t\t\t\t\t<center><img src='"._DIR_PLUGIN_AMAP."img_pack/b_drop.png' /></center>\n";
      $out .= "\t\t\t\t</a>\n";
      $out .= "\t\t\t</td>\n";

      $out .= "\t\t</tr>\n";
    } // fin while
    $out .= "\t</table>\n";
  } // fin   if ($idSaison!=0)
  else
  {
    return "choisir une saison";
  }

  return $out;

} // function table_amap_lister_evenement_distribution


//=========================================================================
//=========================================================================
//
function table_amap_getmodif_evenement_distribution($idSaison, $idEvenement)
{
  $out = '';
  $hiddens = '';

  if ( isset($idSaison) && isset($idEvenement) )
  {
    // 1er boucle sur la table spip_amap_evenements
    $txtQuery = "SELECT DATE_FORMAT(date_evenement, '%d-%m-%Y') As dateEvenement, id_lieu, id_personne1, id_personne2, id_personne3";
    $txtQuery .= " FROM spip_amap_evenements";
    $txtQuery .= " WHERE id_saison=".$idSaison;
    $txtQuery .= " AND id_evenement=".$idEvenement;
    $sqlResult_1 = sql_query($txtQuery);

    if ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))
    {
      $out .= "\t<tr bgcolor='#DBE1C5'>";
      $out .= "\t\t<td>date : </td>\n";
      $out .= "\t\t<td>\n";
      $out .= "\t\t".$tabUnEnregistrement_1['dateEvenement']."\n";
      $out .= "\t\t</td>\n";
      $out .= "\t</tr>";

      $out .= "\t<tr bgcolor='#DBE1C5'>";
      $out .= "\t\t<td>lieu : </td>\n";
      $out .= "\t\t<td>\n";
      $out .= "\t\t\t<select name='idLieu' >\n";

      // on fait une boucle de recherche des lieux possibles
      $txtQuery = "SELECT id_lieu, nom_lieu FROM spip_amap_lieu";
      $sqlResult_2 = sql_query($txtQuery);

      while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
      {
        if ($tabUnEnregistrement_2['id_lieu'] == $tabUnEnregistrement_1['id_lieu'])
          $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement_2['id_lieu']."'>".$tabUnEnregistrement_2['nom_lieu']."</option>\n";
        else
          $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_lieu']."'>".$tabUnEnregistrement_2['nom_lieu']."</option>\n";
      } 
      $out .= "\t\t\t</select>\n";
      $out .= "\t\t</td>\n";
      $out .= "\t</tr>";

      $out .= "\t<tr bgcolor='#DBE1C5'>";
      $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\tnom responsable 1: \n";
      $out .= "\t\t</td>\n";
      $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
 
      // boucle sur la table spip_amap_personne
      $txtQuery = "SELECT id_personne, prenom, nom FROM spip_amap_personne";
      $sqlResult_2 = sql_query($txtQuery);

      $out .= "\t\t\t<select name='idPersonne1'>\n";
      $out .= "\t\t\t\t<option value=''>----</option>\n";
      while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
      {
        if ($tabUnEnregistrement_2['id_personne'] == $tabUnEnregistrement_1['id_personne1'])
          $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
        else
          $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
      } // fin while
      $out .= "\t\t\t</select>\n";
      $out .= "\t\t</td>\n";
      $out .= "\t</tr>\n";

      $out .= "\t<tr bgcolor='#DBE1C5'>";
      $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\tnom responsable 2: \n";
      $out .= "\t\t</td>\n";
      $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
 
      // boucle sur la table spip_amap_personne
      $txtQuery = "SELECT id_personne, prenom, nom FROM spip_amap_personne";
      $sqlResult_2 = sql_query($txtQuery);

      $out .= "\t\t\t<select name='idPersonne2'>\n";
      $out .= "\t\t\t\t<option value=''>----</option>\n";
      while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
      {
        if ($tabUnEnregistrement_2['id_personne'] == $tabUnEnregistrement_1['id_personne2'])
          $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
        else
          $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
      } // fin while
      $out .= "\t\t\t</select>\n";
      $out .= "\t\t</td>\n";
      $out .= "\t</tr>\n";

      $out .= "\t<tr bgcolor='#DBE1C5'>";
      $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\tnom responsable 3: \n";
      $out .= "\t\t</td>\n";
      $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
 
     // boucle sur la table spip_amap_personne
     $txtQuery = "SELECT id_personne, prenom, nom FROM spip_amap_personne";
     $sqlResult_2 = sql_query($txtQuery);

      $out .= "\t\t\t<select name='idPersonne3'>\n";
      $out .= "\t\t\t\t<option value=''>----</option>\n";
      while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
      {
        if ($tabUnEnregistrement_2['id_personne'] == $tabUnEnregistrement_1['id_personne3'])
          $out .= "\t\t\t\t<option selected value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
        else
          $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
      } // fin while
      $out .= "\t\t\t</select>\n";
      $out .= "\t\t</td>\n";
      $out .= "\t</tr>\n";

    } // if ($tabUnEnregistrement_1 = sql_fetch($sqlResult))

    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$idSaison."' />\n";
    $hiddens .= "\t<input type='hidden' name='idEvenement' value='".$idEvenement."' />\n";

	return generer_url_ecrire("amap_distributions", "table=spip_amap_evenements", "action=modif")
                            ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";
  }

} // function table_amap_getmodif_evenement_distribution()


//=========================================================================
//=========================================================================
//
function table_amap_getAgendaUpdate_evenement_distribution($idSaison)
{
  $out = '';
  $hiddens = '';

  if ($idSaison!=0)
  {
    $out .= "\t<tr bgcolor='#DBE1C5'>";
    $out .= "\t\t<td>lieu : </td>\n";
    $out .= "\t\t<td>\n";
    $out .= "\t\t\t<select name='idLieu' >\n";
    $out .= "\t\t\t\t<option value=''>------</option>\n";

    // on fait une boucle de recherche des lieux possibles
    $txtQuery = "SELECT id_lieu, nom_lieu FROM spip_amap_lieu";
    $sqlResult = sql_query($txtQuery);

    while ($tabUnEnregistrement = sql_fetch($sqlResult))
    {
      $out .= "\t\t\t\t<option value='".$tabUnEnregistrement['id_lieu']."'>".$tabUnEnregistrement['nom_lieu']."</option>\n";
    } 
    $out .= "\t\t\t</select>\n";
    $out .= "\t\t</td>\n";
    $out .= "\t</tr>";

    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$idSaison."' />\n";

	return generer_url_ecrire("amap_distributions", "table=spip_amap_evenements", "action=agenda_update")
                              ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";
  } // fin if (isset($idSaison))
  else
  {
    return "choisir une saison";
  }
} // function table_amap_getAgendaUpdate_evenement_distribution()


//=========================================================================
//=========================================================================
//
function table_amap_get_evenement_distribution($idSaison)
{
  $out = '';
  $hiddens = '';

  if (isset($idSaison))
  {
      $out .= "\t<tr bgcolor='#DBE1C5'>";
      $out .= "\t\t<td>date (JJ-MM-AAAA): </td>\n";
      $out .= "\t\t<td>\n";
      $out .= "\t\t\t<input name='dateEvenement' size='10' maxlength='10' /> \n";
      $out .= "\t\t</td>\n";
      $out .= "\t</tr>";

      $out .= "\t<tr bgcolor='#DBE1C5'>";
      $out .= "\t\t<td>lieu : </td>\n";
      $out .= "\t\t<td>\n";
      $out .= "\t\t\t<select name='idLieu' >\n";

      // on fait une boucle de recherche des lieux possibles
      $txtQuery = "SELECT id_lieu, nom_lieu FROM spip_amap_lieu";
      $sqlResult_2 = sql_query($txtQuery);

      while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
      {
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_lieu']."'>".$tabUnEnregistrement_2['nom_lieu']."</option>\n";
      } 
      $out .= "\t\t\t</select>\n";
      $out .= "\t\t</td>\n";
      $out .= "\t</tr>";

      $out .= "\t<tr bgcolor='#DBE1C5'>";
      $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\tnom responsable 1: \n";
      $out .= "\t\t</td>\n";
      $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
 
      // boucle sur la table spip_amap_personne
      $txtQuery = "SELECT id_personne, prenom, nom FROM spip_amap_personne";
      $sqlResult_2 = sql_query($txtQuery);

      $out .= "\t\t\t<select name='idPersonne1'>\n";
      $out .= "\t\t\t\t<option value=''>----</option>\n";
      while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
      {
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
      } // fin while
      $out .= "\t\t\t</select>\n";
      $out .= "\t\t</td>\n";
      $out .= "\t</tr>\n";

      $out .= "\t<tr bgcolor='#DBE1C5'>";
      $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\tnom responsable 2: \n";
      $out .= "\t\t</td>\n";
      $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
 
      // boucle sur la table spip_amap_personne
      $txtQuery = "SELECT id_personne, prenom, nom FROM spip_amap_personne";
      $sqlResult_2 = sql_query($txtQuery);

      $out .= "\t\t\t<select name='idPersonne2'>\n";
      $out .= "\t\t\t\t<option value=''>----</option>\n";
      while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
      {
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
      } // fin while
      $out .= "\t\t\t</select>\n";
      $out .= "\t\t</td>\n";
      $out .= "\t</tr>\n";

      $out .= "\t<tr bgcolor='#DBE1C5'>";
      $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
      $out .= "\t\t\tnom responsable 3: \n";
      $out .= "\t\t</td>\n";
      $out .= "\t\t<td class='arial1' style='border-top: 1px solid #cccccc;'>\n";
 
     // boucle sur la table spip_amap_personne
     $txtQuery = "SELECT id_personne, prenom, nom FROM spip_amap_personne";
     $sqlResult_2 = sql_query($txtQuery);

      $out .= "\t\t\t<select name='idPersonne3'>\n";
      $out .= "\t\t\t\t<option value=''>----</option>\n";
      while ($tabUnEnregistrement_2 = sql_fetch($sqlResult_2))
      {
        $out .= "\t\t\t\t<option value='".$tabUnEnregistrement_2['id_personne']."'>".$tabUnEnregistrement_2['prenom']." ".$tabUnEnregistrement_2['nom']."</option>\n";
      } // fin while
      $out .= "\t\t\t</select>\n";
      $out .= "\t\t</td>\n";
      $out .= "\t</tr>\n";

    $hiddens .= "\t<input type='hidden' name='idSaison' value='".$idSaison."' />\n";

	return generer_url_ecrire("amap_distributions", "action=add")
                             ."<table>\n".$out
                             ."</table>$hiddens<input type='submit'/></form>";
  } // fin if (isset($idSaison))
  else
  {
    return "choisir une saison";
  }
} // function table_amap_get_evenement_distribution()


//=========================================================================
//=========================================================================
//
function table_amap_modif_post_evenement_distribution()
{
  if (  !(    ( ($_POST['idPersonne1'] == $_POST['idPersonne2'])  && ($_POST['idPersonne1'] != '') )
           || ( ($_POST['idPersonne1'] == $_POST['idPersonne3'])  && ($_POST['idPersonne1'] != '') )
           || ( ($_POST['idPersonne2'] == $_POST['idPersonne3'])  && ($_POST['idPersonne2'] != '') )
         ) 
     )
  {
    $txtQuery = "UPDATE spip_amap_evenements SET ";
    $txtQuery .= "id_lieu='".$_POST['idLieu']."', ";
    if ($_POST['idPersonne1'] == '')
      $txtQuery .= "id_personne1=null, ";
    else
      $txtQuery .= "id_personne1='".$_POST['idPersonne1']."', ";
    if ($_POST['idPersonne2'] == '')
      $txtQuery .= "id_personne2=null, ";
    else
      $txtQuery .= "id_personne2='".$_POST['idPersonne2']."', ";
    if ($_POST['idPersonne3'] == '')
      $txtQuery .= "id_personne3=null ";
    else
      $txtQuery .= "id_personne3='".$_POST['idPersonne3']."' ";
    $txtQuery .= "WHERE id_evenement=".$_POST['idEvenement'];

    $sqlResult = sql_query($txtQuery);

    return "Mise à jour dans la table spip_amap_evenements " .
      (!$sqlResult ? ': erreur !!' : ("sous le numero: ".$_POST['idEvenement']." "));
  }
  else
    return "Mise à jour dans la table spip_amap_evenements : Erreur!!<br/><b>Vous avez deux fois la m&ecirc;me personne comme responsables de la distribution</b>";
} //function table_amap_modif_post_evenement_distribution


//=========================================================================
//=========================================================================
//
function table_amap_add_post_evenement_distribution()
{
  if (    ( ($_POST['idPersonne1'] == $_POST['idPersonne2'])  && ($_POST['idPersonne1'] != '') )
       || ( ($_POST['idPersonne1'] == $_POST['idPersonne3'])  && ($_POST['idPersonne1'] != '') )
       || ( ($_POST['idPersonne2'] == $_POST['idPersonne3'])  && ($_POST['idPersonne2'] != '') )
     ) 
  {
    return "Insertion dans la table spip_amap_evenements : Erreur!!<br/><b>Vous avez deux fois la m&ecirc;me personne comme responsables de la distribution</b>";
  }
  else
  {
    $description = array();
    $contenu = array();

    if ($_POST['dateEvenement']) { $description[]='date_evenement'; $contenu[]="'".$_POST['dateEvenement']."'"; }
    if ($_POST['idSaison']) { $description[]='id_saison'; $contenu[]="'".$_POST['idSaison']."'"; }
    if ($_POST['idLieu']) { $description[]='id_lieu'; $contenu[]="'".$_POST['idLieu']."'"; }
    if ($_POST['idPersonne1']) { $description[]='id_personne1'; $contenu[]="'".$_POST['idPersonne1']."'"; }
    if ($_POST['idPersonne2']) { $description[]='id_personne2'; $contenu[]="'".$_POST['idPersonne2']."'"; }
    if ($_POST['idPersonne3']) { $description[]='id_personne3'; $contenu[]="'".$_POST['idPersonne3']."'"; }

    $sqlResult = sql_insert('amap_evenements',
                 "(" . join(', ', $description) . ")",
                 "(" . join(', ', $contenu) . ")");

    return "Insertion dans la table spip_amap_evenements " .
      (!$sqlResult ? ': erreur !!' : ("sous le numero: ".$sqlResult." "));
  }

} //function table_amap_add_post_evenement_distribution


//=========================================================================
//=========================================================================
//
function table_amap_agenda_update_evenement_distribution()
{
  $txtQuery = "SELECT id_evenement, date_intPremierEnreg FROM spip_amap_saison s, spip_evenements e";
  $txtQuery .= " WHERE s.id_agenda=e.id_article";
  $txtQuery .= " AND s.id_saison=".$_POST['idSaison'];
  $txtQuery .= " ORDER BY date_intPremierEnreg";
  $sqlResult_1 = sql_query($txtQuery);
  $sqlResult_3 = 0;
  $res = array();

  while ($tabUnEnregistrement_1 = sql_fetch($sqlResult_1))
  {
    $txtQuery = "SELECT count(id_evenement) As nbEvenement FROM spip_amap_evenements";
    $txtQuery .= " WHERE DATE_FORMAT(date_evenement, '%Y-%m-%d')=DATE_FORMAT('".$tabUnEnregistrement_1['date_intPremierEnreg']."', '%Y-%m-%d')";
    $txtQuery .= " GROUP BY id_evenement";
    $sqlResult_2 = sql_query($txtQuery);
    $tabUnEnregistrement_2 = sql_fetch($sqlResult_2);
//echo "<p>".$txtQuery."</p>";
    if ($tabUnEnregistrement_2['nbEvenement'] > 0)
    {
      $txtQuery = "UPDATE spip_amap_evenements SET ";
      if ($_POST['idLieu'])
        $txtQuery .= "id_lieu='".$_POST['idLieu']."', ";
      $txtQuery .= "id_evenement='".$tabUnEnregistrement_1['id_evenement']."', ";
      $txtQuery .= "date_evenement='".$tabUnEnregistrement_1['date_intPremierEnreg']."' ";
      //$txtQuery .= " WHERE date_evenement=DATE_FORMAT('".$tabUnEnregistrement_1['date_intPremierEnreg']."', '%Y-%m-%d')";
      $txtQuery .= " WHERE DATE_FORMAT(date_evenement, '%Y-%m-%d')=DATE_FORMAT('".$tabUnEnregistrement_1['date_intPremierEnreg']."', '%Y-%m-%d')";
      $sqlResult_3 = sql_query($txtQuery);
//echo "<p>".$txtQuery."</p>";
    }
    else
    {
      $description = array();
      $contenu = array();

      if ($tabUnEnregistrement_1['id_evenement']) { $description[]='id_evenement'; $contenu[]="'".$tabUnEnregistrement_1['id_evenement']."'"; }
      if ($tabUnEnregistrement_1['date_intPremierEnreg']) { $description[]='date_evenement'; $contenu[]="'".$tabUnEnregistrement_1['date_intPremierEnreg']."'"; }
      if ($_POST['idSaison']) { $description[]='id_saison'; $contenu[]="'".$_POST['idSaison']."'"; }
      if ($_POST['idLieu']) { $description[]='id_lieu'; $contenu[]="'".$_POST['idLieu']."'"; }

      $sqlResult_3 = sql_insert('spip_amap_evenements',
                   "(" . join(', ', $description) . ")",
                   "(" . join(', ', $contenu) . ")");
    }
    if ($sqlResult_3) { $res[]=$tabUnEnregistrement_1['id_evenement']; }
  }

  return "Mise à jour dans la table spip_amap_evenements " .
    (!$sqlResult_3 ? ': erreur !!' : ("sous les numeros: (".join(', ', $res).") "));
} //function table_amap_agenda_update_evenement_distribution

//=========================================================================
//=========================================================================
//
function table_amap_suppr_evenement_distribution()
{
  $txtQuery = "DELETE FROM spip_amap_evenements";
  $txtQuery .= " WHERE id_evenement=".$_GET['idEvenement'];

  $sqlResult = sql_query($txtQuery);

  return "Suppression dans la table spip_amap_evenements " .
    (!$sqlResult ? ': erreur !!' : ("sous le numero: ".$_GET['idEvenement']." "));
} //function table_amap_suppr_evenement_distribution

?>
