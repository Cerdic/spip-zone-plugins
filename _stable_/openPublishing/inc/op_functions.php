<?php

/* Ce fichier contient toutes les fonctions utilisé par la balise #FORMULAIRE_ARTICLE
 */



// fonction qui affiche la zone de texte et la barre de typographie

function barre_article($texte, $rows, $cols, $lang='')
{
	static $num_formulaire = 0;
	include_ecrire('inc/layer');

	$texte = entites_html($texte);
	if (!$GLOBALS['browser_barre'])
		return "<textarea name='texte' rows='$rows' class='forml' cols='$cols'>$texte</textarea>";
	
	$num_formulaire++;
	
	return afficher_barre("document.getElementById('formulaire_$num_formulaire')", false) .
	  "<textarea name='texte' rows='$rows' class='forml' cols='$cols'
	id='formulaire_$num_formulaire'
	onselect='storeCaret(this);'
	onclick='storeCaret(this);'
	onkeyup='storeCaret(this);'
	ondbclick='storeCaret(this);'>$texte</textarea>" .
	$GLOBALS['options'];
}

// pour garder la valeur lors d'un rechargement de page

function selected_option($id_rubrique, $rubrique_boucle,$titre_rubrique)
{
	$selected = '';
	if ($id_rubrique == $rubrique_boucle) $selected = "SELECTED";
	return "[<option value='$rubrique_boucle' $selected >&nbsp;$titre_rubrique</option>]";
}

// pour garder la valeur lors d'un rechargement de page

function checkbox_agenda($choix_agenda) {

	$checked = '';
	if ($choix_agenda == "OK") $checked = "CHECKED";
	return "<input type='checkbox' name='choix_agenda' value='OK' '$checked' />&nbsp;"._T('opconfig:publier_agenda')."<br />";
}

// pour garder la valeur lors d'un rechargement de page

function select_annee($annee) {
	$selected = "";
	if ($annee !== '') $selected = "SELECTED";
	$return = "<select name='annee'>"; // pour le moment ne prend en compte que 5 ans
	for ($i = 0;$i<5;$i++) {
		$a = 2007 + $i;
		if ($a == $annee) {
			$return = $return . "<option value='$a' '$selected' >$a</option>";
		}
		else $return = $return . "<option value='$a'>$a</option>";
	}
	return $return ."</select>";
}

// pour garder la valeur lors d'un rechargement de page

function select_mois($mois) {
	$selected = "";
	if ($mois !== '') $selected = "SELECTED";
	$return = "<select name='mois'>"; 

	if ($mois == "01") $return = $return . "<option value='01' '$selected' >janvier</option>";
	else $return = $return . "<option value='01'>janvier</option>";
	if ($mois == "02") $return = $return . "<option value='02' '$selected' >fevrier</option>";
	else $return = $return . "<option value='02'>fevrier</option>";
	if ($mois == "03") $return = $return . "<option value='03' '$selected' >mars</option>";
	else $return = $return . "<option value='03'>mars</option>";
	if ($mois == "04") $return = $return . "<option value='04' '$selected' >avril</option>";
	else $return = $return . "<option value='04'>avril</option>";
	if ($mois == "05") $return = $return . "<option value='05' '$selected' >mai</option>";
	else $return = $return . "<option value='05'>mai</option>";
	if ($mois == "06") $return = $return . "<option value='06' '$selected' >juin</option>";
	else $return = $return . "<option value='06'>juin</option>";
	if ($mois == "07") $return = $return . "<option value='07' '$selected' >juillet</option>";
	else $return = $return . "<option value='07'>juillet</option>";
	if ($mois == "08") $return = $return . "<option value='08' '$selected' >aout</option>";
	else $return = $return . "<option value='08'>aout</option>";
	if ($mois == "09") $return = $return . "<option value='09' '$selected' >septembre</option>";
	else $return = $return . "<option value='09'>septembre</option>";
	if ($mois == "10") $return = $return . "<option value='10' '$selected' >otobre</option>";
	else $return = $return . "<option value='10'>octobre</option>";
	if ($mois == "11") $return = $return . "<option value='11' '$selected' >novembre</option>";
	else $return = $return . "<option value='11'>novembre</option>";
	if ($mois == "12") $return = $return . "<option value='12' '$selected' >decembre</option>";
	else $return = $return . "<option value='12'>decembre</option>";

	return $return ."</select>";
}

// pour garder la valeur lors d'un rechargement de page

