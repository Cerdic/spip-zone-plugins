<?php

/*	Fonction generale d'affichage d'edition du contenu des	*/
/*	objets, dans l'espace prive.				*/
/*	Elle s'affiche selon deux type d'appel :		*/
/*	   - veille_edit&id_objet=x : dans le cas de la 	*/
/*	     modification d'un objet existant ;			*/
/*	   - veille_edit&type=objet&new=oui : dans le cas	*/
/*	     d'un nouvel objet.					*/


if (!defined("_ECRIRE_INC_VERSION")) return;

// Chargement prealable des fonctions exterieures
// suivantes (du core) dont on va avoir besoin pour la suite
include_spip('inc/presentation');
include_spip('inc/documents');
include_spip('inc/barre');


// CE QUI DOIT ETRE FAIT AVANT (autorisations notamment)
function exec_veille_edit()
{
	// On recupere la valeur 'new'. Que l'objet soit nouveau
	// ou pas conditionne toute la suite.
	$new = _request('new');

	// Quel est le type de l'objet ?
	if ($new != "oui") {
		// Si l'objet existe deja (il n'est pas 'new'),
		// alors le type de l'objet se trouve dans l'URL
		// entre les chaines 'id_' et '='. On utilise une regex
		// pour le recuperer.
		preg_match("/id_(.*)=/U",$_SERVER["REQUEST_URI"],$matches);
		$type_objet = $matches[1];
		// Avec ca, on en profite pour recuperer l'id de l'objet
		// dans l'URL. Requis pour la demande d'autorisation qui suit.
		$id_objet = _request('id_'.$type_objet);
	} else {
		// Sinon il est nouveau, alors le type est explicitement
		// passe en parametre dans l'URL.
		$type_objet = _request('type');
	}

	// On cherche a savoir si l'autorisation de modification est donne
	// Egal à 1, si admin ou redac (cf 'vu_autoriser.php').
	$autorisation = autoriser('modifier', $type_objet, $id_objet);

	// Si je suis autorise, alors :
	if ($autorisation) {
		// on cree une variable qui contiendra tous les champs de l'objet 
		// en cours de traitement. On lui donne une valeur par defaut.
		$row = false;

		// 2 possibilites dans le cas d'une edition
		if ($new != "oui") {
			// Si l'objet existe deja (il n'est pas 'new'),
			// alors on remplit la variable 'row' par le contenu existant
			$row = sql_fetsel("*", "spip_vu_".$type_objet."s", "id_$type_objet=$id_objet");
		} else { 
			// Sinon il est nouveau (forcement), on change alors juste 'row'
			$row = true;
		}
	}
		
	// Consequence : on ne peut continuer que dans le cas où 'row' ne possede plus sa valeur par defaut.
	if (!$row) {
		// Si 'row' possede toujours sa valeur par defaut (autorisation manquante au dessus),
		// alors un message d'erreur est affiche.
		include_spip('inc/minipres');
		echo minipres();
	} else {
		// sinon, tout va bien, on appelle la fonction d'edition
		// avec les quatres variables de contexte	
		objet_edit_ok($row, $type_objet, $id_objet, $new);
	}
}



// EDITION DE L'OBJET
function objet_edit_ok($row, $type_objet, $id_objet, $new)
{
	global  $connect_statut, $spip_lang_right;

// 1 - Le contexte

	// Que contiennent les champs de la base ?
	if ($new != 'oui') {		
		// Si l'objet n'est pas nouveau, alors il est en base
		// on peut donc charger les differents champs dans des variables
		$titre=$row['titre'];
		/*$lien=$row['lien'];
		$annonceur=$row['annonceur'];
		$peremption=$row['peremption'];
		$type=$row['type'];
		$descriptif=$row['descriptif'];
		$source_nom=$row['source_nom'];
		$source_lien=$row['source_lien'];*/
		$statut=$row['statut'];

	} else {			
		// Sinon il s'agit d'un nouvel objet
		// Les champs sont donc vides, sauf les suivants qui acquierent une valeur par defaut
		$titre = filtrer_entites(_T('vu:titre_nouvelle_'.$type_objet));	// ici un titre provisoire
		$statut = "prop";						// ici un statut provisoire
	}


// 2 - Preparation de l'affichage

	// On initialise la page et les entetes
	pipeline('exec_init',array('args'=>array('exec'=>'objets_edit','id_objet'=>$id_objet),'data'=>''));
	// Puis charge tout le debut du HTML, les entetes...
	$commencer_page = charger_fonction('commencer_page', 'inc');
	// que l'on affiche ensuite
	echo $commencer_page(_T('vu:titre_page_'.$type_objet.'s_edit', array('titre' => $titre)), "naviguer", $types_objet.'s');

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


// 3 - Le contenu

	// # Colonne de gauche #

	// On cree une colonne a gauche
	echo debut_gauche('', true);
	// Nous n'affichons pour notre part rien ici...
	// ... mais laissons ici la possibilite a d'autres de le faire
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'annonces_edit','id_annonce'=>$id_annonce),'data'=>''));


	
	// # Contenu central #

	// On cree une colonne centrale
	echo debut_droite('', true);

	// On cree le contexte d'edition de l'objet,
	// qui sera envoye ensuite au formulaire
	$contexte = array(
		// Notre formulaire aura une icone de retour (sauf nouvel objet)
		'icone_retour'=>$new=='oui'?'':icone_inline(_T('icone_retour'), generer_url_ecrire("veille_voir","id_$type_objet=$id_objet"), _DIR_VU_IMG_PACK."$type_objet-24.gif", "rien.gif",$GLOBALS['spip_lang_left']),
		// Son adresse de redirection sera : 
		'redirect'=>generer_url_ecrire("veille_voir"),
		// Son titre sera :
		'titre'=>$titre,
		// La variable 'new' aura comme valeur 'oui' en cas de creation,
		// et l'id de l'objet en cas de modification
		'new'=>$new == "oui"?$new:$id_objet,
		// Ses parametres de configuration se trouveront :
		'config_fonc'=>$type_objet.'s_edit_config'
	);

	// Le principal : on recupere et affiche le fond
	// (morceau de squelette calcule qui contient le formulaire d'edition).
	// Nota : chaque type d'objet possede son fond propre, et donc son
	// formulaire propre, ses fonctions CVT propres. Cela amene certes
	// un peu de redondance, mais semble quand meme plus logique a terme.
	echo recuperer_fond("prive/editer/".$type_objet, $contexte);

	// On a fini, mais on laisse la possibilite a d'autres
	// d'afficher du contenu ici
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'veille_edit','id_objet'=>$id_objet),'data'=>''));

	// Enfin, on ferme les differentes colonnes de la pages
	echo fin_gauche(), fin_page();

}

?>
