<?php

//
// Ce code permet la creation du groupe de mots cle egt_squelette et les 16 mots qui lui sont lies
// Avec l'aimable autorisation de l'auteur :
// Configurateur Squelette Epona - 2006 Fev 28 - Marc Lebas - http://spip-epona.org/
// Adapté pour spip2 par damazone
// squelette egt v0.3
//

include_spip('inc/presentation');
include_spip('inc/bandeau');

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_egt_conf(){

global $param;

if (!file_exists("inc_version.php")) {
  marque_erreur(_T('egt:msg_erreur_inc_version'));
  exit;
}
if (!defined('_ECRIRE_INC_VERSION')) include ("inc_version.php");
include_spip('inc/cookie');
if ($GLOBALS['connect_statut'] != "0minirezo") {
  marque_erreur(_T('egt:msg_erreur_acces_refuse'));
  exit;
}
if (file_exists(_FILE_OPTIONS)) include(_FILE_OPTIONS);


function avertir($msg) {echo "<span style=\"color:orange\">$msg</span><br/>";}

function marque_erreur($msg) {
  global $erreur;
  echo "<strong><span style=\"color:red\">$msg</span></strong><br/>";
  $erreur = 1;
}


function rubrique_vide($id_rubrique) {
	$result = sql_select("id_rubrique", "spip_rubriques", "id_parent=" . $id_rubrique . " LIMIT 0,1");
	list($n) = sql_fetch($result);
	if ($n > 0) return false;
	$result = sql_select("id_article", "spip_articles", "id_rubrique=" . $id_rubrique . " AND (statut='publie' OR statut='prepa' OR statut='prop') LIMIT 0,1");
	list($n) = sql_fetch($result);
	if ($n > 0) return false;
	$result = sql_select("id_breve", "spip_breves", "id_rubrique=" . $id_rubrique . " AND (statut='publie' OR statut='prop') LIMIT 0,1");
	list($n) = sql_fetch($result);
	if ($n > 0) return false;
	$result = sql_select("id_syndic", "spip_syndic", "id_rubrique=" . $id_rubrique . " AND (statut='publie' OR statut='prop') LIMIT 0,1");
	list($n) = sql_fetch($result);
	if ($n > 0) return false;
	$result = sql_select("id_document", "spip_documents", "id_rubrique=" . $id_rubrique . " LIMIT 0,1");
	list($n) = sql_fetch($result);
	if ($n > 0) return false;
	return true;
}



//
// Fonctions pour mot-cles
//
function groupe_vide($titre) {
  if (id_groupe($titre) != 0) {
       marque_erreur(_T('egt:msg_groupe_existe_deja'));
       return FALSE;
  }
  return TRUE;
}

function id_groupe($titre) {
  // leve une erreur si plusieurs
  $result = sql_select("id_groupe", "spip_groupes_mots", "titre='" . $titre . "'");

  switch (sql_count($result)) {
    case 0 : return 0;
    case 1 : while ($row = sql_fetch($result)) return $row['id_groupe'];
    default : 
      marque_erreur(sql_count($result)." groupes $titre : ");
      while ($row = sql_fetch($result)) echo 'id_groupe= '.$row['id_groupe'].'<br/>';
      return -1;
  }
}


function id_mot($titre, $type) {
  // leve une erreur si plusieurs
  $result = sql_select("id_mot, titre", "spip_mots", "titre='" . $titre . "' AND type='" . $type . "'");

  switch (sql_count($result)) {
    case 0 : return 0;
    case 1 : while ($row = sql_fetch($result)) return $row['id_mot'];
    default : 
      marque_erreur(sql_count($result)." mots $titre pour groupe mot $type :");
      while ($row = sql_fetch($result)) echo 'id_mot= '.$row['id_mot'].' '.$row['titre'].'<br/>';
      return -1;
  }
}

function active_groupe($groupe, $mots) {
  $id_groupe=id_groupe($groupe);
  $table= "articles,rubriques,syndic,";
  if ($id_groupe != 0) return FALSE;
  //Creation groupe + mot-cles
  $id_groupe = sql_insertq("spip_groupes_mots", array(
	        'titre' => $groupe,
	        'unseul' => 'non',
	        'obligatoire' => 'non',
	        'tables_liees'=> $table,
	        'minirezo' =>  'oui',
	        'comite' =>  'non',
	        'forum' => 'non'));
	if (!$id_groupe)	{
     marque_erreur(_T('egt:msg_erreur_creation_groupe')." $groupe");
     return FALSE;
  }

  foreach ($mots as $mot) {
    $rc_mot = sql_insertq("spip_mots", array(
			'type' => $groupe,
	        'titre' => $mot,
	        'id_groupe' => $id_groupe));
    if (!$rc_mot)	{
       marque_erreur(_T('egt:msg_erreur_creation_mot')." $mot "._T('egt:dans_groupe')." $groupe");
       return FALSE;
    }
    echo " $mot";
  }
  echo " ("._T('egt:groupe_mots')." $groupe "._T('egt:cree').")<br/>";
  return TRUE;
}

function active_groupe_rub($groupe, $mots) {
  $id_groupe=id_groupe($groupe);
  $table= "articles,rubriques,syndic,";
  if ($id_groupe != 0) return FALSE;
  //Creation groupe + mot-cles
  $id_groupe = sql_insertq("spip_groupes_mots", array(
	        'titre' => $groupe,
	        'unseul' => 'non',
	        'obligatoire' => 'non',
	        'tables_liees'=> $table,
	        'minirezo' =>  'oui',
	        'comite' =>  'non',
	        'forum' => 'non'));
	if (!$id_groupe)	{
     marque_erreur(_T('egt:msg_erreur_creation_groupe')." $groupe");
     return FALSE;
  }

  foreach ($mots as $mot) {
    $id_mot = sql_insertq("spip_mots", array(
			'type' => $groupe,
	        'titre' => $mot,
	        'id_groupe' => $id_groupe));
    if (!$id_mot)	{
       marque_erreur(_T('egt:msg_erreur_creation_mot')." $mot "._T('egt:dans_groupe')." $groupe");
       return FALSE;
    }
    echo " $mot";
  }
  echo " ("._T('egt:groupe_mots')." $groupe "._T('egt:cree').")<br/>";
  return TRUE;
}

function pre_desactive_groupe($titre) {
  // seulement pour verifier que le groupe est libre
  $rcode = TRUE;
  $id_groupe=id_groupe($titre);
  if ($id_groupe == 0) return TRUE;
  if ($id_groupe == -1) return FALSE;
  $result = sql_select("id_mot, titre", "spip_mots", "id_groupe=" . $id_groupe);
  while ($row = sql_fetch($result)) {
    if (!pre_desactive_mot($row['id_mot'], $row['titre'])) $rcode = FALSE;
  }
  return $rcode;
}

function desactive_groupe($titre) {
  // aucun contrôle d'attachement; deja fait par pre_desactive_groupe
  $id_groupe=id_groupe($titre);
  if ($id_groupe == 0) return TRUE;
  if ($id_groupe == -1) return FALSE;
  $result = sql_select("id_mot, titre", "spip_mots", "id_groupe=" . $id_groupe);

  while ($row = sql_fetch($result)) {
	sql_delete("spip_mots","id_mot=" . $row['id_mot']);
    foreach (array('breves', 'articles', 'rubriques', 'forum', 'syndic') as $elem)
	  sql_delete("spip_mots_" . $elem,"id_mot=" . $row['id_mot']);
	sql_delete("spip_index_mots","id_mot=" . $row['id_mot']);
    echo $row['titre'].', ';
  }
  sql_delete("spip_groupes_mots","id_groupe=" . $id_groupe);  
  echo " ("._T('egt:groupe_mots')." $titre "._T('egt:efface').")<br/>";
  return TRUE;
}

function pre_desactive_mot($id_mot, $titre) {
  // pour verifier qu'un mot est libre
  $rcode = TRUE;
  foreach (array('breve', 'article', 'rubrique') as $elem) {
	$result = sql_select("id_".$elem, "spip_mots_".$elem."s", "id_mot=" . $id_mot);
    if ($row = sql_fetch($result)) {
      avertir(_T('egt:le_mot_cle')." $titre "._T('egt:est_attache')." ($elem)");
      $rcode = FALSE;
    }
  }
  foreach (array('forum', 'syndic') as $elem) {
	$result = sql_select("id_".$elem, "spip_mots_".$elem, "id_mot=" . $id_mot);	
    if ($row = sql_fetch($result)) {
      avertir(_T('egt:le_mot_cle')." $titre "._T('egt:est_attache')." ($elem)");
      $rcode = FALSE;
    }
  }
  return $rcode;
}


function affiche_mots($id_groupe) {
//pour affihcer les mots-clé même si aucun élément n'est attaché
	$result = sql_select("id_mot, titre", "spip_mots", "id_groupe=" . $id_groupe);	
	echo _T('Mots-clés')." :<br />";
	while ($row = sql_fetch($result)) {
		echo $row['titre']." "._T('egt:existe_deja').", ";
	}
    return TRUE;
}


/////////////////////////////////////
// Main - Point d'entree du programme   //
////////////////////////////////////

$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('egt'), "configuration", "egt");
echo "<br /><br />";