function select_jour($jour) {

	$selected = "";
	if ($jour !== '') $selected = "SELECTED";
	$return = "<select name='jour'>"; // pour le moment tous les mois ont 31 jours
	for ($i = 0;$i<31;$i++) {
		$j = 1 + $i;
		if ($j == $jour) {
			$return = $return . "<option value='$j' '$selected' >$j</option>";
		}
		else $return = $return . "<option value='$j'>$j</option>";
	}
	return $return . '</select>';
}

// pour garder la valeur lors d'un rechargement de page

function select_heure($heure) {

	$selected = "";
	if ($heure !== '') $selected = "SELECTED";
	$return = "<select name='heure'>"; // pour le moment tous les mois ont 31 jours

	if ($heure == "06:00") $return = $return . "<option value='06:00' '$selected' >06:00</option>";
	else $return = $return . "<option value='06:00' >06:00</option>";
	if ($heure == "07:00") $return = $return . "<option value='07:00' '$selected' >07:00</option>";
	else $return = $return . "<option value='07:00' >07:00</option>";
	if ($heure == "08:00") $return = $return . "<option value='08:00' '$selected' >08:00</option>";
	else $return = $return . "<option value='08:00' >08:00</option>";
	if ($heure == "09:00") $return = $return . "<option value='09:00' '$selected' >09:00</option>";
	else $return = $return . "<option value='09:00' >09:00</option>";
	if ($heure == "10:00") $return = $return . "<option value='10:00' '$selected' >10:00</option>";
	else $return = $return . "<option value='10:00' >10:00</option>";
	if ($heure == "11:00") $return = $return . "<option value='11:00' '$selected' >11:00</option>";
	else $return = $return . "<option value='11:00' >11:00</option>";
	if ($heure == "12:00") $return = $return . "<option value='12:00' '$selected' >12:00</option>";
	else $return = $return . "<option value='12:00' >12:00</option>";
	if ($heure == "13:00") $return = $return . "<option value='13:00' '$selected' >13:00</option>";
	else $return = $return . "<option value='13:00' >13:00</option>";
	if ($heure == "14:00") $return = $return . "<option value='14:00' '$selected' >14:00</option>";
	else $return = $return . "<option value='14:00' >14:00</option>";
	if ($heure == "15:00") $return = $return . "<option value='15:00' '$selected' >15:00</option>";
	else $return = $return . "<option value='15:00' >15:00</option>";
	if ($heure == "16:00") $return = $return . "<option value='16:00' '$selected' >16:00</option>";
	else $return = $return . "<option value='16:00' >16:00</option>";
	if ($heure == "17:00") $return = $return . "<option value='17:00' '$selected' >17:00</option>";
	else $return = $return . "<option value='17:00' >17:00</option>";
	if ($heure == "18:00") $return = $return . "<option value='18:00' '$selected' >18:00</option>";
	else $return = $return . "<option value='18:00' >18:00</option>";
	if ($heure == "19:00") $return = $return . "<option value='19:00' '$selected' >19:00</option>";
	else $return = $return . "<option value='19:00' >19:00</option>";
	if ($heure == "20:00") $return = $return . "<option value='20:00' '$selected' >20:00</option>";
	else $return = $return . "<option value='20:00' >20:00</option>";
	if ($heure == "21:00") $return = $return . "<option value='21:00' '$selected' >21:00</option>";
	else $return = $return . "<option value='21:00' >21:00</option>";
	if ($heure == "22:00") $return = $return . "<option value='22:00' '$selected' >22:00</option>";
	else $return = $return . "<option value='22:00' >22:00</option>";
	if ($heure == "23:00") $return = $return . "<option value='23:00' '$selected' >23:00</option>";
	else $return = $return . "<option value='23:00' >23:00</option>";
	if ($heure == "00:00") $return = $return . "<option value='00:00' '$selected' >00:00</option>";
	else $return = $return . "<option value='00:00' >00:00</option>";
	if ($heure == "01:00") $return = $return . "<option value='01:00' '$selected' >01:00</option>";
	else $return = $return . "<option value='01:00' >01:00</option>";
	if ($heure == "02:00") $return = $return . "<option value='02:00' '$selected' >02:00</option>";
	else $return = $return . "<option value='02:00' >02:00</option>";
	if ($heure == "03:00") $return = $return . "<option value='03:00' '$selected' >03:00</option>";
	else $return = $return . "<option value='03:00' >03:00</option>";
	if ($heure == "04:00") $return = $return . "<option value='04:00' '$selected' >04:00</option>";
	else $return = $return . "<option value='04:00' >04:00</option>";
	if ($heure == "05:00") $return = $return . "<option value='05:00' '$selected' >05:00</option>";
	else $return = $return . "<option value='05:00' >05:00</option>";
		
	return $return . '</select>';
}

