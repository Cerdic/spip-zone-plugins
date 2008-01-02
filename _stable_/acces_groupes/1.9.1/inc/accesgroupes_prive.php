<?php
// toutes les fonctions exec_xxx de l'espace privé modifiées par accesgroupes

// http://doc.spip.org/@exec_articles_dist
function exec_articles() {
      	global $cherche_auteur, $ids, $cherche_mot,  $select_groupe, $debut, $id_article, $trad_err; 
      
      	global  $connect_id_auteur, $connect_statut, $options, $spip_display, $spip_lang_left, $spip_lang_right, $dir_lang;
      
      	$id_article= intval($id_article);
      
      	pipeline('exec_init',array('args'=>array('exec'=>'articles','id_article'=>$id_article),'data'=>''));
      
      	$row = spip_fetch_array(spip_query("SELECT * FROM spip_articles WHERE id_article=$id_article"));
      
      	if (!$row) {
      	   // cas du numero hors table
      		$titre = _T('public:aucun_article');
      		debut_page("&laquo; $titre &raquo;", "naviguer", "articles");
      		debut_grand_cadre();
      		fin_grand_cadre();
      		echo $titre;
      		exit;
      	}
      
      	$id_rubrique = $row['id_rubrique'];
      	$statut_article = $row['statut'];
      	$surtitre = $row["surtitre"];
      	$titre = sinon($row["titre"],_T('info_sans_titre'));
      	$soustitre = $row["soustitre"];
      	$descriptif = $row["descriptif"];
      	$nom_site = $row["nom_site"];
      	$url_site = $row["url_site"];
      	$chapo = $row["chapo"];
      	$texte = $row["texte"];
      	$ps = $row["ps"];
      	$date = $row["date"];
      	$maj = $row["maj"];
      	$date_redac = $row["date_redac"];
      	$visites = $row["visites"];
      	$referers = $row["referers"];
      	$extra = $row["extra"];
      	$id_trad = $row["id_trad"];
      	$id_version = $row["id_version"];
      	
      	$statut_rubrique = acces_rubrique($id_rubrique);
      
      	$flag_auteur = spip_num_rows(spip_query("SELECT id_auteur FROM spip_auteurs_articles WHERE id_article=$id_article AND id_auteur=$connect_id_auteur LIMIT 1"));
      
      	$flag_editable = ($statut_rubrique OR ($flag_auteur AND ($statut_article == 'prepa' OR $statut_article == 'prop' OR $statut_article == 'poubelle')));
      
      	debut_page("&laquo; $titre &raquo;", "naviguer", "articles", "", "", $id_rubrique);
      
      	debut_grand_cadre();
      
      	afficher_hierarchie($id_rubrique);
      
      	fin_grand_cadre();
      
      
      // MODIF GROUPEACCES : début du contrôle d'acces à la rubrique
          
          $acces = accesgroupes_verif_acces($id_rubrique, 'prive'); 
      //echo '<br>$acces = '.$acces;
      //echo '<br>accesgroupes_RubPrive(42, "prive") = '.accesgroupes_RubPrive(42, 'prive');		
          if ($acces == 1 || $acces == 2) { 
              accesgroupes_affichage_acces_restreint(); 
          } 
      		else { 
      
      // FIN de la premiere partie d'ACCESGROUPES 
      	
      	
      //
      // Affichage de la colonne de gauche
      //
      
      debut_gauche();
      
      boite_info_articles($id_article, $statut_article, $visites, $id_version);
      
      //
      // Logos de l'article
      //
      
        if ($flag_editable AND ($spip_display != 4)) {
      	  include_spip('inc/chercher_logo');
      	  echo afficher_boite_logo('id_article', $id_article,
      			      _T('logo_article').aide ("logoart"), _T('logo_survol'), 'articles');
        }
      
      // pour l'affichage du virtuel
      $virtuel = '';
      if (substr($chapo, 0, 1) == '=') {
      	$virtuel = substr($chapo, 1);
      }
      
      // Boites de configuration avancee
      
      if ($options == "avancees" && $connect_statut=='0minirezo' && $flag_editable)
        {
      	boites_de_config_articles($id_article);
       
      	boite_article_virtuel($id_article, $virtuel);
        }
      
      //
      // Articles dans la meme rubrique
      //
      
      meme_rubrique_articles($id_rubrique, $id_article, $options);
      
      echo pipeline('affiche_gauche',array('args'=>array('exec'=>'articles','id_article'=>$id_article),'data'=>''));
      
      //
      // Affichage de la colonne de droite
      //
      
      creer_colonne_droite();
       echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles','id_article'=>$id_article),'data'=>''));
      
      debut_droite();
      
      changer_typo('','article'.$id_article);
      
      debut_cadre_relief();
      
      //
      // Titre, surtitre, sous-titre
      //
      
      $modif = titres_articles($titre, $statut_article,$surtitre, $soustitre, $descriptif, $url_site, $nom_site, $flag_editable, $id_article, $id_rubrique);
      
      
       echo "<div class='serif' align='$spip_lang_left'>";
      
       debut_cadre_couleur();
       echo formulaire_dater($id_article, $flag_editable, $statut_article, $date, $date_redac);
       fin_cadre_couleur();
      
      //
      // Liste des auteurs de l'article
      //
      
       echo "\n<div id='editer_auteurs-$id_article'>";
       echo formulaire_editer_auteurs($cherche_auteur, $ids, $id_article,$flag_editable);
       echo "</div>";
      
      //
      // Liste des mots-cles de l'article
      //
      
      if ($options == 'avancees' AND $GLOBALS['meta']["articles_mots"] != 'non') {
        echo formulaire_mots('article', $id_article, $cherche_mot, $select_groupe, $flag_editable);
      }
      
      // Les langues
      
        if (($GLOBALS['meta']['multi_articles'] == 'oui')
      	OR (($GLOBALS['meta']['multi_rubriques'] == 'oui') AND ($GLOBALS['meta']['gerer_trad'] == 'oui'))) {
      
          echo formulaire_referencer_traduction($id_article, $id_rubrique, $id_trad,  $flag_editable, $trad_err);
        }
      
       echo pipeline('affiche_milieu',array('args'=>array('exec'=>'articles','id_article'=>$id_article),'data'=>''));
      
       if ($statut_rubrique)
         echo debut_cadre_relief('', true),
           "\n<div id='instituer_article-$id_article'>",     
           formulaire_instituer_article($id_article, $statut_article, 'articles', "id_article=$id_article"),
           '</div>',
           fin_cadre_relief('', true);
      
       afficher_corps_articles($virtuel, $chapo, $texte, $ps, $extra);
      
       if ($flag_editable) {
      	echo "\n<div align='$spip_lang_right'><br />";
      	bouton_modifier_articles($id_article, $id_rubrique, $modif,_T('texte_travail_article', $modif), "warning-24.gif", "");
      	echo "</div>";
      }
      
      //
      // Documents associes a l'article
      //
      
       if ($spip_display != 4)
       afficher_documents_et_portfolio($id_article, "article", $flag_editable);
      
       if ($flag_auteur AND  $statut_article == 'prepa' AND !$statut_rubrique)
      	echo demande_publication($id_article);
      
       echo "</div>";
       echo "</div>";
       fin_cadre_relief();
      
        echo "<br /><br />";
        
        $tm = rawurlencode($titre);
        echo "\n<div align='center'>";
        icone(_T('icone_poster_message'), generer_url_ecrire("forum_envoi","statut=prive&id_article=$id_article&titre_message=$tm&url=" . generer_url_retour("articles","id_article=$id_article")), "forum-interne-24.gif", "creer.gif");
        echo "</div><br />";
        
        echo exec_discuter_dist($id_article, $debut);
      
      
      // ACCESGROUPES : à placer avant fin_page(); 
      	 		} 
      // FIN ACCESGROUPES
      
      	
      	
        fin_page();
      
}
      

