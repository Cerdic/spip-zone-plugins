<?
/* générateur de clones de rubrique avec articles
  
	cy_altern &copy; 2007 - Distribu&#233; sous licence GNU/LGPL
	
*/


function exec_gener_cont() {
  // PARAMETRES de configuration gérés par cfg 
	 $num_source = lire_config('gener_cont/secteur_source');   // le numéro de la rubrique qui contient les sous-rubriques modèles
	 $num_cible = lire_config('gener_cont/secteur_cible');    // le numéro du secteur où la création de clones est autorisée
	 $max_copies = lire_config('gener_cont/nb_max_clones');   // le nombre max de copies (sous-répertoires) autorisé
	 $statut_articles_defaut = lire_config('gener_cont/statut_articles_defaut');  // le statut par défaut des articles clonés
	 		
	 include_spip("inc/presentation");

  // vérifier les droits
    global $connect_statut;
  	global $connect_toutes_rubriques;
    if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
  		debut_page(_T('titre'), "generateur_rubriques", "plugin");
  		echo _T('avis_non_acces_page');
  		fin_page();
  		exit;
  	}
	
 // config des noms de tables SPIP
//		global $table_prefix;
//		$prefix_tables_SPIP = $table_prefix;	 // $table_prefix définie dans ecrire/inc_version.php (qui appelle mes_options.php s'il existe)
		$Trubriques = "spip_rubriques";
		$Tarticles = "spip_articles";
		$Tauteurs = "spip_auteurs";
		$Tauteurs_rubriques = "spip_auteurs_rubriques";
		$Tauteurs_articles = "spip_auteurs_articles";

 // choix auteur uniquement pour les admins complets 
		if ($connect_toutes_rubriques 
			  AND isset($_POST['auteur_copies']) 
				AND $_POST['auteur_copies'] != '') { 
				$id_utilisateur = $_POST['auteur_copies'];
		}
		else {
				 $id_utilisateur = $GLOBALS['auteur_session']['id_auteur'];
		}

