<?php

/*	Fonction generale d'affichage du contenu des objets, 	*/
/*	dans l'espace prive.					*/
/*	Elle s'affiche lors de l'appel de la page 		*/
/*	'exec/actualites_voir&type=truc&id=1', en recuperant	*/	
/*	donc deux parametres passes dans l'url, depuis la	*/
/* 	page 'actualites_tous'.					*/


if (!defined("_ECRIRE_INC_VERSION")) return;

// Chargement prealable des fonctions exterieures
// suivantes (du core) dont on va avoir besoin pour la suite
include_spip('inc/presentation');
include_spip('inc/actions');

global $logo_libelles;
$GLOBALS['logo_libelles']['id_actualite'] = "LOGO ACTUALITE";

function exec_actualites_voir(){

// 1/4 - Les autorisations
	// Avant meme de commencer, on verifie qu'on a bien
	// l'autorisation de voir cette page.
	if (!autoriser('voir', 'actualites_voir')) {
		// Si on est pas autorise,
		include_spip('inc/minipres');
		// on renvoie un message d'erreur,
		echo minipres();
		// et on quitte.
		exit;
	}


// 2/4 - Le contexte
	// Premiere chose, il s'agit de recuperer le contexte.
	// Pour le type, plus complique : un regex.
	// On analyse l'URL complete (_SERVER["REQUEST_URI"]),
	// pour trouver une chaine de caracteres quelconque qui 
	// soit entouree par 'id_' d'un cote et '=' de l'autre
	// (motif : /id_(.*)=/U). On souhaite la deuxieme colonne
	// (la n°1) du tableau des resultats (cf. doc sur preg_match).	
	preg_match("/id_(.*)=/U",$_SERVER["REQUEST_URI"],$matches);
	$type_objet = $matches[1];
	// Pour l'id, il s'agit d'un appel simple aux parametres
	// fournis par l'URL
	$id_objet = intval(_request('id_'.$type_objet));


	// Ceci nous permet de connaitre le nom de la table dans
	// lequel se trouve l'objet 
	$table_objet = "spip_".$type_objet."s";

	// et nous permet d'aller chercher en base les infos 
	// qui nous manquent. On extrait chaque champ, et on met 
	// le tout dans le tableau $row.
	$row = sql_fetsel("*", $table_objet, "id_$type_objet=$id_objet");

	// On extrait quelques champs du tableau $row, pour en faire 
	// autant de variables. Ceci pour plus de praticite dans 
	// les manipulation qui vont suivre.
	// Le titre pour l'entete <head> de la page
	$titre = $row['titre'];
	// Le statut pour le changement de statut a la volee	
	$statut = $row['statut'];
	// La date de publication pour l'appel de la fonction 'dater'
	$date_heure = $row['date'];
	// Le reste n'est pas indispensable pour le moment...

	
// 3/4 - Preparation de l'affichage
	// Est-ce que quelqu'un a deja ouvert l'objet en edition ?
	$flag_editable = autoriser('modifier',$type_objet,$id_objet);
	if ($flag_editable AND $GLOBALS['meta']['articles_modif'] != 'non') {
		// Si oui, on affiche le message
		include_spip('inc/drapeau_edition');
		$modif = mention_qui_edite($id_objet, $type_objet);
	} else
		// Sinon, on se contente de laisser vide la variable $modif
		$modif = array();
	
	// On initialise la page et les entetes
	pipeline('exec_init', array('args'=>array('exec'=>$type_objet.'s_voir',$id_objet=>$id_objet),'data'=>'')); 

	// Puis charge tout le debut du HTML, les entetes...
	$commencer_page = charger_fonction('commencer_page', 'inc'); 
	// que l'on affiche ensuite
	echo $commencer_page(_T("&laquo; $titre &raquo;", "naviguer", "actualites_tous"));	

	// On affiche un fil d'ariane (assez rudimentaire)
	// pour faciliter la navigation sur le modele 
	// de 'afficher_hierarchie'
	
	// Y a t-il une rubrique attaché à cet objet ?
	$table_liens = "spip_".$type_objet."s_liens";
	$row = sql_fetsel("id_objet", $table_liens, "id_$type_objet=$id_objet AND objet = 'rubrique'","","id_objet ASC","1" );
	
	if($row['id_objet']) {
		$id_rubrique = $row['id_objet'];
		echo debut_grand_cadre(true);
		echo afficher_hierarchie($id_rubrique);
		
		// cas d'un objet dans 2 rubriques
		$row2 = sql_fetsel("id_objet", $table_liens, "id_$type_objet=$id_objet AND objet = 'rubrique'","","id_objet ASC","1,1" );
		if($row2['id_objet']) {
			$idrub2 = $row2['id_objet'];
			echo '<hr />';
			echo 'Aussi dans la rubrique : ';
			$rubtitre = sql_fetsel("titre", 'spip_rubriques', "id_rubrique=$idrub2");
			echo '<a href="'.generer_url_ecrire("naviguer","id_rubrique=".$idrub2).'">'.$rubtitre['titre'].'</a>';
		}
		echo fin_grand_cadre(true);
	} else {
	echo debut_grand_cadre(true);
	echo "<ul dir='ltr' class='verdana3' id='chemin'><li>"
		. "<span class='bloc'><a href='".generer_url_ecrire("naviguer","id_rubrique=0")."' class='racine'>Racine du site</a></span>"
		. "<ul><li>"
		. "<span class='bloc'>"
		. "<em> > </em>"
		. "<a href='".generer_url_ecrire("actualites_tous")."' class='secteur on'>"._T('actualites:naviguer_titre')."</a>"
		. "</span></li></ul></li></ul>";
	echo fin_grand_cadre(true);
	}

	// On choisit quels boutons seront affiches dans le 'bloc des raccourcis'
	// (dépends des autorisations donnees par CFG)
	$quels_boutons = "";
	if (function_exists('lire_config')) {	
		if ( lire_config('actualites/objet_actualite') != "off" )
			$quels_boutons.= icone_horizontale(_T('actualites:raccourcis_actualite'), generer_url_ecrire("actualites_edit","type=actualite&new=oui"), _DIR_ACTUALITES_IMG_PACK."actualite-24.gif", "creer.gif", false);
	}

		


// 4/4 - Le contenu 

	// # La colonne de gauche # //

	// On cree une colonne a gauche
	echo debut_gauche('', true)
		// On ouvre une boite d'infos.
		. debut_boite_info(true)
		// Le pipeline 'boite_infos' va recuperer le squelette decrit 
		// en '/prive/infos', et le calculer avec les parametres indiques
		. pipeline('boite_infos', array('data' => '','args' => array('type'=>$type_objet, 'id'=>$id_objet, 'row'=>$row)))
		// On ferme la boite d'infos
		. fin_boite_info(true);

		$flag_administrable = autoriser('modifier','actualite',$id_objet);
		$iconifier = charger_fonction('iconifier', 'inc');
		echo $iconifier('id_actualite', $id_objet, 'actualites_voir', false, $flag_administrable);

	// On a fini, mais on laisse la possibilite a d'autres
	// d'afficher du contenu ici
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'actualites_voir','id_$type_objet'=>$id_objet),'data'=>''));		



	// # La colonne de droite # //
	// (qui s'affiche a gauche en mode petit ecran)

	// On cree une colonne a droite
	echo creer_colonne_droite('', true)
		// On ouvre un bloc de raccourcis
		. bloc_des_raccourcis($quels_boutons);

	// On a fini, mais on laisse la possibilite a d'autres
	// d'afficher du contenu ici
	echo pipeline('affiche_droite',	array('args'=>array('exec'=>'actualites_voir','id_actualite'=>$id_actualite),'data'=>''));



	// # Contenu central # //

	// Voici une serie de variable necessaire a l'affichage...		
	// --> ici on creee un contexte pour l'appel du fond
	$contexte = array('id'=>$id_objet);

	// --> ici on recupere le fond, calcule selon le $contexte, qui
	// contient le prototype de la fiche objet. On notera qu'il existe
	// autant de fonds que de type d'objets differents.
	$fond = recuperer_fond("prive/contenu/$type_objet",$contexte);
	// Puis laisse la possibilite a d'autres d'ajouter du contenu ici
	// ou de faire des modifications		
	$fond = pipeline('afficher_contenu_objet', array('args'=>array('type'=>$type_objet,'id_objet'=>$id_objet,'contexte'=>$contexte),'data'=> $fond));

	// --> ici on inclut notre fond dans un onglet de contenu
	$onglet_contenu = "<div id='wysiwyg'>$fond</div>";

	// --> ici on cree l'onglet de proprietes
	// Prechargement du bloc de date et d'edition de mots-cles
	$dater = charger_fonction('dater', 'inc');
	$editer_mots = charger_fonction('editer_mots', 'inc');
	// L'onglet propriete n'est qu'un assemblage subtil des deux
	$onglet_proprietes = ($dater? $dater($id_objet, $flag_editable, $statut, $type_objet, 'actualites_voir', $date_heure): '')
		. $editer_mots($type_objet, $id_objet, $cherche_mot, $select_groupe, $flag_editable, true, 'actualites_voir');

	// --> ici un bouton de modification de l'objet
	$actions = $flag_editable
		? icone_inline(
			!$modif ? _T('actualites:icone_modifier_'.$type_objet)
				: "",
			generer_url_ecrire("actualites_edit","id_$type_objet=$id_objet&retour=nav"),
			!$modif ? _DIR_ACTUALITES_IMG_PACK."$type_objet-24.gif" : "warning-24.gif",
			!$modif ? "edit.gif" : '',
			$GLOBALS['spip_lang_right']
			)
		: "";	

	// --> ici deux boutons de navigations : precedent, suivant
	// Pour 'precedent. Requete sql qui selectionne 
	// le *premier* objet dont l'id est *inferieur*
	// à celui de l'actuel, que l'on associe ensuite à '$prec_id'.
	$requete_prec = mysql_query("SELECT id_".$type_objet." FROM spip_".$type_objet."s WHERE id_".$type_objet." < ".$id_objet." ORDER BY id_".$type_objet." desc LIMIT 1 ");
	$prec_id = mysql_fetch_array($requete_prec);
	// Idem pour 'suivant', on effectue une requete sql qui 
	// selectionne le *premier* objet dont l'id est *superieur* 
	// à celui de l'actuel, que l'on associe ensuite à '$suiv_id'.
	$requete_suiv = mysql_query("SELECT id_".$type_objet." FROM spip_".$type_objet."s WHERE id_".$type_objet." > ".$id_objet." ORDER BY id_".$type_objet." LIMIT 1 ");
	$suiv_id = mysql_fetch_array($requete_suiv);
	// On calcule les url precedente et suivante
	$prec_url = generer_url_ecrire("actualites_voir","id_$type_objet=$prec_id[0]");
	$suiv_url = generer_url_ecrire("actualites_voir","id_$type_objet=$suiv_id[0]");
	// On genere le texte a afficher sous les boutons
	$libelle_precedent_objet = "actualites:icone_precedent_".$type_objet;
	$libelle_suivant_objet = "actualites:icone_suivant_".$type_objet;
	// On cree les boutons avec leurs liens et libelles
	$boutons_nav = "<ul>"
		// Une boite pour le bouton precedent
		. "<li class='bouton_prec'>"
			// Contenu s'affiche seulement si un objet precede
			. ($prec_id ? icone_inline(_T($libelle_precedent_objet), $prec_url, _DIR_ACTUALITES_IMG_PACK."$type_objet-24.gif", _DIR_ACTUALITES_IMG_PACK."precedent.gif") : "")
		. "</li>"
		// Une boite pour le bouton suivant
		. "<li class='bouton_suiv'>"
			// Contenu d'affiche seulement si un objet suit
			. ($suiv_id ? icone_inline(_T($libelle_suivant_objet), $suiv_url, _DIR_ACTUALITES_IMG_PACK."$type_objet-24.gif", _DIR_ACTUALITES_IMG_PACK."suivant.gif") : "")
		. "</li>"		
		. "</ul>"; 



	// Et maintenant l'affichage proprement dit du contenu.
	// A ce stade nous possedons un onglet de contenu, un onglet
	// de proprietes, deux boutons de navigation et un bouton 
	// de modification qu'il va falloir organiser dans la fiche 
	// de l'objet.

	// On cree la colonne centrale de la page,
	echo debut_droite('', true)
		// on ouvre le bloc general de la fiche objet
		. "<div class='fiche_objet'>"
		// on affiche les boutons de navigation
		. "<div class='boutons_nav'>$boutons_nav</div>"
		// on affiche le bouton de modification
		. "<div class='bouton_actions'>$actions</div>"
		// on ouvre le sous-bloc des onglets
		. _INTERFACE_ONGLETS
		// on affiche les 2 onglets a l'aide la fonction adequate
		// qui fait ça proprement (core)
		. afficher_onglets_pages(array('voir' => _T('onglet_contenu'), 'props' => _T('onglet_proprietes')), array('props'=>$onglet_proprietes, 'voir'=>$onglet_contenu))
		// On ferme le bloc de la fiche objet	
		. "</div>";

	// On a fini, mais on laisse la possibilite a d'autres
	// d'afficher du contenu ici
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'actualites_voir','id_objet'=>$id_objet),'data'=>''));

	// Enfin, on ferme les differentes colonnes de la pages
	echo fin_gauche(), fin_page();
}

?>
