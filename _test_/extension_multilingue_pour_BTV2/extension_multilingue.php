<?php
function ExtensionMultilingue_BT_toolbox($params) {

//test pour savoir si on est appelé par la btv2 ou récursivement par la btm( barre typo multilingue)
//c'est à dire que la btm va créer n nouvelles btv2 pour chaque langues, donc ce pipeline sera appelé pour chacune, et ne devra pas s'exécuter
if (strpos($params['champ'], "zone_multilingue") === FALSE)
{
	$fetch = function_exists('spip_fetch_array')?'spip_fetch_array':'sql_fetch';
	$ret = '';
	
	//le champ est passe soit sous la forme document.formulaire.champ, soit sous la forme document.getElementsByName('champ')[0]
	if (strpos($params['champ'], "document.formulaire") === false)
		$nom_champ = substr($params['champ'], strpos($params['champ'], "'")+1, strlen(substr($params['champ'], strpos($params['champ'], "'")+1))-5 );
	else
		$nom_champ = substr($params['champ'], strrpos($params['champ'], ".")+1);


	$langues_choisies = explode(",",lire_config('ExtensionMultilingue/langues_ExtensionMultilingue','fr,en,de'));	
	
	//edition des rubriques
	if ($_GET['exec'] == "rubriques_edit") {

		if (lire_config('ExtensionMultilingue/rubriques_'.$nom_champ.'_ExtensionMultilingue', '') != "on")
			return $params;
	
		if ($_GET['new'] == "oui") {
			$titre = filtrer_entites(_T('titre_nouvelle_rubrique'));
			$descriptif = "";
			$texte = "";
		} else {
			$id_rubrique_tmp = intval($_GET['id_rubrique']);
			$row = $fetch(spip_query("SELECT * FROM spip_rubriques WHERE id_rubrique='$id_rubrique_tmp'"));
	
			if (!$row) return $params;
	
			$titre = str_replace("\"","'",$row['titre']);
			$descriptif = $row['descriptif'];
			$texte = $row['texte'];
		}
	}
	//edition des articles
	else if ($_GET['exec'] == "articles_edit")
	{
		if (lire_config("ExtensionMultilingue/articles_".$nom_champ."_ExtensionMultilingue", '') != "on")
			return $params;

		if ($_GET['new'] == "oui") {
			$surtitre = "";
			$titre = filtrer_entites(_T('info_nouvel_article'));
			$soustitre = "";
			$descriptif = "";
			$chapo = "";
			$texte = "";
			$ps = "";
		} else {
			$id_article_tmp = intval($_GET['id_article']);
			$row = $fetch(spip_query("SELECT * FROM spip_articles WHERE id_article='$id_article_tmp'"));
	
			if (!$row) return $params;
	
			$surtitre = str_replace("\"","'",$row['surtitre']);
			$titre = str_replace("\"","'",$row['titre']);
			$soustitre = str_replace("\"","'",$row['soustitre']);
			$descriptif = $row['descriptif'];
			$texte = $row['texte'];
			$chapo = $row['chapo'];
			$ps = $row['ps'];
		}
		
	}
	//edition des breves
	else if ($_GET['exec'] == "breves_edit")
	{
		if (lire_config("ExtensionMultilingue/breves_".$nom_champ."_ExtensionMultilingue", '') != "on")
			return $params;
		if ($_GET['new'] == "oui") 
		{
			$titre = filtrer_entites(_T('titre_nouvelle_breve'));
			$texte = "";
			$lien_titre = "";
		} 
		else 
		{
			$id_breve_tmp = intval($_GET['id_breve']);
			$row = $fetch(spip_query("SELECT * FROM spip_breves WHERE id_breve='$id_breve_tmp'"));
	
			if (!$row) return $params;
	
			$titre = str_replace("\"","'",$row['titre']);
			$texte = $row['texte'];
			$lien_titre = str_replace("\"","'",$row['lien_titre']);
			
		}
	}
	//edition de la configuration du site
	else if ($_GET['exec'] == "configuration")
	{
		if (lire_config("ExtensionMultilingue/configuration_".$nom_champ."_ExtensionMultilingue", '') != "on")
			return $params;
		$titre = str_replace("\"","'",$GLOBALS['meta']["nom_site"]);
		$descriptif = $GLOBALS['meta']["descriptif_site"];

	}
	//edition des auteurs
	else if ($_GET['exec'] == "auteur_infos") {

		if (lire_config("ExtensionMultilingue/auteurs_".$nom_champ."_ExtensionMultilingue", '') != "on")
			return $params;
		$result = spip_query("SELECT * FROM spip_auteurs WHERE id_auteur=" . intval($_GET['id_auteur']) );

		if ($row = $fetch($result)) {
			$titre = str_replace("\"","'",$row["nom"]);
			$descriptif = $row["bio"];
		} else {
			$titre = "";
			$descriptif = "";
		}
	}
	//édition des groupes de mots clefs
	else if ($_GET['exec'] == "mots_type")
	{
		
		if (lire_config("ExtensionMultilingue/groupesmots_".$nom_champ."_ExtensionMultilingue", '') != "on")
			return $params;
		if ($_GET['new'] == "oui") {
		  	$titre = filtrer_entites(_T('titre_nouveau_groupe'));
		  	$descriptif = "";
			$texte = "";
		  
		} else {
			$id_groupe_tmp= intval($_GET['id_groupe']);
			$result_groupes = spip_query("SELECT * FROM spip_groupes_mots WHERE id_groupe=$id_groupe_tmp");

			while($row = $fetch($result_groupes)) {
				$titre = str_replace("\"","'",$row['titre']);
				$descriptif = $row['descriptif'];
				$texte = $row['texte'];
				
			}
		}

	}
	//édition des mots clefs
	else if ($_GET['exec'] == "mots_edit")
	{
		
		if (lire_config("ExtensionMultilingue/mots_".$nom_champ."_ExtensionMultilingue", '') != "on")
			return $params;
	
		$id_mot_tmp = intval($_GET['id_mot']);
		$row = $fetch(spip_query("SELECT * FROM spip_mots WHERE id_mot='$id_mot_tmp'"));
		 if ($row) {
			$titre = str_replace("\"","'",$row['titre']);
			$descriptif = $row['descriptif'];
			$texte = $row['texte'];
	 	} else {
			$titre = filtrer_entites(_T('texte_nouveau_mot'));
			$descriptif = "";
			$texte = "";
	 	}

	}

	//édition des sites référencés
	else if ($_GET['exec'] == "sites_edit")	{

		if (lire_config("ExtensionMultilingue/sites_".$nom_champ."_ExtensionMultilingue", '') != "on")
			return $params;
		$result = spip_query("SELECT * FROM spip_syndic WHERE id_syndic=" . intval($_GET['id_syndic']) );

		if ($row = $fetch($result)) {
			$titre = str_replace("\"","'",$row["nom_site"]);
			$descriptif = $row["descriptif"];
		} else {
			$titre = "";
			$descriptif = "";
		}
	}
	
	//dans les cas ou la btm doit s'éxécuter :
	if (($_GET['exec'] == "sites_edit") || ($_GET['exec'] == "auteur_infos") || ($_GET['exec'] == "articles_edit") || ($_GET['exec'] == "breves_edit") || ($_GET['exec'] == "mots_edit") || ($_GET['exec'] == "mots_type") || ($_GET['exec'] == "configuration") || ($_GET['exec'] == "rubriques_edit"))	
	{
		
		//en fonction du champ que l'on est en train de traiter...
		if (($nom_champ == "titre") || ($nom_champ == "nom_site") || ($nom_champ == "change_type") || ($nom_champ == "nom"))
		{
			//on gere le numero dans un input separe
			$ret .= "
			<label>Num&eacute;ro : <input type='text' name=\"numero_zone_multilingue_".$nom_champ."\" value=\"".extension_multilingue_extraire_numero($titre)."\" size='5' /></label><div class=\"container-onglets\">
        		<ul class=\"tabs-nav\">";
        		for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "        <li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
        		}
			$ret .= "</ul>";

			
			//on ajouter un onglet par langue, et dans chaque onglet une BTV2 et un input
			// en réalité les div des onglets sont ajoutés en bas de la page et seulement à l'initilisation (voir le header_privé) ils sont déplacés 
			// vers le champ à éditer
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "
				<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typotitres_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				}
				$ret .= "<input type='text' class='formo' name=\"zone_multilingue_".$i."_".$nom_champ."\" value=\"".supprimer_numero(extension_multilingue_extraire_multi_lang($titre, $langues_choisies[$i]))."\" size='40'  /></div>";
			}
        		
			$ret .= "</div>";
			
				
		}
		if ($nom_champ == "lien_nom")
		{
			$ret .= "
			<div class=\"container-onglets\">
        		<ul class=\"tabs-nav\">";
        		for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "        <li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
        		}
			$ret .= "</ul>";

			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "
				<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typotitres_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				}
				$ret .= "<input type='text' class='formo' name=\"zone_multilingue_".$i."_".$nom_champ."\" value=\"".extension_multilingue_extraire_multi_lang($titre, $langues_choisies[$i])."\" size='40'  /></div>";
			}
        		
			$ret .= "</div>";
			
				
		}
		else if (($nom_champ == "descriptif") || ($nom_champ == "descriptif_site") || ($nom_champ == "bio"))
		{
			$ret .= "<div class=\"container-onglets\">
    			<ul class=\"tabs-nav\">";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
        	        		$ret.="<li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
        	        }
        		$ret.="	</ul>";

			for ($i=0; $i<count($langues_choisies); $i++)
			{	
				$ret .= "<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typodescriptifs_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				
				}
				$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_".$i."_".$nom_champ."\" class=\"forml\" rows=\"6\" cols=\"40\">".entites_html(extension_multilingue_extraire_multi_lang($descriptif, $langues_choisies[$i]))."</textarea></div>";
        		}
			$ret.="</div>";
			
		}
	
		else if ($nom_champ == "texte")
		{
			
			
			$ret .= "<div class=\"container-onglets\">
        		<ul class=\"tabs-nav\">";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{	
        	        	$ret.="	<li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
			}
        	        $ret.="	</ul>";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typotextes_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				}
				$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_".$i."_".$nom_champ."\" class=\"forml\" rows=\"15\" cols=\"40\">".entites_html(extension_multilingue_extraire_multi_lang($texte, $langues_choisies[$i]))."</textarea></div>";
        		}
			$ret .="</div>";
			
		}
		else if ($nom_champ == "surtitre")
		{
			$ret .= "
			<div class=\"container-onglets\">
        		<ul class=\"tabs-nav\">";
        		for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "        <li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
        		}
			$ret .= "</ul>";

			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "
				<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typotitres_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				}
				$ret .= "<input type='text' class='formo' name=\"zone_multilingue_".$i."_".$nom_champ."\" value=\"".extension_multilingue_extraire_multi_lang($surtitre, $langues_choisies[$i])."\" size='40'  /></div>";
			}
        		
			$ret .= "</div>";
			
		}
		else if ($nom_champ == "soustitre")
		{
			$ret .= "
			<div class=\"container-onglets\">
        		<ul class=\"tabs-nav\">";
        		for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "        <li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
        		}
			$ret .= "</ul>";

			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "
				<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typotitres_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				}
				$ret .= "<input type='text' class='formo' name=\"zone_multilingue_".$i."_".$nom_champ."\" value=\"".extension_multilingue_extraire_multi_lang($soustitre, $langues_choisies[$i])."\" size='40'  /></div>";
			}
        		
			$ret .= "</div>";
			
		}
		else if ($nom_champ == "lien_titre")
		{
			
			$ret .= "
			<div class=\"container-onglets\">
        		<ul class=\"tabs-nav\">";
        		for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "        <li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
        		}
			$ret .= "</ul>";

			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "
				<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typotitres_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				}
				$ret .= "<input type='text' class='formo' name=\"zone_multilingue_".$i."_".$nom_champ."\" value=\"".extension_multilingue_extraire_multi_lang($lien_titre, $langues_choisies[$i])."\" size='40'  /></div>";
			}
        		
			$ret .= "</div>";
			
		}
		else if ($nom_champ == "chapo")
		{
			$ret .= "<div class=\"container-onglets\">
    			<ul class=\"tabs-nav\">";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
        	        		$ret.="<li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
        	        }
        		$ret.="	</ul>";

			for ($i=0; $i<count($langues_choisies); $i++)
			{	
				$ret .= "<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typodescriptifs_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				}
				$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_".$i."_".$nom_champ."\" class=\"forml\" rows=\"5\" cols=\"40\">".entites_html(extension_multilingue_extraire_multi_lang($chapo, $langues_choisies[$i]))."</textarea></div>";
        		}
			$ret.="</div>";
			
		}
		else if ($nom_champ == "ps")
		{
			$ret .= "<div class=\"container-onglets\">
    			<ul class=\"tabs-nav\">";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
        	        		$ret.="<li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
        	        }
        		$ret.="	</ul>";

			for ($i=0; $i<count($langues_choisies); $i++)
			{	
				$ret .= "<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typodescriptifs_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				}
				$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_".$i."_".$nom_champ."\" class=\"forml\" rows=\"5\" cols=\"40\">".entites_html(extension_multilingue_extraire_multi_lang($ps, $langues_choisies[$i]))."</textarea></div>";
        		}
			$ret.="</div>";
			
		}
    		
	}		
	$params['flux'] .= $ret;
	return $params;
	}	
}