// http://doc.spip.org/@exec_articles_edit_dist
function exec_articles_edit()
{
	$id_article = _request('id_article');
	$id_rubrique = _request('id_rubrique');
	$lier_trad = intval(_request('lier_trad'));
	$new = _request('new');

	pipeline('exec_init',array('args'=>array('exec'=>'articles_edit','id_article'=>$id_article),'data'=>''));
	
	$row = article_select($id_article, $id_rubrique, $lier_trad, $new);
	if (!$row) die ("<h3>"._T('info_acces_interdit')."</h3>");

	$id_article = $row['id_article'];

	// si une ancienne revision est demandee, la charger
	// en lieu et place de l'actuelle ; attention les champs
	// qui etaient vides ne sont pas vide's. Ca permet de conserver
	// des complements ajoutes "orthogonalement", et ca fait un code
	// plus generique.
	if ($id_version = intval(_request('id_version'))) {
		include_spip('inc/revisions');
		if ($textes = recuperer_version($id_article, $id_version)) {
			foreach ($textes as $champ => $contenu)
				$row[$champ] = $contenu;
		}
	}

	$id_rubrique = $row['id_rubrique'];
	$titre = $row['titre'];

	if ($id_version) $titre.= ' ('._T('version')." $id_version)";

	debut_page(_T('titre_page_articles_edit', array('titre' => $titre)),
			"naviguer", "articles", "hauteurTextarea();", 
			"",
			$id_rubrique);

	debut_grand_cadre();
	afficher_hierarchie($id_rubrique);
	fin_grand_cadre();


// MODIF GROUPEACCES : début du contrôle d'acces à la rubrique
    
    $acces = accesgroupes_verif_acces($id_rubrique, 'prive'); 
//echo '<br>$acces = '.$acces;
//echo '<br>accesgroupes_RubPrive(42, "prive") = '.accesgroupes_RubPrive(42, 'prive');		
    if ($acces == 1 || $acces == 2) { 
        accesgroupes_affichage_acces_restreint(); 
    } 
		else { 

// FIN de la premiere partie d'ACCESGROUPES 
	
	
	debut_gauche();

	// Pave "documents associes a l'article"

	if (!$new){

		# affichage sur le cote des pieces jointes, en reperant les inserees
		# note : traiter_modeles($texte, true) repere les doublons
		# aussi efficacement que propre(), mais beaucoup plus rapidement
		traiter_modeles(join('',$row), true);
		afficher_documents_colonne($id_article, 'article', true);
	}
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'articles_edit','id_article'=>$id_article),'data'=>''));
	creer_colonne_droite();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles_edit','id_article'=>$id_article),'data'=>''));
	debut_droite();
	
	debut_cadre_formulaire();
	echo formulaire_articles_edit($row, $lier_trad, $new, $GLOBALS['meta']);
	fin_cadre_formulaire();