// traitement des données envoyées par le formulaire
		if (isset($_POST['num_parent']) AND $_POST['num_parent'] != '' 
			  AND isset($_POST['nb_copies']) AND $_POST['nb_copies'] != ''
				AND isset($_POST['num_parent_2copy']) AND $_POST['num_parent_2copy'] != ''
				) {
			 $num_parent = $_POST['num_parent'];
			 $nb_copies = $_POST['nb_copies'];
			 $num_parent_2copy = $_POST['num_parent_2copy'];
			 
			 $Terr_rub = array();
			 $Terr_art = array();
			 
// ici le début de la boucle pour la création d'un clone de rubrique
  // étape 1 : création de la rubrique
			 $result4 = spip_query("SELECT titre, texte, descriptif FROM $Trubriques WHERE id_rubrique = $num_parent_2copy LIMIT 1");
			 $row4 = spip_fetch_array($result4);
			 $titre_rub = addslashes($row4['titre']);
		// récup des champs constituant le titre
    	 $Tchamps = array();
			 for ($j = 1; $j < 4; $j++) {
			 		 if ($_POST['champ_'.$j.'_titre_rub'] AND $_POST['champ_'.$j.'_titre_rub'] != '') {
					 		$champ_ec = trim($_POST['champ_'.$j.'_titre_rub']);
					 // traiter les valeurs débutant par # (#INCREMENT ou appel des champs #TITRE, #TEXTE et #DESCRIPTIF pour récup du contenu des champs de la rubrique clonée)
							if (strpos($champ_ec, '#') === 0) {
								 $champ_ec = strtolower(substr($champ_ec, 1));
  							 if ($champ_ec == "increment") {
  					 			  $debut_increment_titre_rub = ( (isset($_POST['debut_increment_titre_rub']) AND $_POST['debut_increment_titre_rub'] != '') ? intval($_POST['debut_increment_titre_rub']) : 1);
  								  $Tchamps[$j] = '#INCREMENT';
  						   }
  					 		 else {
  					 				  $Tchamps[$j] = $row4[$champ_ec];
  					     }
							}
					// si pas de # en début de valeur, récupérer le contenu du champ POST tel quel
							else {
									 $Tchamps[$j] = $champ_ec;
							}
					 }
			 }
			 
			 $texte_rub = addslashes($row4['texte']);
			 $descriptif_rub = addslashes($row4['descriptif']);
			 $date_ec = date("Y-m-j H:i:s");
			 
			 for ($i = 1; $i <= $_POST['nb_copies']; $i ++) {
      			 if (count($Tchamps) > 0) {
								$titre_rub = '';
								for ($n = 1; $n <= count($Tchamps); $n++) {
										if (trim($Tchamps[$n]) == '#INCREMENT') {
											 $titre_rub .= $debut_increment_titre_rub + $i - 1;
										}
										else {
												 $titre_rub .= addslashes($Tchamps[$n]);
										}
								}
      			 }
						 spip_query("INSERT INTO $Trubriques (id_rubrique, id_parent, titre, id_secteur, statut, date, texte, descriptif) 
						 						 VALUES ('', $num_parent, '$titre_rub', $num_cible, 'publie', '$date_ec', '$texte_rub', '$descriptif_rub')" );
  	 				 if (mysql_error() != '') {
  					 		$Terr_rub[] = 'insertion rubrique n°'.$i.' : erreur => '.mysql_error();
  							break;
  					 }
  				   else {  // création rubrique = OK
  				 	  		$id_rub_ec = mysql_insert_id();
							// si l'utilisateur est un admin restreint, lui attribuer la rubrique créée
  								if (!$connect_toutes_rubriques) {
											spip_query("INSERT INTO $Tauteurs_rubriques (id_rubrique, id_auteur) VALUES ($id_rub_ec, $id_utilisateur)");
        			 				if (mysql_error() != '') {
        							 	 $Terr_rub[] = 'insertion auteur_rubrique n°'.$i.' : erreur => '.mysql_error();
    									 	 break;
        							}
							    }
    						 
    // Etape 2 : création des articles		
     // ici, c'est le bide : une mauvaise bidouille pour récupérer les articles de la rubrique à copier 
     // TO DO : faire une copie de l'arborescence de la rubrique (un pt'it coup de récursif en vue... y'a un truc à retrouver dans ak !)
     // avec la création des champs automatique du style $$nom_champ_spip = ...
    			 			  if (isset($_POST['statut_articles']) AND $_POST['statut_articles'] != '') {
										 $statut_articles = $_POST['statut_articles'];
									}
									else {
											 $statut_articles = $statut_articles_defaut;
									}
									$result5 = spip_query("SELECT * FROM $Tarticles WHERE id_rubrique = $num_parent_2copy");
    						  while ($row5 = spip_fetch_array($result5)) {
    									 $surtitre = $row5['surtitre'];
    			 						 $titre = $row5['titre'];
    			 						 $soustitre = $row5['soustitre'];
    			 						 $descriptif = $row5['descriptif'];
    			 						 $nom_site = $row5['nom_site'];
    			 						 $chapo = $row5['chapo'];
    			 						 $texte = $row5['texte'];
    			 						 $ps = $row5['ps'];
            	 		 // création clone article
          						 spip_query("INSERT INTO $Tarticles (id_article, id_rubrique, id_secteur, date, statut, surtitre, titre, soustitre, descriptif, nom_site, chapo, texte, ps) 
            					 						  VALUES ('', '$id_rub_ec', '$num_cible', '$date_ec', '$statut_articles', '$surtitre', '$titre', '$soustitre', '$descriptif', '$nom_site', '$chapo', '$texte', '$ps')");
            					 if (mysql_error() != '') {
          								 $Terr_art[] = 'insertion article rubrique n°'.$id_rub_ec.' : erreur => '.mysql_error();
            					 }
          						 else {
              						 $id_art_ec = mysql_insert_id();
              						 spip_query("INSERT INTO $Tauteurs_articles (id_article, id_auteur) VALUES ($id_art_ec, $id_utilisateur)");
              			 				if (mysql_error() != '') {
              							 	 $Terr_art[] = 'insertion auteur_article rubrique n° '.$id_rub_ec.' : erreur => '.mysql_error();
              							}
          						 }
    						  }   // fin while article
						 
  		    	  }  // fin else création rubrique = OK
					 
				}   // fin for nb_copies
				
	  }  // fin if $_POST complet


// DEBUT FORMULAIRE DE LANCEMENT

	// trouver la version en cours à partir de plugin.xml
     $p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
     $pp = explode("/", end($p));
     define('_DIR_PLUGIN_GENER_CONT',(_DIR_PLUGINS.$pp[0]));
		
		 $Tlecture_fich_plugin = file(_DIR_PLUGIN_GENER_CONT.'/plugin.xml');
		 $stop_prochain = 0;
		 foreach ($Tlecture_fich_plugin as $ligne) {
						 if ($stop_prochain == 1) {
							  $version_script = $ligne;
							  break;
						 }
						 if (substr_count($ligne, '<version>') > 0) {
							  $stop_prochain = 1;
						 }
		 }
    debut_page('Moulinette &agrave; cloner les rubriques');
		
//		    debut_page(_T('titre_naviguer_dans_le_site')),"naviguer", "rubriques",'', '',$id_rubrique);
// print '<br>$_POST = ';		
// print_r($_POST);
// print '<br>$id_utilisateur = '.$id_utilisateur;
		 ?>
 
<style type="text/css">
			 .erreurs {
			 		 font-size: 12px;
					 color: #f00;			 			
			 }
			 .ok {
			 		 font-size: 18px;
					 color: #093;
			 }
</style>
<?
    
    debut_gauche();
    
    debut_boite_info();  
    echo "<img src=\""._DIR_PLUGIN_GENER_CONT."/img_pack/gener_cont-24.png\" style=\"float: left; margin-right: 10px;\">";
		echo "Cette moulinette clone une sous-rubrique de la rubrique des mod&egrave;les (".$num_source.") dans une sous-rubrique du secteur ".$num_cible.".";
		echo "<br /><br /><strong>Version : </strong>".$version_script;		
		echo '<br /><br /><a href=".?exec=cfg&cfg=gener_cont">Configuration de ce plugin</a>';
    fin_boite_info();
    
    debut_droite();
    gros_titre('Moulinette &agrave; cloner les rubriques');

		
    echo "\r\n<br />";  
    if (isset($_POST['num_parent']) AND $_POST['num_parent'] != '' AND isset($_POST['nb_copies']) AND $_POST['nb_copies'] != '') {
    	 debut_cadre_trait_couleur("rubrique-24.gif", false, "", "Choix de la rubrique parent et du nombre de rubriques");  ?>
			 <a href="<? print basename($_SERVER['PHP_SELF']); ?>?exec=gener_cont" style="float: right;">Retour cr&eacute;ation rubriques</a>
<?		 if (count($Terr_rub) > 0 OR count($Terr_art) > 0) {  ?>
			 <div id="erreurs" class="erreurs">
			 			Erreurs dans les rubriques :<br />
<?		     foreach ($Terr_rub as $err) { 
									 print $err.'<br /><br />';
				   }  	?>
					  <br />
						Erreurs dans les articles :<br />	
<?	       foreach ($Terr_art as $err) { 
									 print $err.'<br /><br />';
				   }  ?>
			 </div>
<?		 }
			 else { ?>			 			
			 <div id="ok" class="ok">
			 			Tout c'est bien pass&eacute;, <? print $nb_cree; ?> rubrique(s) cr&eacute;&eacute;(s).
			 </div>
<?   	 }  
			 fin_cadre_trait_couleur();
 	  }
		else {  ?>
<form method="post" action="<? print basename($_SERVER['PHP_SELF']); ?>?exec=gener_cont" id="form_saisie" name="form_saisie">
			
<?			 debut_cadre_trait_couleur("rubrique-24.gif", false, "", "Choix de la rubrique de destination et du nombre de copies");  ?>			
			 Rubrique o&ugrave; cr&eacute;er les clones : 
			 <select id="num_parent" name="num_parent">
<?		 	 if ($num_cible == 0) {
				 		$sql_where = 'id_parent = 0';
				 }
				 else {
				 			$sql_where = 'id_secteur = '.$num_cible;
				 }
				 $sql1 = "SELECT id_rubrique, titre FROM $Trubriques WHERE $sql_where";
				 $result1 = spip_query($sql1);
				 while ($row1 = spip_fetch_array($result1)) {   
				 			 echo "<option value=\"".$row1['id_rubrique']."\">".$row1['titre']."</option>";
			   }  ?>			 				 
			 </select>
			<br /><br />
			Nombre de sous-rubriques &agrave; cr&eacute;er : 
			<select id="nb_copies" name="nb_copies">
<?		   for ($i = 1; $i <= $max_copies; $i++) {   
				 		 echo "<option value=\"".$i."\">".$i."</option>";
			   }  ?>				 			     
			</select>
			<br />
<? 		   fin_cadre_trait_couleur();
				 debut_cadre_trait_couleur("rubrique-24.gif", false, "", "Param&eacute;trage de la rubrique &agrave; copier"); ?>			
			 Rubrique &agrave; copier : 
			 <select id="num_parent_2copy" name="num_parent_2copy">
<?		 	 $result2 = spip_query("SELECT id_rubrique, titre FROM $Trubriques WHERE id_parent = $num_source");   // AND id_rubrique != $num_source
				 while ($row2 = spip_fetch_array($result2)) {   
				 			 echo "<option value=\"".$row2['id_rubrique']."\">".$row2['titre']."</option>";
  			 }  ?>			 				 
			 </select>
<?  		 if ($connect_toutes_rubriques) {   // choix auteur uniquement pour les admins complets ?>			 
			 <br /><br />
			 Auteur des articles : 
			 <select id="auteur_copies" name="auteur_copies">
<?		      $result3 = spip_query("SELECT id_auteur, login FROM $Tauteurs");
						while ($row3 = spip_fetch_array($result3)) {
									echo "<option value=\"".$row3['id_auteur']."\">".$row3['login']."</option>";
						}   ?>
			 </select>
<? 		   } ?>			 
				 <br /><br />
				 Statut des articles :
      	 <select name="statut_articles">
      	 				 <option value="publie" <? echo ($statut_articles_defaut == "publie" ? 'selected="selected"' : ''); ?>>publi&#233; en ligne</option>
      					 <option value="prop" <? echo ($statut_articles_defaut == "prop" ? 'selected="selected"' : ''); ?>>propos&#233; &agrave; l'&#233;valuation</option>
      					 <option value="prepa" <? echo ($statut_articles_defaut == "prepa" ? 'selected="selected"' : ''); ?>>en cours de r&#233;daction</option>
      					 <option value="poubelle" <? echo ($statut_articles_defaut == "poubelle" ? 'selected="selected"' : ''); ?>>&agrave; la poubelle</option>
      					 <option value="refuse" <? echo ($statut_articles_defaut == "refuse" ? 'selected="selected"' : ''); ?>>refus&#233;</option>
      	 </select>
				 
<? 		   fin_cadre_trait_couleur();
				 debut_cadre_trait_couleur("rubrique-24.gif", false, "", "Création des titres de sous-rubriques :");			?>
				 champ 1 : <input type="text" id="champ_1_titre_rub" name="champ_1_titre_rub"> 
				 <br />champ 2 : <input type="text" id="champ_2_titre_rub" name="champ_2_titre_rub">
				 <br />champ 3 : <input type="text" id="champ_3_titre_rub" name="champ_3_titre_rub">
				 <br />
				 Num&eacute;rotation : incr&eacute;ment commence &agrave; : <input type="text" style="width: 30px;" id="debut_increment_titre_rub" name="debut_increment_titre_rub">
				 <br /><br />
				 <span style="font-size: 75%;">
				 Le titre de la rubrique sera constitu&eacute; par les champs 1 &agrave; 3. Chacun de ces champs peut &ecirc;tre rempli automatiquement par 
				 le contenu d'un champ de la rubrique &agrave; cloner (<strong>#TITRE</strong>, <strong>#DESCRIPTIF</strong> ou <strong>#TEXTE</strong>)
				 Pour qu'un champ soit rempli par un num&eacute;ro incr&eacute;ment&eacute; &agrave; chaque clone, mettre <strong>#INCREMENT</strong> dans le champ
				 <br /><br />Pour exemple : la formule 
				 <ul>
				 		<li>champ 1 : <strong>#TITRE</strong></li>
						<li>champ 2 : <strong>#INCREMENT</strong></li>
						<li>champ 3 : <strong>#DESCRIPTIF</strong></li>
						<li>increment à <strong>12</strong></li>
				 </ul>
				 donnera comme premier titre de la s&eacute;rie : 
				 <br /><strong>Partie II / Chapitre 12 :&lt;multi&gt;[fr] le titre du chapitre [en]chapter's title&lt;/multi&gt;</strong>
				 <br />si on clone une rubrique avec les caract&eacute;ristiques suivantes :
				 <ul>
				 		<li>titre : <strong>Partie II / Chapitre </strong></li>
						<li>descriptif : <strong> :&lt;multi&gt;[fr] le titre du chapitre [en]chapter's title &lt;/multi&gt;</strong></li>
				 </ul>
				 </span>
<? 		   fin_cadre_trait_couleur();   ?>
			<input type="submit" name="valide_generateur" id="valide_generateur" value="g&eacute;n&eacute;rer">

</form>
<?  }

		echo fin_page();
		
}
 ?>