function extension_multilingue_appelsjavascript() {
	//insere les appels javascript et css requis dans le head de la page	

	return " <link rel=\"stylesheet\" href=\"".find_in_path('css/jquery.tabs.css')."\" type=\"text/css\" media=\"print, projection, screen\"><!-- Additional IE/Win specific style sheet (Conditional Comments) --><!--[if lte IE 7]>
        		<link rel=\"stylesheet\" href=\"".find_in_path('css/jquery.tabs-ie.css')."\" type=\"text/css\" media=\"projection, screen\">
        		<![endif]-->
           
        		<script type=\"text/javascript\" src=\"".find_in_path('javascript/jquery.tabs.js')."\"></script>";
}
function extension_multilingue_afficheronglets() {
	//appel à la fonction jQuery d'affichage des onglets
	return "$('.container-onglets').tabs();
			$('.container-onglets').find('table.spip_barre').css(\"display\", \"block\");});";
}

function ExtensionMultilingue_header_prive($texte) {

$langues_choisies = explode(",",lire_config('ExtensionMultilingue/langues_ExtensionMultilingue','fr,en,de'));	
	
$newtab="";
	if (($_GET['exec'] == "sites_edit") || ($_GET['exec'] == "articles_edit") || ($_GET['exec'] == "auteur_infos") || ($_GET['exec'] == "breves_edit") || ($_GET['exec'] == "mots_edit") || ($_GET['exec'] == "mots_type") || ($_GET['exec'] == "configuration") || ($_GET['exec'] == "rubriques_edit"))	
	{

		// insertion des librairies javascript requises et initialisation javascript :
		// - masquer le précédent textarea ou input du champ,
		// - mettre les onglets en place (qui étaient provisoirement placés en pied de page) -> déplacement avec jQuery
		// - puis redirection de l'évènement submit pour fusionner l'ensemble des onglets dans un <multi> avant de faire le vrai submit (calculer_actions_head_multilingues)
	

		//cas de l'edition des rubriques
		if ($_GET['exec'] == "rubriques_edit")
		{
			$newtab .= extension_multilingue_appelsjavascript()."
			       
			<script type=\"text/javascript\">
			$(document).ready(function() {";
		
			if (lire_config('barre_typo_generalisee/rubriques_titre_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/rubriques_titre_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues_titre("document.formulaire.titre", $langues_choisies, "input")."$('#barre_typo_rubrique_titre table.spip_barre').css(\"display\", \"none\");";
			}
			if (lire_config('barre_typo_generalisee/rubriques_descriptif_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/rubriques_descriptif_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif", $langues_choisies, "textarea")."$('#barre_typo_rubrique_descriptif table.spip_barre').css(\"display\", \"none\");";
			}
			if (lire_config('barre_typo_generalisee/rubriques_texte_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/rubriques_texte_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues("document.formulaire.texte", $langues_choisies, "textarea")."$('#barre_typo_rubrique_texte table.spip_barre').css(\"display\", \"none\");";	
			}
			$newtab .= extension_multilingue_afficheronglets();
		
			$newtab .= "</script>";
		}
		//cas de l'edition des articles
		else if ($_GET['exec'] == "articles_edit")
		{
			$newtab .= extension_multilingue_appelsjavascript()."
		       
			<script type=\"text/javascript\">
			$(document).ready(function() {";
		
			if (lire_config('barre_typo_generalisee/articles_surtitre_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/articles_surtitre_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues("document.formulaire.surtitre", $langues_choisies, "input")."$('#barre_typo_article_surtitre table.spip_barre').css(\"display\", \"none\");";
			}
			if (lire_config('barre_typo_generalisee/articles_titre_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/articles_titre_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues_titre("document.formulaire.titre", $langues_choisies, "input")."$('#barre_typo_article_titre table.spip_barre').css(\"display\", \"none\");";
			}
			if (lire_config('barre_typo_generalisee/articles_soustitre_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/articles_soustitre_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues("document.formulaire.soustitre", $langues_choisies, "input")."$('#barre_typo_article_soustitre table.spip_barre').css(\"display\", \"none\");";
			}
			if (lire_config('barre_typo_generalisee/articles_descriptif_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/articles_descriptif_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif", $langues_choisies, "textarea")."$('#barre_typo_article_descriptif table.spip_barre').css(\"display\", \"none\");";
			}
				if (lire_config("ExtensionMultilingue/articles_texte_ExtensionMultilingue", '') == "on") 
				{	
				$newtab .= calculer_actions_head_multilingues("document.formulaire.texte", $langues_choisies, "textarea")."$('table.spip_barre').css(\"display\", \"none\");";		
				
				//on annule le decoupage des textes trop longs fait par SPIP
				$newtab .= "$('textarea[@id=texte1]').css(\"display\", \"none\");$('textarea[@id=texte1]').val('');";
				$newtab .= "$('textarea[@id=texte2]').css(\"display\", \"none\");$('textarea[@id=texte2]').val('');";
				$newtab .= "$('textarea[@id=texte3]').css(\"display\", \"none\");$('textarea[@id=texte3]').val('');";
				$newtab .= "$('textarea[@id=texte4]').css(\"display\", \"none\");$('textarea[@id=texte4]').val('');";
				$newtab .= "$('textarea[@id=texte5]').css(\"display\", \"none\");$('textarea[@id=texte5]').val('');";
				$newtab .= "$('textarea[@id=texte6]').css(\"display\", \"none\");$('textarea[@id=texte6]').val('');";
				$newtab .= "$('textarea[@id=texte7]').css(\"display\", \"none\");$('textarea[@id=texte7]').val('');";
				$newtab .= "$('textarea[@id=texte8]').css(\"display\", \"none\");$('textarea[@id=texte8]').val('');";
				$newtab .= "$('textarea[@id=texte9]').css(\"display\", \"none\");$('textarea[@id=texte9]').val('');";
				}
			if (lire_config('barre_typo_generalisee/articles_chapo_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/articles_chapo_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues("document.formulaire.chapo", $langues_choisies, "textarea")."$('#barre_typo_article_chapo table.spip_barre').css(\"display\", \"none\");";	
			}
			if (lire_config('barre_typo_generalisee/articles_ps_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/articles_ps_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues("document.formulaire.ps", $langues_choisies, "textarea")."$('#barre_typo_article_ps table.spip_barre').css(\"display\", \"none\");";	
			}
			$newtab .= extension_multilingue_afficheronglets();
		
			$newtab .= "</script>";
		}	
		//cas de l'edition des breves
		else if ($_GET['exec'] == "breves_edit")
		{
		$newtab .= extension_multilingue_appelsjavascript()."
		       
			<script type=\"text/javascript\">
			$(document).ready(function() {";
		
			if (lire_config('barre_typo_generalisee/breves_titre_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/breves_titre_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues_titre("document.formulaire.titre", $langues_choisies, "input")."$('#barre_typo_breve_titre table.spip_barre').css(\"display\", \"none\");";
			}
				if (lire_config("ExtensionMultilingue/breves_texte_ExtensionMultilingue", '') == "on")
				{ 
					$newtab .= calculer_actions_head_multilingues("document.formulaire.texte", $langues_choisies, "textarea")."$('table.spip_barre').css(\"display\", \"none\");";	
				}
			
			if (lire_config('barre_typo_generalisee/breves_lien_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/breves_lien_titre_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues("document.formulaire.lien_titre", $langues_choisies, "input")."$('#barre_typo_breve_lien_titre table.spip_barre').css(\"display\", \"none\");";
			}
			$newtab .= extension_multilingue_afficheronglets();
		
			$newtab .= "</script>";
		}
		//cas de l'edition de la configuration
		else if ($_GET['exec'] == "configuration")
		{
			$newtab .= extension_multilingue_appelsjavascript()."
		       
			<script type=\"text/javascript\">
			$(document).ready(function() {";
		
			if (lire_config('barre_typo_generalisee/configuration_nom_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/configuration_nom_site_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues_titre("document.formulaire.nom_site", $langues_choisies, "input")."$('#barre_typo_configuration_nom_site table.spip_barre').css(\"display\", \"none\");";
			}
			if (lire_config('barre_typo_generalisee/configuration_description_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/configuration_descriptif_site_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif_site", $langues_choisies, "textarea")."$('#barre_typo_configuration_descriptif_site table.spip_barre').css(\"display\", \"none\");";
			}
			$newtab .= extension_multilingue_afficheronglets();
		
			$newtab .= "</script>";
		}
		//cas de l'edition des groupes de mots clefs
		else if ($_GET['exec'] == "mots_type") 
		{
			$newtab .= extension_multilingue_appelsjavascript()."
		       
			<script type=\"text/javascript\">
		
			$(document).ready(function() {";
			if (lire_config('barre_typo_generalisee/groupesmots_nom_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/groupesmots_change_type_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues_titre("document.formulaire.change_type", $langues_choisies, "input")."$('#barre_typo_groupemot_nom table.spip_barre').css(\"display\", \"none\");";
			}
			if (lire_config('barre_typo_generalisee/groupesmots_descriptif_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/groupesmots_descriptif_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif", $langues_choisies, "textarea")."$('#barre_typo_groupemot_descriptif table.spip_barre').css(\"display\", \"none\");";
			}
			if (lire_config('barre_typo_generalisee/groupesmots_texte_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/groupesmots_texte_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues("document.formulaire.texte", $langues_choisies, "textarea")."$('#barre_typo_groupemot_texte table.spip_barre').css(\"display\", \"none\");";
			}
			$newtab .= extension_multilingue_afficheronglets();
		
			$newtab .= "</script>";
		}
		//cas de l'edition des mots clefs
		else if ($_GET['exec'] == "mots_edit")
		{
			$newtab .= extension_multilingue_appelsjavascript()."
		       
			<script type=\"text/javascript\">
			$(document).ready(function() {";
			if (lire_config('barre_typo_generalisee/mots_nom_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/mots_titre_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues_titre("document.formulaire.titre", $langues_choisies, "input")."$('#barre_typo_mot_nom table.spip_barre').css(\"display\", \"none\");";
			}
			if (lire_config('barre_typo_generalisee/mots_descriptif_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/mots_descriptif_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif", $langues_choisies, "textarea")."$('#barre_typo_mot_descriptif table.spip_barre').css(\"display\", \"none\");";
			}
			if (lire_config('barre_typo_generalisee/mots_texte_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/mots_texte_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues("document.formulaire.texte", $langues_choisies, "textarea")."$('#barre_typo_mot_texte table.spip_barre').css(\"display\", \"none\");";
			}
			$newtab .= extension_multilingue_afficheronglets();
		
			$newtab .= "</script>";
		}
		//cas de l'edition des sites references
		else if ($_GET['exec'] == "sites_edit") 
		{
			$newtab .= extension_multilingue_appelsjavascript()."
		       
			<script type=\"text/javascript\">
			$(document).ready(function() {";
		
			if (lire_config('barre_typo_generalisee/sites_nom_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/sites_nom_site_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues_titre("document.formulaire.nom_site", $langues_choisies, "input")."$('#barre_typo_site_nom table.spip_barre').css(\"display\", \"none\");";
			}
			if (lire_config('barre_typo_generalisee/sites_description_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/sites_descriptif_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif", $langues_choisies, "textarea")."$('#barre_typo_site_descriptif table.spip_barre').css(\"display\", \"none\");";
			}
			$newtab .= extension_multilingue_afficheronglets();
		
			$newtab .= "</script>";
		}
		//cas de l'edition des auteurs
		else if ($_GET['exec'] == "auteur_infos") 
		{
			$newtab .= extension_multilingue_appelsjavascript()."
		       
			<script type=\"text/javascript\">
			$(document).ready(function() {";
		
			if (lire_config('barre_typo_generalisee/auteurs_signature_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/auteurs_nom_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues_titre("document.formulaire.nom", $langues_choisies, "input")."$('#barre_typo_auteur_nom table.spip_barre').css(\"display\", \"none\");";
			}
			if (lire_config('barre_typo_generalisee/auteurs_quietesvous_barre_typo_generalisee') == "on")
			{
				if (lire_config("ExtensionMultilingue/auteurs_bio_ExtensionMultilingue", '') == "on") $newtab .= calculer_actions_head_multilingues("document.formulaire.bio", $langues_choisies, "textarea")."$('#barre_typo_auteur_bio table.spip_barre').css(\"display\", \"none\");";
			}
			$newtab .= extension_multilingue_afficheronglets();
		
			$newtab .= "</script>";
		}
		
	}

	
	//inclure librairie pour l'affichage des onglets
	return $texte.$newtab;
}


// http://doc.spip.org/@multi_trad
function extension_multilingue_multi_trad_lang ($trads, $langue_souhaitee) {
	 

	if (isset($trads[$langue_souhaitee])) {
		return $trads[$langue_souhaitee];

	}	// cas des langues xx_yy
	else if (ereg('^([a-z]+)_', $spip_lang, $regs) AND isset($trads[$regs[1]])) {
		return $trads[$regs[1]];
	}	
	// sinon, renvoyer la premiere du tableau
	// remarque : on pourrait aussi appeler un service de traduction externe
	// ou permettre de choisir une langue "plus proche",
	// par exemple le francais pour l'espagnol, l'anglais pour l'allemand, etc.
	else  /*return array_shift($trads);*/ return "";
}

// analyse un bloc multi
// http://doc.spip.org/@extraire_trad
function extension_multilingue_extraire_trad_lang ($bloc, $langue_souhaitee) {
	$lang = '';
// ce reg fait planter l'analyse multi s'il y a de l'{italique} dans le champ
//	while (preg_match("/^(.*?)[{\[]([a-z_]+)[}\]]/siS", $bloc, $regs)) {
	while (preg_match("/^(.*?)[\[]([a-z_]+)[\]]/siS", $bloc, $regs)) {
		$texte = trim($regs[1]);
		if ($texte OR $lang)
			$trads[$lang] = $texte;
		$bloc = substr($bloc, strlen($regs[0]));
		$lang = $regs[2];
	}
	$trads[$lang] = $bloc;

	// faire la traduction avec ces donnees
	return extension_multilingue_multi_trad_lang($trads, $langue_souhaitee);
}

// repere les blocs multi dans un texte et extrait le bon
// http://doc.spip.org/@extraire_multi
function extension_multilingue_extraire_multi_lang ($letexte, $langue_souhaitee) {
	if (strpos($letexte, '<multi>') === false) return $letexte; // perf
	if (preg_match_all("@<multi>(.*?)</multi>@sS", $letexte, $regs, PREG_SET_ORDER))
		foreach ($regs as $reg)
			$letexte = str_replace($reg[0], extension_multilingue_extraire_trad_lang($reg[1], $langue_souhaitee), $letexte);
	return $letexte;
}

function calculer_actions_head_multilingues ($champ, $langues_choisies, $typedechamp)
{
	//réassembler les textareas ou inputs des onglets dans un <multi>[fr]...[en]..[..]..</multi> avant le submit
	$nom_champ = str_replace(".", "_", $champ);
	$champ_fin = substr($champ, strrpos($champ, ".") + 1);

			$resultat .= "$('".$typedechamp."[@name=".$champ_fin."]').css(\"display\", \"none\");";
			$resultat .= "$(\"".$typedechamp."[@name=".$champ_fin."]\").parents().filter(\"form\").bind(\"submit\", function(e) { 
				var valeur".$champ_fin."='';
			";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$resultat .= "if ($('".$typedechamp."[@name=zone_multilingue_".$i."_".$champ_fin."]').val() != '') valeur".$champ_fin."+='[".$langues_choisies[$i]."]'+$('".$typedechamp."[@name=zone_multilingue_".$i."_".$champ_fin."]').val();
				";
			}
			
			$resultat .= "if (valeur".$champ_fin." != '') $('".$typedechamp."[@name=".$champ_fin."]').val('<multi>'+valeur".$champ_fin."+'</multi>'); else $('".$typedechamp."[@name=".$champ_fin."]').val('');});";
			return $resultat;
}
function calculer_actions_head_multilingues_titre ($champ, $langues_choisies, $typedechamp)
{
	//idem fonction précédente mais avec gestion du numéro dans un champ à part que l'on colle devant le titre
	// N°. <multi>[fr]...[en]..[..]..</multi>
	
	$nom_champ = str_replace(".", "_", $champ);
	$champ_fin = substr($champ, strrpos($champ, ".") + 1);

			$resultat .= "$('".$typedechamp."[@name=".$champ_fin."]').css(\"display\", \"none\");";
			$resultat .= "$(\"".$typedechamp."[@name=".$champ_fin."]\").parents().filter(\"form\").bind(\"submit\", function(e) { 
				var valeur".$champ_fin."='';
			";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$resultat .= "if ($('".$typedechamp."[@name=zone_multilingue_".$i."_".$champ_fin."]').val() != '') valeur".$champ_fin."+='[".$langues_choisies[$i]."]'+$('".$typedechamp."[@name=zone_multilingue_".$i."_".$champ_fin."]').val();
				";
			}
			$resultat .= "var numero = $('".$typedechamp."[@name=numero_zone_multilingue_".$champ_fin."]').val();
				if (valeur".$champ_fin." != '') 
				     {
					if (numero != '')
					{	$('".$typedechamp."[@name=".$champ_fin."]').val(numero+'. <multi>'+valeur".$champ_fin."+'</multi>'); 
					} 
					else
					{	$('".$typedechamp."[@name=".$champ_fin."]').val('<multi>'+valeur".$champ_fin."+'</multi>'); 
					}
				    }
				    else 
				    {
					$('".$typedechamp."[@name=".$champ_fin."]').val('');
				    }
			});";
			return $resultat;
}
function extension_multilingue_extraire_numero($titre) {
	if (ereg("([0-9]+)\.", $titre, $match)) return $match[1];
	return '';
}
?>