// ACCESGROUPES : à placer avant fin_page(); 
	 		} 
// FIN ACCESGROUPES
		
	fin_page();
}


// http://doc.spip.org/@exec_articles_versions_dist
function exec_articles_versions()
{
  global
    $champs_extra,
    $chapo,
    $connect_id_auteur,
    $descriptif,
    $dir_lang,
    $id_article,
    $id_diff,
    $id_version,
    $les_notes,
    $nom_site,
    $options,
    $ps,
    $soustitre,
    $surtitre,
    $texte,
    $titre,
    $url_site;


//
// Lire l'article
//

    $id_article = intval($id_article);
    $result = spip_query("SELECT * FROM spip_articles WHERE id_article='$id_article'");

if ($row = spip_fetch_array($result)) {
	$id_article = $row["id_article"];
	$id_rubrique = $row["id_rubrique"];
	$titre = $row["titre"];
	$date = $row["date"];
	$statut_article = $row["statut"];
	$maj = $row["maj"];
	$date_redac = $row["date_redac"];
	$visites = $row["visites"];
	$referers = $row["referers"];
	$extra = $row["extra"];
	$id_trad = $row["id_trad"];
}

if (!($id_version = intval($id_version))) {
	$id_version = $row['id_version'];
}
$textes = recuperer_version($id_article, $id_version);

$id_diff = intval($id_diff);
if (!$id_diff) {
	$diff_auto = true;
	$row = spip_fetch_array(spip_query("SELECT id_version FROM spip_versions WHERE id_article=$id_article AND id_version<$id_version ORDER BY id_version DESC LIMIT 1"));
	if ($row) $id_diff = $row['id_version'];
}

//
// Calculer le diff
//

if ($id_version && $id_diff) {
	include_spip('inc/diff');

	if ($id_diff > $id_version) {
		$t = $id_version;
		$id_version = $id_diff;
		$id_diff = $t;
		$old = $textes;
		$new = $textes = recuperer_version($id_article, $id_version);
	}
	else {
		$old = recuperer_version($id_article, $id_diff);
		$new = $textes;
	}

	$textes = array();
	$champs = array('surtitre', 'titre', 'soustitre', 'descriptif', 'nom_site', 'url_site', 'chapo', 'texte', 'ps');
	
	foreach ($champs as $champ) {
		if (!$new[$champ] && !$old[$champ]) continue;

		$diff = new Diff(new DiffTexte);
		$textes[$champ] = afficher_diff($diff->comparer(preparer_diff($new[$champ]), preparer_diff($old[$champ])));
	}
}

if (is_array($textes))
foreach ($textes as $var => $t) $$var = $t;



debut_page(_T('info_historique')." &laquo; $titre &raquo;", "naviguer", "articles", "", "", $id_rubrique);

debut_grand_cadre();

afficher_hierarchie($id_rubrique);

fin_grand_cadre();

      
      
// MODIF GROUPEACCES : début du contrôle d'acces à la rubrique
    
    $acces = accesgroupes_verif_acces($id_rubrique, 'prive'); 
//echo '<br>$acces = '.$acces;
//echo '<br>accesgroupes_RubPrive(42, "prive") = '.accesgroupes_RubPrive(42, 'prive');		
    if ($acces == 1 || $acces == 2) { 
        accesgroupes_affichage_acces_restreint(); 
    } 
		else { 

// FIN de la premiere partie d'ACCESGROUPES 
      	
//////////////////////////////////////////////////////
// Affichage de la colonne de gauche
//

debut_gauche();


debut_raccourcis();
icone_horizontale(_T('icone_retour_article'), generer_url_ecrire("articles","id_article=$id_article"), "article-24.gif","rien.gif");
icone_horizontale(_T('icone_suivi_revisions'), generer_url_ecrire("suivi_revisions",""), "historique-24.gif","rien.gif");
fin_raccourcis();


//////////////////////////////////////////////////////
// Affichage de la colonne de droite
//

debut_droite();

changer_typo('','article'.$id_article);

echo "<a name='diff'></a>\n";

debut_cadre_relief();

//
// Titre, surtitre, sous-titre
//

if ($statut_article=='publie') {
	$logo_statut = "puce-verte.gif";
}
else if ($statut_article=='prepa') {
	$logo_statut = "puce-blanche.gif";
}
else if ($statut_article=='prop') {
	$logo_statut = "puce-orange.gif";
}
else if ($statut_article == 'refuse') {
	$logo_statut = "puce-rouge.gif";
}
else if ($statut_article == 'poubelle') {
	$logo_statut = "puce-poubelle.gif";
}


echo "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
echo "<tr width='100%'><td width='100%' valign='top'>";
if ($surtitre) {
	echo "<span $dir_lang><font face='arial,helvetica' size='3'><b>";
	echo typo($surtitre);
	echo "</b></font></span>\n";
}
gros_titre($titre, $logo_statut);

if ($soustitre) {
	echo "<span $dir_lang><font face='arial,helvetica' size='3'><b>";
	echo typo($soustitre);
	echo "</b></font></span>\n";
}


if ($descriptif OR $url_site OR $nom_site) {
	echo "<p><div align='left' style='padding: 5px; border: 1px dashed #aaaaaa; background-color: #e4e4e4;' $dir_lang>";
	echo "<font size='2' face='Verdana,Arial,Sans,sans-serif'>";
	$texte_case = ($descriptif) ? "{{"._T('info_descriptif')."}} $descriptif\n\n" : '';
	$texte_case .= ($nom_site.$url_site) ? "{{"._T('info_urlref')."}} [".$nom_site."->".$url_site."]" : '';
	echo propre($texte_case);
	echo "</font>";
	echo "</div>";
}

echo "</td>";

echo "<td align='center'>";

// L'article est-il editable ?
 $result_auteur = spip_query("SELECT * FROM spip_auteurs_articles WHERE id_article=$id_article AND id_auteur=$connect_id_auteur");

$flag_auteur = (spip_num_rows($result_auteur) > 0);
$flag_editable = (acces_rubrique($id_rubrique)
	OR ($flag_auteur AND ($statut_article == 'prepa' OR $statut_article == 'prop' OR $statut_article == 'poubelle')));

if ($flag_editable)
	icone(_T('icone_modifier_article').'<br />('._T('version')." $id_version)", generer_url_ecrire("articles_edit","id_article=$id_article&id_version=$id_version"), "article-24.gif", "edit.gif");

echo "</td>";

echo "</tr></table>";


//////////////////////////////////////////////////////
// Affichage des versions
//

debut_cadre_relief();

$result = spip_query("SELECT id_version, titre_version, date, id_auteur	FROM spip_versions WHERE id_article=$id_article ORDER BY id_version DESC");

echo "<ul class='verdana3'>";
while ($row = spip_fetch_array($result)) {
	echo "<li>\n";
	$date = affdate_heure($row['date']);
	$version_aff = $row['id_version'];
	$titre_version = typo($row['titre_version']);
	$titre_aff = $titre_version ? $titre_version : $date;
	if ($version_aff != $id_version) {
		$lien = parametre_url(self(), 'id_version', $version_aff);
		$lien = parametre_url($lien, 'id_diff', '');
		echo "<a href='".($lien.'#diff')."' title=\""._T('info_historique_affiche')."\">$titre_aff</a>";
	}
	else {
		echo "<b>$titre_aff</b>";
	}

	if ($row['id_auteur']) {
		$t = spip_fetch_array(spip_query("SELECT nom FROM spip_auteurs WHERE id_auteur=".$row['id_auteur']));
		echo " (".typo($t['nom']).")";
	}

	if ($version_aff != $id_version) {
		echo " <span class='verdana2'>";
		if ($version_aff == $id_diff) {
			echo "<b>("._T('info_historique_comparaison').")</b>";
		}
		else {
			$lien = parametre_url(self(), 'id_version', $id_version);
			$lien = parametre_url($lien, 'id_diff', $version_aff);
			echo "(<a href='".($lien.'#diff').
			"'>"._T('info_historique_comparaison')."</a>)";
		}
		echo "</span>";
	}
	echo "</li>\n";
}
echo "</ul>\n";

fin_cadre_relief();


//////////////////////////////////////////////////////
// Corps de la version affichee
//

if ($id_version) {
	echo "\n\n<div align='justify'>";

	// pour l'affichage du virtuel

	if (substr($chapo, 0, 1) == '=') {
		$virtuel = substr($chapo, 1);
	}
	
	if ($virtuel) {
		debut_boite_info();
		echo _T('info_renvoi_article')." ".propre("<center>[->$virtuel]</center>");
		fin_boite_info();
	}
	else {
		echo "<div $dir_lang><b>";
		$revision_nbsp = ($options == "avancees");	// a regler pour relecture des nbsp dans les articles
		echo justifier(propre_diff($chapo));
		echo "</b></div>\n\n";
	
		echo "<div $dir_lang>";
		echo justifier(propre_diff($texte));
		echo "</div>";
	
		if ($ps) {
			echo debut_cadre_enfonce();
			echo "<div $dir_lang><font size='2' face='Verdana,Arial,Sans,sans-serif'>";
			echo justifier("<b>"._T('info_ps')."</b> ".propre_diff($ps));
			echo "</font></div>";
			echo fin_cadre_enfonce();
		}
		$revision_nbsp = false;
	
		if ($les_notes) {
			echo debut_cadre_relief();
			echo "<div $dir_lang><font size='2'>";
			echo justifier("<b>"._T('info_notes')."&nbsp;:</b> ".$les_notes);
			echo "</font></div>";
			echo fin_cadre_relief();
		}
	
		if ($champs_extra AND $extra) {
			include_spip('inc/extra');
			extra_affichage($extra, "articles");
		}
	}
}

fin_cadre_relief();


// ACCESGROUPES : à placer avant fin_page(); 
	 		} 
// FIN ACCESGROUPES
		

fin_page();
}



