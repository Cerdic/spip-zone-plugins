<?php

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/mots');

function exec_abonnes_tous()
{

global $connect_statut;
global $connect_toutes_rubriques;
global $connect_id_auteur;
global $type,$debut;
global $new,$changer_statut,$statut,$tri,$cherche_auteur,$id_auteur;
 
 $options = 'avancees' ;
 
$nomsite=lire_meta("nom_site"); 
$urlsite=lire_meta("adresse_site"); 

 
// Admin SPIP-Listes
debut_page("Spip listes", "redacteurs", "spiplistes");

// spip-listes bien installé ?
if (!function_exists(spip_listes_onglets)){
    echo("<h3>erreur: spip-listes est mal installé !</h3>");    
    fin_page();
	  exit;
}

if ($connect_statut != "0minirezo" ) {
	echo "<p><b>"._T('spiplistes:acces_a_la_page')."</b></p>";
	fin_page();
	exit;
}

if (($connect_statut == "0minirezo") OR ($connect_id_auteur == $id_auteur)) {
	$statut_auteur=$statut;
	spip_listes_onglets("messagerie", "Spip listes");
}

debut_gauche();

spip_listes_raccourcis();

creer_colonne_droite();

debut_droite("messagerie");

 
//
// Recherche d'auteur
//

if ($cherche_auteur) {
	echo "<p align='left'>";
	$query = "SELECT id_auteur, nom, email FROM spip_auteurs";
	$result = spip_query($query);

    unset($table_auteurs);
	unset($table_ids);
	while ($row = spip_fetch_array($result)) {
	 
         if( email_valide_bloog($cherche_auteur) ){
                $table_auteurs[] = $row["email"] ; }
                else {
                $table_auteurs[] = $row["nom"];
                }
		$table_ids[] = $row["id_auteur"];
	}
	$resultat = mots_ressemblants($cherche_auteur, $table_auteurs, $table_ids);
	debut_boite_info();
	if (!$resultat) {
		echo "<b>"._T('texte_aucun_resultat_auteur', array('cherche_auteur' => $cherche_auteur)).".</b><br />";
	}
	else if (count($resultat) == 1) {

		list(, $nouv_auteur) = each($resultat);
		echo "<b>"._T('spiplistes:une_inscription')."</b><br />";
		$query = "SELECT * FROM spip_auteurs WHERE id_auteur=$nouv_auteur";
		$result = spip_query($query);
		echo "<ul>";
		while ($row = spip_fetch_array($result)) {
			$id_auteur = $row['id_auteur'];
			$nom_auteur = $row['nom'];
			$email_auteur = $row['email'];
			$bio_auteur = $row['bio'];

			echo "<li><font face='Verdana,Arial,Sans,sans-serif' size=2><b><font size=3><a href=\"?exec=abonne_edit&id_auteur=$id_auteur\">".typo($nom_auteur)."</a></font></b>";
			echo " | $email_auteur";
                        echo "</font>\n";
		}
		echo "</ul>";
	}
	else if (count($resultat) < 16) {
		reset($resultat);
		unset($les_auteurs);
		while (list(, $id_auteur) = each($resultat)) $les_auteurs[] = $id_auteur;
		if ($les_auteurs) {
			$les_auteurs = join(',', $les_auteurs);
			echo "<b>"._T('texte_plusieurs_articles', array('cherche_auteur' => $cherche_auteur))."</b><br />";
			$query = "SELECT * FROM spip_auteurs WHERE id_auteur IN ($les_auteurs) ORDER BY nom";
			$result = spip_query($query);
			echo "<ul>";
			while ($row = spip_fetch_array($result)) {
				$id_auteur = $row['id_auteur'];
				$nom_auteur = $row['nom'];
				$email_auteur = $row['email'];
				$bio_auteur = $row['bio'];

				echo "<li><font face='Verdana,Arial,Sans,sans-serif' size=2><b><font size=3>".typo($nom_auteur)."</font></b>";

				if ($email_auteur) echo " ($email_auteur)";
				echo " | <a href=\"".generer_url_ecrire("abonne_edit","id_auteur=$id_auteur")."\">"._T('spiplistes:choisir')."</a>";

				if (trim($bio_auteur)) {
					echo "<br /><font size=1>".couper(propre($bio_auteur), 100)."</font>\n";
				}
				echo "</font><p>\n";
			}
			echo "</ul>";
		}
	}
	else {
		echo "<b>"._T('texte_trop_resultats_auteurs', array('cherche_auteur' => $cherche_auteur))."</b><br />";
	}
	fin_boite_info();
	echo "<p>";

}


global $table_prefix;
$query_message = "SELECT * FROM ".$table_prefix."_articles AS listes LEFT JOIN ".$table_prefix."_auteurs_articles AS abonnements USING (id_article) WHERE statut='liste'";
$result_pile = spip_query($query_message);
$nb_abonnes = spip_num_rows($result_pile);

$query_message = "SELECT * FROM ".$table_prefix."_articles AS listes LEFT JOIN ".$table_prefix."_auteurs_articles AS abonnements USING (id_article) WHERE statut='inact'";
$result_pile = spip_query($query_message);
$nb_abonnes_int = spip_num_rows($result_pile);
		
$query = "SELECT id_auteur, nom, extra FROM spip_auteurs";
$result = spip_query($query);
$nb_inscrits = spip_num_rows($result);

	$cmpt_texte = 0;
	$cmpt_html = 0;
	$cmpt_non = 0;

	while ($row = spip_fetch_array($result)) {
	$abo = get_extra($row["id_auteur"],auteur);

	if ($abo['abo'] == "texte"){
	$cmpt_texte = $cmpt_texte + 1 ;
	}

	if ($abo['abo'] == "html"){
	$cmpt_html = $cmpt_html + 1 ;
	}

	if ($abo['abo'] == "non"){
	$cmpt_non = $cmpt_non + 1 ;
	}

	$total_abo = $cmpt_html + $cmpt_texte ;
	}

$abonnes = spip_query("select a.id_auteur, count(d.id_article) from spip_auteurs a  
                left join spip_auteurs_articles d on a.id_auteur =  
 	                d.id_auteur group by a.id_auteur having count(d.id_article) = 0;"); 
 	                
$nb_abonnes_auc = spip_num_rows($abonnes);

debut_cadre_relief('forum-interne-24.gif');


echo"<div>";
echo"<div style='float:right;width:150px'>";
echo "<b>"._T('spiplistes:repartition')."</b>  <br /><b>"._T('spiplistes:html')."</b> : $cmpt_html <br /><b>"._T('spiplistes:texte')."</b> : $cmpt_texte <br /><b>"._T('spiplistes:desabonnes')."</b> : $cmpt_non";
echo"</div>";
$total= $cmpt_html+$cmpt_texte+$cmpt_non;
echo "Nombre d'abonn&eacute;s : ".$total_abo."<p>Abonn&eacute;s aux listes publiques : ".$nb_abonnes."<br />Abonn&eacute;s aux listes internes : ".$nb_abonnes_int."<br />Abonn&eacute;s &agrave; aucune liste : ".$nb_abonnes_auc."</p>";

echo"</div>";


//echo debut_block_invisible("auteursarticle");

	$query = "SELECT * FROM spip_auteurs WHERE ";
	$query .= "statut!='5poubelle' AND statut!='nouveau' ORDER BY statut, nom";
	$result = spip_query($query);

	if (spip_num_rows($result) > 0) {
		echo "<form action='?exec=abonnes_tous' METHOD='post'>";
        echo "<div align=center>\n";
		echo "<input type='text' name='cherche_auteur' class='fondl' value='' size='20'>";
		echo " <input type='submit' name='Chercher' value='"._T('bouton_chercher')."' class='fondo'>";
		echo "</div></FORM>";
	}
// echo fin_block();



fin_cadre_relief();

echo "<p>";

// auteur

$retour = "?exec=abonnes_tous&";

//changer de statut

if(!$statut) $statut=' ';

if( ($changer_statut=='oui') AND ( ($statut=='html') OR ($statut=='texte') OR ($statut=='non') ) ){
$extras = get_extra($id_auteur,"auteur");
$extras["abo"] = $statut;
set_extra($id_auteur,$extras,"auteur");
}


if (!$tri) $tri='nom';
$retour .= "tri=$tri";
if ($tri=='nom' OR $tri=='statut')
	$partri = " "._T('info_par_tri', array('tri' => $tri));
else if ($tri=='nombre')
	$partri = " "._T('info_par_nombre_article');


//
// Construire la requete
//

// si on n'est pas minirezo, supprimer les auteurs sans article publie
// sauf les admins, toujours visibles.
// limiter les statuts affiches
if ($connect_statut == '0minirezo') {
	
		$sql_visible = "aut.statut IN ('6forum','5poubelle','1comite') OR art.statut IN ('liste', 'inact')";
		$tri = 'nom';
}

$sql_sel = '';

// tri
switch ($tri) {
case 'nombre':
	$sql_order = ' ORDER BY compteur DESC, unom';
	$type_requete = 'nombre';
	break;

case 'statut':
	$sql_order = ' ORDER BY statut, login = "", unom';
	$type_requete = 'auteur';
	break;

case 'nom':
default:
	$type_requete = 'auteur';
	$sql_sel = ", ".creer_objet_multi ("nom", $spip_lang);
	$sql_order = " ORDER BY multi";
}



//
// La requete de base est tres sympa
//

$query = "SELECT
	aut.id_auteur AS id_auteur,
	aut.statut AS statut,
	aut.login AS login,
	aut.nom AS nom,
	aut.email AS email,
	aut.url_site AS url_site,
	aut.messagerie AS messagerie,
	aut.extra AS extra,
	UPPER(aut.nom) AS unom,
	count(lien.id_article) as compteur
	$sql_sel
FROM spip_auteurs as aut
LEFT JOIN spip_auteurs_articles AS lien ON aut.id_auteur=lien.id_auteur
LEFT JOIN spip_articles AS art ON (lien.id_article = art.id_article)
WHERE
	$sql_visible
GROUP BY aut.id_auteur
$sql_order";


$t = spip_query($query);
$nombre_auteurs = spip_num_rows($t);

//
// Lire les auteurs qui nous interessent
// et memoriser la liste des lettres initiales
//

$max_par_page = 30;
if ($debut > $nombre_auteurs - $max_par_page)
	$debut = max(0,$nombre_auteurs - $max_par_page);
$debut = intval($debut);

$i = 0;
$auteurs=array();
while ($auteur = spip_fetch_array($t)) {
	if ($i>=$debut AND $i<$debut+$max_par_page) {
		if ($auteur['statut'] == '0minirezo')
			$auteur['restreint'] = spip_num_rows(
				spip_query("SELECT * FROM spip_auteurs_rubriques
				WHERE id_auteur=".$auteur['id_auteur']));
			$auteurs[] = $auteur;
	}
	$i++;

	if ($tri == 'nom') {
		$lettres_nombre_auteurs ++;
		$premiere_lettre = strtoupper(spip_substr(extraire_multi($auteur['nom']),0,1));
		if ($premiere_lettre != $lettre_prec) {
#			echo " - $auteur[nom] -";
			$lettre[$premiere_lettre] = $lettres_nombre_auteurs-1;
		}
		$lettre_prec = $premiere_lettre;
	}
}



//
// Affichage
//


// reglage du debut
$max_par_page = 30;
if ($debut > $nombre_auteurs - $max_par_page)
	$debut = max(0,$nombre_auteurs - $max_par_page);
$fin = min($nombre_auteurs, $debut + $max_par_page);

// ignorer les $debut premiers
unset ($i);
reset ($auteurs);
while ($i++ < $debut AND each($auteurs));

// ici commence la vraie boucle
debut_cadre_relief('redacteurs-24.gif');
echo "<table border='0' cellpadding=3 cellspacing=0 width='100%' class='arial2'>\n";
echo "<tr bgcolor='#DBE1C5'>";
echo "<td width='20'>";
	$img = "<img src='img_pack/admin-12.gif' alt='' border='0'>";
	if ($tri=='statut')
		echo $img;
	else
		echo "<a href='?exec=abonnes_tous&tri=statut' title='"._T('lien_trier_statut')."'>$img</a>";

echo "</td><td>";
	if ($tri == '' OR $tri=='nom')
		echo '<b>'._T('info_nom').'</b>';
	else
		echo "<a href='?exec=abonnes_tous&tri=nom' title='"._T('lien_trier_nom')."'><b>"._T('info_nom')."</b></a>";

if ($options == 'avancees') echo "</td><td colspan='2'><b>"._T('info_contact')."</b>";
echo "</td><td>";
	if ($visiteurs != 'oui') {
		if ($tri=='nombre')
			echo "<b>"._T('spiplistes:format')."</b>";
		else
			echo "<b>"._T('spiplistes:format')."</b>"; 

	}
echo "</td><td>";
echo "<b>"._T('spiplistes:modifier')."</b>";

echo "</td></tr>\n";

if ($nombre_auteurs > $max_par_page) {
	echo "<tr bgcolor='white'><td colspan='".($options == 'avancees' ? 5 : 3)."'>";
	echo "<font face='Verdana,Arial,Sans,sans-serif' size='2'>";
	for ($j=0; $j < $nombre_auteurs; $j+=$max_par_page) {
		if ($j > 0) echo " | ";

		if ($j == $debut)
			echo "<b>$j</b>";
		else if ($j > 0)
			echo "<a href=$retour&debut=$j>$j</a>";
		else
			echo " <a href=$retour>0</a>";

		if ($debut > $j  AND $debut < $j+$max_par_page){
			echo " | <b>$debut</b>";
		}

	}
	echo "</font>";
	echo "</td></tr>\n";

	if (($tri == 'nom') AND $options == 'avancees') {
		// affichage des lettres
		echo "<tr bgcolor='white'><td colspan='5'>";
		echo "<font face='Verdana,Arial,Sans,sans-serif' size=2>";
		foreach ($lettre as $key => $val) {
			if ($val == $debut)
				echo "<b>$key</b> ";
			else
				echo "<a href=$retour&debut=$val>$key</a> ";
		}
		echo "</font>";
		echo "</td></tr>\n";
	}
	echo "<tr height='5'></tr>";
}


 if($debut)$retour .="&debut=".$debut;

//translate extra field data
list(,,,$trad,$val) = explode("|",_T("spiplistes:options")); 
$trad = explode(",",$trad);
$val = explode(",",$val);
$trad_map = Array();
for($index_map=0;$index_map<count($val);$index_map++) {
	$trad_map[$val[$index_map]] = $trad[$index_map];
}
$i=0;
foreach ($auteurs as $row) {
	// couleur de ligne
	$couleur = ($i % 2) ? '#FFFFFF' : $couleur_claire;
	$i++;
	echo "<tr bgcolor='$couleur'>";

	// statut auteur
	echo "<td>";
	echo bonhomme_statut($row);

	// nom
	echo '</td><td>';
	echo "<a href='?exec=abonne_edit&id_auteur=".$row['id_auteur']."'>".typo($row['nom']).'</a>';

	if ($connect_statut == '0minirezo' AND $row['restreint'])
		echo " &nbsp;<small>"._T('statut_admin_restreint')."</small>";


	// contact
	if ($options == 'avancees') {
		echo '</td><td>';
		if ($row['messagerie'] == 'oui' AND $row['login']
		AND $activer_messagerie != "non" AND $connect_activer_messagerie != "non" AND $messagerie != "non")
			echo bouton_imessage($row['id_auteur'],"force")."&nbsp;";
		if ($connect_statut=="0minirezo")
			if (strlen($row['email'])>3)
				echo "<a href='mailto:".$row['email']."'>"._T('lien_email')."</a>";
			else
				echo "&nbsp;";

		if (strlen($row['url_site'])>3)
			echo "</td><td><a href='".$row['url_site']."'>"._T('lien_site')."</a>";
		else
			echo "</td><td>&nbsp;";
	}

	// Abonné ou pas ?
	echo '</td><td>';
	
	$extra = unserialize ($row["extra"]);
	
        if( !is_array($extra) ){
        $extra = array();
        $extra["abo"] = "non";
        set_extra($row["id_auteur"],$extra,'auteur');
        get_extra($row["id_auteur"],'auteur');
        }
	
        $abo = $extra["abo"];

		if($abo == "non") echo "-";
		else echo "&nbsp;".$trad_map[$abo];
		
		
		// Modifier l'abonnement
	echo '</td><td>';
	
  if ($row["statut"] != '0minirezo') {
if($extra["abo"] == 'html') $option_abo = "<a href='$retour&id_auteur=".$row['id_auteur']."&changer_statut=oui&statut=non'>"._T('spiplistes:desabo')."</a> | <a href='$retour&id_auteur=".$row['id_auteur']."&changer_statut=oui&statut=texte'>"._T('spiplistes:texte')."</a>";
else if($extra["abo"] == 'texte') $option_abo = "<a href='$retour&id_auteur=".$row['id_auteur']."&changer_statut=oui&statut=non'>"._T('spiplistes:desabo')."</a> | <a href='$retour&id_auteur=".$row['id_auteur']."&changer_statut=oui&statut=html'>html</a>";
else  if(($extra["abo"] == 'non')OR (!$extra["abo"])) $option_abo = "<a href='$retour&id_auteur=".$row['id_auteur']."&changer_statut=oui&statut=texte'>"._T('spiplistes:texte')."</a> | <a href='$retour&id_auteur=".$row['id_auteur']."&changer_statut=oui&statut=html'>html</a>";
echo "&nbsp;".$option_abo;
  }
	echo "</td></tr>\n";
}

echo "</table>\n";


echo "<a name='bas'>";
echo "<table width='100%' border='0'>";

$debut_suivant = $debut + $max_par_page;
if ($debut_suivant < $nombre_auteurs OR $debut > 0) {
	echo "<tr height='10'></tr>";
	echo "<tr bgcolor='white'><td align='left'>";
	if ($debut > 0) {
		$debut_prec = strval(max($debut - $max_par_page, 0));
		echo "<form method=\"get\" action=\"".generer_url_ecrire('abonnes_tous','debut=$debut_prec')."\">";
		echo "<input type='submit' name='submit' value='&lt;&lt;&lt;' class='fondo'>";
		echo "</form>";
		//echo "<a href='$retour&debut=$debut_prec'>&lt;&lt;&lt;</a>";
	}
	echo "</td><td align='right'>";
	if ($debut_suivant < $nombre_auteurs) {
		echo '<form method="post" action="'.generer_url_ecrire("abonnes_tous","debut=$debut_suivant").'">';
		echo "<input type='submit' name='submit' value='&gt;&gt;&gt;' class='fondo'>";
		echo "</form>";
		//echo "<a href='$retour&debut=$debut_suivant'>&gt;&gt;&gt;</a>";
	}
	echo "</td></tr>\n";
}

echo "</table>\n";

fin_cadre_relief();


// MODE STATUT FIN -------------------------------------------------------------


$spiplistes_version = "SPIP-listes b1.9";
echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$spiplistes_version."<p>" ;

fin_page();

}

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'abonnés et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/
?>
