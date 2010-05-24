<?php

/*	Fonction generale d'affichage du contenu des objets, 	*/
/*	dans l'espace prive.					*/
/*	Elle s'affiche lors de l'appel de la page 		*/
/*	'exec/veille_voir&type=truc&id=1', en recuperant	*/	
/*	donc deux parametres passes dans l'url, depuis la	*/
/* 	page 'veille_tous'.					*/


if (!defined("_ECRIRE_INC_VERSION")) return;

// Chargement prealable des fonctions exterieures
// suivantes (du core) dont on va avoir besoin pour la suite
include_spip('inc/presentation');
include_spip('inc/actions');


function afficher_vu_hierarchie($id_parent, $message='',$id_objet=0,$type='',$id_secteur=0,$restreint='') {
	global $spip_lang_left,$spip_lang_right;

	$out = "";
	$nav = "";

	$parents = '';
	$tag = "a";
	$on = ' on';


	$res = sql_fetsel("id_parent, titre, lang", "spip_rubriques", "id_rubrique=".intval($id_rubrique));

	$id_parent = $res['id_parent'];
	changer_typo($res['lang']);

	$class = '';

	$parents = "<ul><li><span class='bloc'><em> &gt; </em><$tag class='$class$on'"
	. ($tag=='a'?" href='". generer_url_ecrire("naviguer","veille_voir")."'":"")
	. ">"
	. supprimer_numero(typo(sinon($res['titre'], _T('Vu !'))))
	. "</$tag></span>"
	. $parents
	. "</li></ul>";



	$out .=  $nav;

	$out = pipeline('affiche_hierarchie',array('args'=>array(
			'id_parent'=>$id_parent,
			'message'=>$message,
			'id_objet'=>$id_objet,
			'objet'=>$type,
			'id_secteur'=>$id_secteur,
			'restreint'=>$restreint),
			'data'=>$out));

 	return $out;
}