// http://doc.spip.org/@exec_breves_edit_dist
function exec_breves_edit()
{
global
  $champs_extra,
  $connect_statut,
  $id_breve,
  $id_rubrique,
  $lien_titre,
  $lien_url,
  $new,
  $spip_ecran,
  $texte;

$id_breve = intval($id_breve);

if ($new != "oui") {
	$result = spip_query("SELECT * FROM spip_breves WHERE id_breve=$id_breve");

	
	if ($row=spip_fetch_array($result)) {
		$id_breve=$row['id_breve'];
		$titre=$row['titre'];
		$texte=$row['texte'];
		$lien_titre=$row['lien_titre'];
		$lien_url=$row['lien_url'];
		$statut=$row['statut'];
		$id_rubrique=$row['id_rubrique'];
		$extra = $row['extra'];
	}
}
else {
	$titre = filtrer_entites(_T('titre_nouvelle_breve'));
	$texte = "";
	$onfocus = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
	$lien_titre='';
	$lien_url='';
	$statut = "prop";
	$row = spip_fetch_array(spip_query("SELECT id_secteur FROM spip_rubriques WHERE id_rubrique = ".intval($id_rubrique)));
	$id_rubrique = $row['id_secteur'];
}

pipeline('exec_init',array('args'=>array('exec'=>'breves_edit','id_breve'=>$id_breve),'data'=>''));

debut_page(_T('titre_page_breves_edit', array('titre' => $titre)), "naviguer", "breves", "", "", $id_rubrique);


debut_grand_cadre();

afficher_hierarchie($id_rubrique);

fin_grand_cadre();


// MODIF GROUPEACCES : début du contrôle d'acces à la rubrique
    
    $acces = accesgroupes_verif_acces($id_rubrique, 'prive'); 
//echo '<br>$acces = '.$acces;
//echo '<br>accesgroupes_RubPrive(42, "prive") = '.accesgroupes_RubPrive(42, 'prive');		
    if ($acces == 1 || $acces == 2) { 
        accesgroupes_affichage_acces_restreint(); 
    } 
		else { 

// FIN de la premiere partie d'ACCESGROUPES 


debut_gauche();
if ($new != 'oui' AND ($connect_statut=="0minirezo" OR $statut=="prop")) {
	# affichage sur le cote des images, en reperant les inserees
	# note : traiter_modeles($texte, true) repere les doublons
	# aussi efficacement que propre(), mais beaucoup plus rapidement
	traiter_modeles("$titre$texte", true);
	afficher_documents_colonne($id_breve, "breve", true);
}
echo pipeline('affiche_gauche',array('args'=>array('exec'=>'breves_edit','id_breve'=>$id_breve),'data'=>''));
creer_colonne_droite();
echo pipeline('affiche_droite',array('args'=>array('exec'=>'breves_edit','id_breve'=>$id_breve),'data'=>''));
debut_droite();
debut_cadre_formulaire();


if ($new != "oui") {
	echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
	echo "<tr width='100%'>";
	echo "<td>";
		icone(_T('icone_retour'), generer_url_ecrire("breves_voir","id_breve=$id_breve"), "breve-24.gif", "rien.gif");
	
	echo "</td>";
	echo "<td>", http_img_pack("rien.gif", ' ', "width='10'"), "</td>\n";
	echo "<td width='100%'>";
	echo _T('info_modifier_breve');
	gros_titre($titre);
	echo "</td></tr></table>";
	echo "<p>";
}


if ($connect_statut=="0minirezo" OR $statut=="prop" OR $new == "oui") {
	if ($id_breve) $lien = "id_breve=$id_breve";
	echo generer_url_post_ecrire('breves_voir',$lien, 'formulaire');

	if ($new == "oui") echo "<INPUT TYPE='Hidden' NAME='new' VALUE=\"oui\">";

	$titre = entites_html($titre);
	$lien_titre = entites_html($lien_titre);

	echo _T('entree_titre_obligatoire');
	echo "<INPUT TYPE='text' CLASS='formo' NAME='titre' VALUE=\"$titre\" SIZE='40' $onfocus>";


	/// Dans la rubrique....
	echo "<INPUT TYPE='Hidden' NAME='id_rubrique_old' VALUE=\"$id_rubrique\"><p />";

	if ($id_rubrique == 0) $logo_parent = "racine-site-24.gif";
	else {
		$result=spip_query("SELECT id_parent FROM spip_rubriques WHERE id_rubrique='$id_rubrique'");

		while($row=spip_fetch_array($result)){
			$parent_parent=$row['id_parent'];
		}
		if ($parent_parent == 0) $logo_parent = "secteur-24.gif";
		else $logo_parent = "rubrique-24.gif";
	}


	debut_cadre_couleur("$logo_parent", false, "",_T('entree_interieur_rubrique').aide ("brevesrub"));

	// selecteur de rubrique (en general pas d'ajax car toujours racine)
	$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
	echo $selecteur_rubrique($id_rubrique, 'breve', ($statut == 'publie'));

	fin_cadre_couleur();
	
	if ($spip_ecran == "large") $rows = 28;
	else $rows = 15;
	
	echo "<p /><B>"._T('entree_texte_breve')."</B><BR>";
	echo afficher_barre('document.formulaire.texte');
	echo "<TEXTAREA NAME='texte' ".$GLOBALS['browser_caret']." ROWS='$rows' CLASS='formo' COLS='40' wrap=soft>";
	echo entites_html($texte);
	echo "</TEXTAREA><P>\n";


	echo _T('entree_liens_sites').aide ("breveslien")."<BR>";
	echo _T('info_titre')."<BR>";
	echo "<INPUT TYPE='text' CLASS='forml' NAME='lien_titre' VALUE=\"$lien_titre\" SIZE='40'><BR>";

	echo _T('info_url')."<BR>";
	echo "<INPUT TYPE='text' CLASS='forml' NAME='lien_url' VALUE=\"$lien_url\" SIZE='40'><P>";

	if ($champs_extra) {
		include_spip('inc/extra');
		extra_saisie($extra, 'breves', $id_rubrique);
	}

	if ($connect_statut=="0minirezo" AND acces_rubrique($id_rubrique)) {
		debut_cadre_relief();
		echo "<B>"._T('entree_breve_publiee')."</B>\n";

		echo "<SELECT NAME='statut' SIZE=1 CLASS='fondl'>\n";
		
		echo "<OPTION".mySel("prop",$statut)." style='background-color: white'>"._T('item_breve_proposee')."\n";		
		echo "<OPTION".mySel("refuse",$statut). http_style_background('rayures-sup.gif'). ">"._T('item_breve_refusee')."\n";		
		echo "<OPTION".mySel("publie",$statut)." style='background-color: #B4E8C5'>"._T('item_breve_validee')."\n";		

		echo "</SELECT>".aide ("brevesstatut")."<P>\n";
		fin_cadre_relief();
	}
	else {
		echo "<INPUT TYPE='Hidden' NAME='statut' VALUE=\"$statut\">";
	}
	echo "<P ALIGN='right'><INPUT TYPE='submit' NAME='Valider' VALUE='"._T('bouton_enregistrer')."' CLASS='fondo'  >";
	echo "</FORM>";
}
else echo "<H2>"._T('info_page_interdite')."</H2>";

fin_cadre_formulaire();

// ACCESGROUPES : à placer avant fin_page(); 
	 		} 
// FIN ACCESGROUPES

fin_page();
}


