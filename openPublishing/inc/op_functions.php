<?php

/* Ce fichier contient toutes les fonctions utilisé par la balise #FORMULAIRE_ARTICLE
 */

// fonction testant la validite de la configuration du plugin
// prend en entree le tableau de configuration
// retourne un tableau contenant deux valeurs :
//   - code : si true, la configuration est valide, sinon elle ne l'est pas.
//   - message : les messages explicatif pour rendre la configuration valide.
function test_configuration($config)
{
	$tab = array(
		'code' => false,
		'message' => array()
	);
	
	/*
	 * Test prealable : si pas de configuration, on jette
	 */
	if (!is_array($config))
	{
		$tab['message'][] = _T('opconfig:erreur_die');
		return $tab;
	}
	
	
	/*
	 * Premier test : si pas d'auteur "anonyme", on jette.
	 */
	  
	if(!$config['IDAuteur']) $tab['message'][] = _T('opconfig:erreur_auteur_anonyme');
	
	
	/*
	 * Second test : si pas de rubrique "openPublishing", on jette.
	 */
	$rubrique = false; 
        foreach ($config as $key => $val) 
        {
        	if ((substr($key,0,3)) == "op_") 
        	{
	        	if ($val == "openPublishing") 
	        	{
	        		$rubrique = true;
			}
		}
	}
	if (!$rubrique) $tab['message'][] = _T('opconfig:erreur_rubrique');
	
	
	/*
	 *Troisieme test : si la gestion des mots-cles est active alors qu'aucun mot-cle n'a ete choisi
	 */
	if ($config['MotCle']=="yes")
	{
		$groupe = false;
		foreach ($config as $key => $val)
		{
	                if ((substr($key,0,7)) == "groupe_")
	                {
	                	if ($val == "openPublishing")
	                	{
	                		$groupe = true;
	                	}
			}
		}
		if (!$groupe) $tab['message'][] = _T('opconfig:erreur_groupe');
	}
	                                                    
	if (count($tab['message']) == 0) $tab['code'] = true;
	
	return $tab;
}

// fonction recherchant des formulaires Publication ouverte appartenant à d'autres plugin
function recherche_formulaire($env) {
	$env = @unserialize($env);
		
	// pour chaque plugin, vérifier si il existe un fichier
	// formulaire_nom_du_plugin.html dans le répertoire pub_ouverte/
	$tab_balise = array();
	foreach ($GLOBALS['plugins'] as $plugin) {
		if (file_exists(_DIR_PLUGINS.$plugin.'/pub_ouverte/formulaire_'.$plugin.'.html')) {

 			// contenu du formulaire
			$env['formulaire_'.$plugin] = inclure_balise_dynamique(
				'pub_ouverte/formulaire_'.$plugin, // fond
				0, // delai
				array ( // environnement
				 'test' => 'ceci est un test'
				)
			);
			
			return $env['formulaire_'.$plugin];
			//array(
			//	'formulaire_'.$plugin,
				
			// ajouter dans env le formulaire
			$tab_env[] = inclure_balise_dynamique(
			array('pub_ouverte/formulaire_'.$plugin,
				0,
				array( // le contexte du formulaire
					'id_article' => $article,
					'bouton' => $bouton,
				)
			), false);

			//$bouton= 'Ajouter l\'image ou le document';
			//$tab_balise[] = 
			
		}
	}
	

	$env = @serialize($env);
	return $env;
}

// fonction recherchant un éventuel logo pour affichage
function logo_article($id_article) {
	$nom = 'arton' . intval($id_article);
	$formats_logos = Array('jpg' ,'png', 'gif', 'bmp', 'tif');

	foreach ($formats_logos as $format) {
		if (@file_exists($d = (_DIR_LOGOS . $nom . '.' . $format)))
			return '<img src="'.$d.'" title="logo article" width="50" height="50"/>';
	}
	return '';
}

// fonction qui affiche la zone de texte et la barre de typographie

function barre_article($texte, $rows, $cols, $lang='')
{
	static $num_formulaire = 0;
	//include_ecrire('inc/layer');
	include_spip('inc/layer');

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

function checkbox_AuteurSpip($choix_AuteurSpip) {
	$checked = '';
	if ($choix_AuteurSpip == "OK") $checked = "CHECKED";
	return "<input type='checkbox' name='choix_AuteurSpip' value='OK' '$checked' />&nbsp;"._T('opconfig:choix_auteur_spip')."<br />";
}
// pour garder la valeur lors d'un rechargement de page

function select_annee($annee) {
	$selected = "";
	if ($annee !== '') $selected = "SELECTED";
	$return = "<select name='annee'>"; // pour le moment ne prend en compte que 5 ans
	for ($i = 0;$i<5;$i++) {
		$a = 2008 + $i;
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
	
	sql_insertq(
		'spip_articles',
		array (
			'statut' => 'prepa',
			'date' => 'NOW()',
			'accepter_forum' => $forum_publics)
	);

	$ret = sql_fetch(sql_select(
		array('MAX(id_article) as id_article'),
		array('spip_articles')
	));

	$article = $ret['id_article'];

	sql_delete(
		'spip_auteurs_articles',
		array('id_article = '.sql_quote($article).' LIMIT 1')
	);

	// lors de la demande d'un nouvel id article, il faut supprimer les relations éventuelles avec la table mots_articles
	sql_delete(
		'spip_mots_articles',
		array('id_article = '.sql_quote($article))
	);
	
	return $article;
}

?>