function exec_veille_voir(){

// 1/4 - Les autorisations
	// Avant meme de commencer, on verifie qu'on a bien
	// l'autorisation de voir cette page.
	if (!autoriser('voir', 'veille_voir')) {
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
	$table_objet = "spip_vu_".$type_objet."s";

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
	echo $commencer_page(_T("&laquo; $titre &raquo;", "naviguer", "vu_tous"));	

	// On affiche un fil d'ariane (assez rudimentaire)
	// pour faciliter la navigation sur le modele 
	// de 'afficher_hierarchie'
	echo debut_grand_cadre(true);
	echo "<ul dir='ltr' class='verdana3' id='chemin'><li>"
		. "<span class='bloc'><a href='".generer_url_ecrire("naviguer","id_rubrique=0")."' class='racine'>Racine du site</a></span>"
		. "<ul><li>"
		. "<span class='bloc'>"
		. "<em> > </em>"
		. "<a href='".generer_url_ecrire("veille_tous")."' class='secteur on'>"._T('vu:naviguer_titre')."</a>"
		. "</span></li></ul></li></ul>";
	echo fin_grand_cadre(true);

	// On choisit quels boutons seront affiches dans le 'bloc des raccourcis'
	// (dépends des autorisations donnees par CFG)
	$quels_boutons = "";
	if (function_exists('lire_config')) {	
		if ( lire_config('vu/objet_annonce') != "off" )
			$quels_boutons.= icone_horizontale(_T('vu:raccourcis_annonce'), generer_url_ecrire("veille_edit","type=annonce&new=oui"), _DIR_VU_IMG_PACK."annonce-24.gif", "creer.gif", false);
		if( lire_config('vu/objet_evenement') != "off")
			$quels_boutons.= icone_horizontale(_T('vu:raccourcis_evenement'), generer_url_ecrire("veille_edit","type=evenement&new=oui"), _DIR_VU_IMG_PACK."evenement-24.gif", "creer.gif", false);
		if( lire_config('vu/objet_publication') != "off")
			$quels_boutons.= icone_horizontale(_T('vu:raccourcis_publication'), generer_url_ecrire("veille_edit","type=publication&new=oui"), _DIR_VU_IMG_PACK."publication-24.gif", "creer.gif", false);
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

	// On a fini, mais on laisse la possibilite a d'autres
	// d'afficher du contenu ici
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'veille_voir','id_$type_objet'=>$id_objet),'data'=>''));		



	// # La colonne de droite # //
	// (qui s'affiche a gauche en mode petit ecran)

	// On cree une colonne a droite
	echo creer_colonne_droite('', true)
		// On ouvre un bloc de raccourcis
		. bloc_des_raccourcis($quels_boutons);

	// On a fini, mais on laisse la possibilite a d'autres
	// d'afficher du contenu ici
	echo pipeline('affiche_droite',	array('args'=>array('exec'=>'breves_voir','id_breve'=>$id_breve),'data'=>''));



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
	$onglet_proprietes = ($dater? $dater($id_objet, $flag_editable, $statut, $type_objet, 'veille_voir', $date_heure): '')
		. $editer_mots($type_objet, $id_objet, $cherche_mot, $select_groupe, $flag_editable, true, 'veille_voir');

	// --> ici un bouton de modification de l'objet
	$actions = $flag_editable
		? icone_inline(
			!$modif ? _T('vu:icone_modifier_'.$type_objet)
				: "",
			generer_url_ecrire("veille_edit","id_$type_objet=$id_objet&retour=nav"),
			!$modif ? _DIR_VU_IMG_PACK."$type_objet-24.gif" : "warning-24.gif",
			!$modif ? "edit.gif" : '',
			$GLOBALS['spip_lang_right']
			)
		: "";	

	// --> ici deux boutons de navigations : precedent, suivant
	// Pour 'precedent. Requete sql qui selectionne 
	// le *premier* objet dont l'id est *inferieur*
	// à celui de l'actuel, que l'on associe ensuite à '$prec_id'.
	$requete_prec = mysql_query("SELECT id_".$type_objet." FROM spip_vu_".$type_objet."s WHERE id_".$type_objet." < ".$id_objet." ORDER BY id_".$type_objet." desc LIMIT 1 ");
	$prec_id = mysql_fetch_array($requete_prec);
	// Idem pour 'suivant', on effectue une requete sql qui 
	// selectionne le *premier* objet dont l'id est *superieur* 
	// à celui de l'actuel, que l'on associe ensuite à '$suiv_id'.
	$requete_suiv = mysql_query("SELECT id_".$type_objet." FROM spip_vu_".$type_objet."s WHERE id_".$type_objet." > ".$id_objet." ORDER BY id_".$type_objet." LIMIT 1 ");
	$suiv_id = mysql_fetch_array($requete_suiv);
	// On calcule les url precedente et suivante
	$prec_url = generer_url_ecrire("veille_voir","id_$type_objet=$prec_id[0]");
	$suiv_url = generer_url_ecrire("veille_voir","id_$type_objet=$suiv_id[0]");
	// On genere le texte a afficher sous les boutons
	$libelle_precedent_objet = "vu:icone_precedent_".$type_objet;
	$libelle_suivant_objet = "vu:icone_suivant_".$type_objet;
	// On cree les boutons avec leurs liens et libelles
	$boutons_nav = "<ul>"
		// Une boite pour le bouton precedent
		. "<li class='bouton_prec'>"
			// Contenu s'affiche seulement si un objet precede
			. ($prec_id ? icone_inline(_T($libelle_precedent_objet), $prec_url, _DIR_VU_IMG_PACK."$type_objet-24.gif", _DIR_VU_IMG_PACK."precedent.gif") : "")
		. "</li>"
		// Une boite pour le bouton suivant
		. "<li class='bouton_suiv'>"
			// Contenu d'affiche seulement si un objet suit
			. ($suiv_id ? icone_inline(_T($libelle_suivant_objet), $suiv_url, _DIR_VU_IMG_PACK."$type_objet-24.gif", _DIR_VU_IMG_PACK."suivant.gif") : "")
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
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'veille_voir','id_objet'=>$id_objet),'data'=>''));

	// Enfin, on ferme les differentes colonnes de la pages
	echo fin_gauche(), fin_page();
}

?>