// http://doc.spip.org/@exec_rubriques_edit_dist
function exec_rubriques_edit()
{
  global
    $champs_extra,
    $connect_statut,
    $id_parent,
    $id_rubrique,
    $new,
    $options;

if ($new == "oui") {
	if (($connect_statut=='0minirezo') AND acces_rubrique($id_parent)) {
		$id_parent = intval($id_parent);
		$id_rubrique = 0;
		$titre = filtrer_entites(_T('titre_nouvelle_rubrique'));
		$onfocus = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		$descriptif = "";
		$texte = "";
	}
	else {
		echo _T('avis_acces_interdit');
		exit;
	}
}
else {
	$id_rubrique = intval($id_rubrique);
	$result = spip_query("SELECT * FROM spip_rubriques WHERE id_rubrique='$id_rubrique'");
	while ($row = spip_fetch_array($result)) {
		$id_parent = $row['id_parent'];
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$texte = $row['texte'];
		$id_secteur = $row['id_secteur'];
		$extra = $row["extra"];
	}
}

 pipeline('exec_init',array('args'=>array('exec'=>'rubriques_edit','id_rubrique'=>$id_rubrique),'data'=>''));
 debut_page(_T('info_modifier_titre', array('titre' => $titre)), "naviguer", "rubriques", '', '', $id_rubrique);

if ($id_parent == 0) $ze_logo = "secteur-24.gif";
else $ze_logo = "rubrique-24.gif";

if ($id_parent == 0) $logo_parent = "racine-site-24.gif";
else {
	$id_secteur = spip_fetch_array(spip_query("SELECT id_secteur FROM spip_rubriques WHERE id_rubrique='$id_parent'"));
	$id_secteur = $id_secteur['id_secteur'];
	if ($id_parent_== $id_secteur)
		$logo_parent = "secteur-24.gif";
	else	$logo_parent = "rubrique-24.gif";
}


debut_grand_cadre();

afficher_hierarchie($id_parent);

fin_grand_cadre();


// MODIF GROUPEACCES : début du contrôle d'acces à la rubrique
    
    $acces = accesgroupes_verif_acces($id_rubrique, 'prive'); 
//echo '<br>$acces = '.$acces;
//echo '<br>accesgroupes_RubPrive(42, "prive") = '.accesgroupes_RubPrive(42, 'prive');		
    if ($acces == 1 || $acces == 2) { 
        accesgroupes_affichage_acces_restreint(); 
    } 
		else { 

// FIN de la premiere partie d'ACCESGROUPES 


debut_gauche();
//////// parents



echo pipeline('affiche_gauche',array('args'=>array('exec'=>'rubriques_edit','id_rubrique'=>$id_rubrique),'data'=>''));
creer_colonne_droite();
echo pipeline('affiche_droite',array('args'=>array('exec'=>'rubriques_edit','id_rubrique'=>$id_rubrique),'data'=>''));	  
debut_droite();

debut_cadre_formulaire();

echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
echo "<tr width='100%'>";
echo "<td>";

if ($id_rubrique) icone(_T('icone_retour'), generer_url_ecrire("naviguer","id_rubrique=$id_rubrique"), $ze_logo, "rien.gif");
else icone(_T('icone_retour'), generer_url_ecrire("naviguer","id_rubrique=$id_parent"), $ze_logo, "rien.gif");

echo "</td>";
echo "<td>". http_img_pack('rien.gif', " ", "width='10'") . "</td>\n";
echo "<td width='100%'>";
echo _T('info_modifier_rubrique');
gros_titre($titre);
echo "</td></tr></table>";
echo "<p>";

echo  generer_url_post_ecrire("naviguer",($id_rubrique ? "id_rubrique=$id_rubrique" : ""));

$titre = entites_html($titre);

echo _T('entree_titre_obligatoire');
echo "<INPUT TYPE='text' CLASS='formo' NAME='titre' VALUE=\"$titre\" SIZE='40' $onfocus><P>";


debut_cadre_couleur("$logo_parent", false, '', _T('entree_interieur_rubrique').aide ("rubrub"));

// selecteur de rubriques
$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
$restreint = ($GLOBALS['statut'] == 'publie');
echo $selecteur_rubrique($id_parent, 'rubrique', $restreint, $id_rubrique);


// si c'est une rubrique-secteur contenant des breves, demander la
// confirmation du deplacement
 $contient_breves = spip_fetch_array(spip_query("SELECT COUNT(*) AS cnt FROM spip_breves WHERE id_rubrique='$id_rubrique' LIMIT 1"));
 $contient_breves = $contient_breves['cnt'];

if ($contient_breves > 0) {
	$scb = ($contient_breves>1? 's':'');
	echo "<div><font size='2'><input type='checkbox' name='confirme_deplace'
	value='oui' id='confirme-deplace'
	><label for='confirme-deplace'>&nbsp;"
	._T('avis_deplacement_rubrique',
		array('contient_breves' => $contient_breves,
			'scb' => $scb))
	."</font></label></div>\n";
} else
	echo "<input type='hidden' name='confirme_deplace' value='oui' />\n";


fin_cadre_couleur();

echo "<P>";


if ($options == "avancees" OR $descriptif) {
	echo "<B>"._T('texte_descriptif_rapide')."</B><BR>";
	echo _T('entree_contenu_rubrique')."<BR>";
	echo "<TEXTAREA NAME='descriptif' CLASS='forml' ROWS='4' COLS='40' wrap=soft>";
	echo entites_html($descriptif);
	echo "</TEXTAREA><P>\n";
}
else {
	echo "<INPUT TYPE='Hidden' NAME='descriptif' VALUE=\"".entites_html($descriptif)."\" />";
}

echo "<B>"._T('info_texte_explicatif')."</B>";
echo aide ("raccourcis");
echo "<BR><TEXTAREA NAME='texte' ROWS='15' CLASS='formo' COLS='40' wrap=soft>";
echo entites_html($texte);
echo "</TEXTAREA>\n";

	if ($champs_extra) {
		include_spip('inc/extra');
		extra_saisie($extra, 'rubriques', $id_secteur);
	}

echo "<input type='hidden' name='new' value='",
	  (($new == "oui") ? 'oui' : 'non'),
	  "' />";

echo "\n<p align='right'><input type='submit' value='"._T('bouton_enregistrer')."' CLASS='fondo' />\n</p></form>";

fin_cadre_formulaire();


// ACCESGROUPES : à placer avant fin_page(); 
	 		} 
// FIN ACCESGROUPES


fin_page();
}