global $connect_statut, $connect_toutes_rubriques;

echo gros_titre(_T('egt:squelette_egt'),'',false);
echo debut_cadre('', true);
		echo _T('egt:introduction');
echo fin_cadre(true);

echo debut_gauche('', true);
echo debut_droite("",true);

echo debut_cadre_trait_couleur(find_in_path('img_pack/egt.png'), true, "", $titre=_T('egt:configuration_egt'),"","");


//
// Affichage et arret si la base n'est pas coherente
//
foreach (array('egt_squelette') as $titre) id_groupe($titre);
foreach (array('afficher aide site', 'afficher article actu','afficher article dans colonne centrale','afficher infos bandeau','afficher logo breves','afficher logo participer','afficher logo rechercher','afficher nom site','afficher site syndique et articles','afficher texte a retenir','afficher titre a retenir','exclure de dernieres parutions','exclure du menu','ne pas afficher formulaire site','album une','espace membres',) as $titre) id_mot($titre, 'egt_squelette');


//
// Affichage etat final
//

//Affichage de l'état du groupe squelette_egt et actualiser la page

echo debut_cadre_relief(  "", false, "", $titre = _T('egt:etat'));
debut_boite_info(true);

foreach (array('egt_squelette') as $titre) {
  $id_groupe=id_groupe($titre);
  if ($id_groupe == 0) {echo  _T('egt:groupe')." ".$titre." : "._T('egt:absent')."<br/>";} else {
    echo  _T('egt:groupe')." ".$titre." : "._T('egt:present')."<br/>";
    pre_desactive_groupe($titre);
	echo "<br />";
  }
}
$u = generer_url_ecrire("egt_conf","",false);
echo "<div style=\"text-align:right\"><a href=\"$u\"><img src=\"".find_in_path('images/action_reload.png')."\" alt=\""._T('egt:rafraichir')."\" width=\"20px\" height=\"20px\" />"._T('egt:rafraichir')."</a></div>";