// fonction qui demande à la base un nouvel id_article

function op_request_new_id($connect_id_auteur)
{
	$statut_nouv='prepa';
	$forums_publics = substr(lire_meta('forums_publics'),0,3);
	spip_query("INSERT INTO spip_articles (statut, date, accepter_forum) VALUES ( 'prepa', NOW(), '$forums_publics')");
	$article = mysql_insert_id();
	spip_query("DELETE FROM spip_auteurs_articles WHERE id_article = $article");
	spip_query("INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES ($connect_id_auteur, $article)");
	// lors de la demande d'un nouvel id article, il faut supprimer les relations éventuelles avec la table mots_articles
	spip_query("DELETE FROM spip_mots_articles WHERE id_article = '$article'");
	return $article;
}

// fonction qui liste les documents

function op_liste_vignette($article)
{


	$result = spip_query("SELECT * FROM spip_documents_articles WHERE id_article = $article");

	if (mysql_num_rows($result) > 0 ) {
		echo '<div id="block-center">';
		echo '<div id="block-center-titre"><b>&nbsp;&nbsp;Vos documents</b></div>';
		echo '<div id="block-content"><small>';
		echo '<center><p>'._T('opconfig:aide_inclusion').'</p></center></small>';
	}
	else return;

	echo '<center><table><tr>';
			
	while($row=mysql_fetch_array($result)){
		$id_doc = $row[0];
		$result2 = spip_query("SELECT fichier, mode FROM spip_documents WHERE id_document = $id_doc");
		while($row2=mysql_fetch_array($result2)){
			$empla = $row2['fichier'];
			$mode = $row2['mode'];
			
			// ajout du code inclusion
			if ($mode == "vignette") {
				echo '<td align="center"><img src="'.$empla.'" width="100" height="100" \><br />';
				echo '<code>&lt;img'.$id_doc.'|right&gt;</code><br />';
				echo '<code>&lt;img'.$id_doc.'|center&gt;</code><br />';
				echo '<code>&lt;img'.$id_doc.'|left&gt;</code><br />';
			}
			else {
				$tableau = split('[.]', $empla);
				$ext = $tableau[1];
				// ajout pour utiliser les vignettes spip pour documents
				list($fic, $largeur, $hauteur) = vignette_par_defaut($ext);
 				$image = "<img src='$fic'\n\theight='$hauteur' width='$largeur' />";
				echo '<td align="center">'.$image.'<br />';
				echo '<code>&lt;doc'.$id_doc.'|right&gt;</code><br />';
				echo '<code>&lt;doc'.$id_doc.'|center&gt;</code><br />';
				echo '<code>&lt;doc'.$id_doc.'|left&gt;</code><br />';
			}
			echo '</td>';
		}
	}
	echo '</tr></table>';

	echo '</center>';
	echo '</div></div><br />';
}

// renvoie sous forme de tableau la liste des extensions autorisée par spip

function get_types_documents() {
	$query = "SELECT extension FROM spip_types_documents";
	return spip_query($query);
}
	
// affichage du tableau extension

function afficher_tab($tab_ext) {

	while ($ext = mysql_fetch_array($tab_ext)) {
		$message = $message . $ext[0] .', ';
	}
	return $message;
}

// reliquat spipindy, fonction qui coupe les trop gros textes.

function coupe_trop_long($texte){    // utile pour les textes > 32ko
    if (strlen($texte) > 28*1024) {
        $texte = str_replace("\r\n","\n",$texte);
        $pos = strpos($texte, "\n\n", 28*1024);    // coupe para > 28 ko
        if ($pos > 0 and $pos < 32 * 1024) {
            $debut = substr($texte, 0, $pos)."\n\n<!--SPIP-->\n";
            $suite = substr($texte, $pos + 2);
        } else {
            $pos = strpos($texte, " ", 28*1024);    // sinon coupe espace
            if (!($pos > 0 and $pos < 32 * 1024)) {
                $pos = 28*1024;    // au pire (pas d'espace trouv'e)
                $decalage = 0; // si y'a pas d'espace, il ne faut pas perdre le caract`ere
            } else {
                $decalage = 1;
            }
            $debut = substr($texte,0,$pos + $decalage); // Il faut conserver l'espace s'il y en a un
            $suite = substr($texte,$pos + $decalage);
       }
  return (array($debut,$suite));
 }
 else return (array($texte,''));
}

?>
 