// http://doc.spip.org/@exec_naviguer_dist
function exec_naviguer()
{
	global $new, $id_parent, $id_rubrique, $spip_display,  $connect_statut, $champs_extra, $cherche_mot,  $select_groupe, $descriptif, $texte, $titre;


	$flag_editable = ($connect_statut == '0minirezo' AND (acces_rubrique($id_parent) OR acces_rubrique($id_rubrique))); // id_parent necessaire en cas de creation de sous-rubrique

	$id_rubrique = intval($id_rubrique);
	$id_parent = intval($id_parent);
	if ($id_parent == $id_rubrique && $id_parent) exit;
	if ($flag_editable AND $new) {
		if ($new == 'oui')
			$id_rubrique = enregistre_creer_naviguer($id_parent);
		enregistre_modifier_naviguer($id_rubrique,
					     $id_parent,
					     $titre,
					     $texte,
					     $descriptif
					     );

		calculer_rubriques();
		calculer_langues_rubriques();

			// invalider les caches marques de cette rubrique
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_rubrique/$id_rubrique'");

		// pour avoir id_rubrique dans l'URL
		if ($new == 'oui') {
			redirige_par_entete(generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique, true));
		} 
	}

//
// recuperer les infos sur cette rubrique
//

	$row=spip_fetch_array(spip_query("SELECT * FROM spip_rubriques WHERE id_rubrique='$id_rubrique'"));
	if ($row) {
		$id_parent=$row['id_parent'];
		$titre=$row['titre'];
		$descriptif=$row['descriptif'];
		$texte=$row['texte'];
		$statut = $row['statut'];
		$extra = $row["extra"];
	} else $statut = $titre = $descriptif = $texte = $extra = '';

	if ($id_rubrique ==  0) $ze_logo = "racine-site-24.gif";
	else if ($id_parent == 0) $ze_logo = "secteur-24.gif";
	else $ze_logo = "rubrique-24.gif";