// Analyse et traitement requete
//
$param=$_GET['param'];

switch ($param) {
  case 'mots_on' : {  echo "<h1>"._T('egt:creation_mots')."</h1>";
			$id_groupe=id_groupe('egt_squelette');
			if ($id_groupe == 0) {
			active_groupe('egt_squelette', array('afficher aide site', 'afficher article actu','afficher article dans colonne centrale','afficher infos bandeau','afficher logo breves','afficher logo participer','afficher logo rechercher','afficher nom site','afficher site syndique et articles','afficher texte a retenir','afficher titre a retenir','exclure de dernieres parutions','exclure du menu','ne pas afficher formulaire site','album une','espace membres',));
			} else {
				avertir(_T('egt:msg_groupe_existe_deja'));
				affiche_mots($id_groupe);
				}
		break ;
	}
  case 'mots_off' : {  echo "<h1>"._T('egt:suppression_mots')."</h1>";
			foreach (array('egt_squelette') as $titre) {
			if (!pre_desactive_groupe($titre)) {
			avertir("Groupe $titre encore attach&eacute;");
			} else desactive_groupe($titre);
			}
  		break;
	}
}


if (isset($erreur)) marque_erreur("<h1>Echec</h1>");

echo fin_boite_info(true);

//
// Affichage menu
//

//Création automatique de mots-clé
echo debut_cadre_relief(  "", false, "", $titre = _T('egt:creation_auto_mots'));
debut_boite_info(true);
$u = generer_url_ecrire("egt_conf","param=mots_on",false);
echo "<a href=\"$u\">"._T('egt:creer_mots')."<br /></a>";
echo fin_boite_info(true);


//Remerciements
echo debut_cadre_relief(  "", false, "", $titre = _T('Remerciements'));
debut_boite_info(true);
echo "Merci &agrave; Marc pour ce script (squelette <a href=\"http://spip-epona.org/\">Epona</a>) 
ainsi que <a href=\"http://www.plugandspip.com\"> Les plugins Spip</a> pour leurs tutoriels";
echo fin_boite_info(true);

echo fin_cadre_trait_couleur(true);
echo fin_gauche(), fin_page();
                        exit;
                }                
?>