///// debut de la page

	pipeline('exec_init',array('args'=>array('exec'=>'naviguer','id_rubrique'=>$id_rubrique),'data'=>''));

	debut_page(($titre ? ("&laquo; ".textebrut(typo($titre))." &raquo;") :
		    _T('titre_naviguer_dans_le_site')),
		   "naviguer",
		   "rubriques",
		   '',
		   '',
		   $id_rubrique);

//////// parents

	  debut_grand_cadre();

	  if ($id_rubrique  > 0) afficher_hierarchie($id_parent);
	  else $titre = _T('info_racine_site').": ". $GLOBALS['meta']["nom_site"];
	  fin_grand_cadre();

// MODIF ACCESGROUPES : début du contrôle d'acces à la rubrique
    
    $acces = accesgroupes_verif_acces($id_rubrique, 'prive'); 
//echo '<br>$acces = '.$acces;
//echo '<br>accesgroupes_RubPrive(42, "prive") = '.accesgroupes_RubPrive(42, 'prive');		
    if ($acces == 1 || $acces == 2) { 
			 accesgroupes_affichage_acces_restreint();
    } 
		else { 

// FIN de la premiere partie d'ACCESGROUPES 
		
	  changer_typo('', 'rubrique'.$id_rubrique);

	  debut_gauche();

	  if ($spip_display != 4) {

		infos_naviguer($id_rubrique, $statut);

//
// Logos de la rubrique
//
		if ($flag_editable AND ($spip_display != 4)) {
			include_spip('inc/chercher_logo');
			echo afficher_boite_logo('id_rubrique', $id_rubrique, ($id_rubrique ? _T('logo_rubrique') : _T('logo_standard_rubrique'))." ".aide ("rublogo"), _T('logo_survol'), 'naviguer');
		}

//
// Afficher les boutons de creation d'article et de breve
//
		raccourcis_naviguer($id_rubrique, $id_parent);
	  }
		
		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'naviguer','id_rubrique'=>$id_rubrique),'data'=>''));
		creer_colonne_droite();
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'naviguer','id_rubrique'=>$id_rubrique),'data'=>''));	  
		debut_droite();

	  debut_cadre_relief($ze_logo);

	  montre_naviguer($id_rubrique, $titre, $descriptif, $ze_logo, $flag_editable);

	  if ($champs_extra AND $extra) {
		include_spip('inc/extra');
		extra_affichage($extra, "rubriques");
	  }

/// Mots-cles
	    if ($GLOBALS['meta']["articles_mots"] != 'non' AND $id_rubrique > 0) {
	      echo "\n<p>",
		formulaire_mots('rubrique', $id_rubrique,  $cherche_mot,  $select_groupe, $flag_editable);
	    }


	    if (strlen($texte) > 1) {
	      echo "\n<p><div align='justify'><font size=3 face='Verdana,Arial,Sans,sans-serif'>", justifier(propre($texte)), "&nbsp;</font></div>";
	    }


//
// Langue de la rubrique
//

	    langue_naviguer($id_rubrique, $id_parent, $flag_editable);
	    
	    fin_cadre_relief();


//
// Gerer les modifications...
//

	    contenu_naviguer($id_rubrique, $id_parent, $ze_logo, $flag_editable);

// ACCESGROUPES : à placer avant fin_page(); 
	 		} 
// FIN ACCESGROUPES
			
	    fin_page();
}


?>