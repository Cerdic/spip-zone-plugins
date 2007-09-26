<?php

/*
 *   +----------------------------------+
 *    Nom du Filtre :    Chatons
 *   +----------------------------------+
 *    Date : lundi 25 Septembre 2007
 *    Auteur :  Gurdil
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *    Cette fonction permet d'afficher des smileys 
 *    ou chatons.
 *   +-------------------------------------+ 
*/

function spipbb_chatons($texte) {
    $path = dirname(find_in_path('../../plugins/spipBB/chatons/test'));
	$liste = $chatons = array();
	$dossier=opendir($path);
	while ($image = readdir($dossier)) {
		if (preg_match(',^([a-z][a-z0-9_-]*)\.(png|gif|jpg),', $image, $reg)) { 
			$chatons[0][] = ':'.$reg[1];
			$liste[] = '<strong>:'.$reg[1].'</strong>';	
			list(,,,$size) = @getimagesize("$path/$reg[1].$reg[2]");
			$chatons[1][] = "<img class=\"no_image_filtrer\" alt=\"$reg[1]\" title=\"$reg[1]\" src=\"plugins/spipbb/chatons/$reg[1].$reg[2]\" $size/>";
		}
	}

	if (strpos($texte, ':')===false) return $texte;
	$chatons_rempl = $chatons;
	return str_replace($chatons_rempl[0], $chatons_rempl[1], $texte);
}

/*
 *   +----------------------------------+
 *    Nom du Filtre :    quelstatut
 *   +----------------------------------+
 *    Date : 1 fevrier 2007
 *    Auteur :  Chryjs (chryjs@free.fr)
 *   +----------------------------------+
 *
 * Essai de realisation d un filtre quelstatut
*/
/*
function spipbb_quelstatut($nom='',$id='') {
global $table_prefix;

	if ($nom) $query = "SELECT statut FROM ".$table_prefix."_auteurs WHERE nom='$nom'";
	if ($id) $query = "SELECT statut FROM ".$table_prefix."_auteurs WHERE id_auteur=$id";

	$result_auteurs = spip_query($query);

	switch ($result_auteurs[statut]) {
	'0minirezo': return _T('info_administrateur_1');
		break;
	'1comite': return _T('info_redacteur_1');
		break;
	'6forum':
	'6visiteur': return _T('info_visiteur_1');
		break;
	'5poubelle':
	default:
		return '';
	}
	return  '';
}
*/

/*
 *   +----------------------------------+
 *    Nom du Filtre :    get_auteur_infos
 *   +----------------------------------+
 *    Date : lundi 23 février 2004
 *    Auteur :  Nikau (luchier@nerim.fr)
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *    Cette fonction permet d'obtenir toutes les infos 
 *    d'un auteur avec son nom ou son id_auteur
 *    ATTENTION !! cette fonction ne s'utilise pas de       
 *    façon classique !! voir explication dans la contrib'
 *    Fonction utilisée également dans la fonction
 *    'afficher_avatar'
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.spip-contrib.net/article.php3?id_article=261
*/
// profile.html + afficher_avatar
function spipbb_get_auteur_infos($id='', $nom='') {
	if ($id) $query = "SELECT * FROM spip_auteurs WHERE id_auteur=$id";
	if ($nom) $query = "SELECT * FROM spip_auteurs WHERE nom='$nom'";
	$result = spip_query($query);

	if ($row = spip_fetch_array($result)) {
		$row=serialize($row);
	}
	return $row;
}

/*
 *   +----------------------------------+
 *    Nom du Filtre :    afficher_avatar
 *   +----------------------------------+
 *    Date : lundi 23 février 2004
 *    Auteur :  Nikau (luchier@nerim.fr)
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *    Cette fonction permet d'afficher 
 *    l'avatar d'un auteur.
 *    On peut passer une classe CSS pour régler
 *    l'affichage
 *    EXEMPLE :
 *    [(#NOM|afficher_avatar{''})] ou
 *     [(#NOM|afficher_avatar{'nom_de_la_classe'})]
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/article.php3?id_article=261
*/
// voirsujet
function spipbb_afficher_avatar($nom, $classe='') {
	if ($classe!='') $insert=" class=\"$classe\""; else $insert="";
	
	$infos=unserialize(spipbb_get_auteur_infos('', $nom));
	$fichier = '';
	
	if ($infos['statut']=="0minirezo" OR $infos[statut]=="1comite") {
		$racine="auton$infos[id_auteur]";
		if (file_exists("IMG/$racine.gif")) {
			$fichier = "$racine.gif";
		}
		else if (file_exists("IMG/$racine.jpg")) {
			$fichier = "$racine.jpg";
		}
		else if (file_exists("IMG/$racine.png")) {
			$fichier = "$racine.png";
		}
	    
		if($fichier!= '' ){
			$retour="<img".$insert." src=\"IMG/$fichier\" alt=\"avatar\" />";
		}
	}
	else {
		if ($infos['statut']=="6forum") {
			$infos=unserialize(spipbb_get_auteur_infos('', $nom));
			$source=unserialize($infos[extra]);
			$source_extra=$source[avatar];
			if(isset($source_extra))
				$retour="<img".$insert."  src=\"".$source_extra."\" alt=\"Avatar\" />";
		}
	}
	return $retour;
}

/*
 *   +----------------------------------+
 *    Nom des Filtres :  afficher_mots_clefs et pas_afficher_mots_clefs
 *   +----------------------------------+
 *    Date : lundi 25 fevrier 2004
 *    Auteur :  Nikau (luchier@nerim.fr)
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *    Permet d'afficher ou non les mots clefs pour
 *    les forums selon le statut de l'auteur du message
 *    EXEMPLE :
 *    [(#ID_FORUM|afficher_mots_clefs] ou
 *     [(#ID_FORUM|pas_afficher_mots_clefs]
 *   !! Adaptez les numeros (10 et 11) Ã  vos numeros de groupe de mots cles !!
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://spip-contrib.net/article.php3?id_article=421
*/
// poster
function spipbb_afficher_mots_clefs($texte) {
// 3 a changer par le num du Groupe "Type de sujets"
// 4 a changer par le num du Groupe de mot cle "Moderation"
	if (($GLOBALS['auteur_session']['statut']=='0minirezo') OR ($GLOBALS['auteur_session']['statut']=='1comite'))
	{
		$GLOBALS['afficher_groupe'][]=3; //$GLOBALS['spipbb']['spipbb_id_mot_annonce']
		$GLOBALS['afficher_groupe'][]=4; //$GLOBALS['spipbb']['spipbb_id_mot_ferme']
	}
	else {
		$GLOBALS['afficher_groupe'][]=0; 
	}
} // afficher_mots_clefs

// 4 a changer par le num du Groupe de mot cle "Moderation"
function spipbb_pas_afficher_mots_clefs($texte) {
	if (($GLOBALS['auteur_session']['statut']=='0minirezo'))
	{
		$GLOBALS['afficher_groupe'][]=4; //$GLOBALS['spipbb']['spipbb_id_mot_ferme']
	}
	else {
		$GLOBALS['afficher_groupe'][]=0;
	}
} //pas_afficher_mots_clefs

/*
 *   +---------------------------------------------+
 *    Nom du Filtre : Nombre de messages 
 *   +---------------------------------------------+
 *    Date : mercredi 09 avril 2003
 *    Auteur : BoOz Email:booz@bloog.net
 *    site : http://bloog.net
 *   +---------------------------------------------+
 *    Fonctions de ce filtre :
 *    Compte le nombre de messages d'un auteur
 *     Appelez le dans vos squellette tout simplement
 *     par : [(#ID_AUTEUR|nb_messages)]
 *   +---------------------------------------------+
 *
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/
 *
 */
// membres_liste profil_bb
function spipbb_nb_messages($id_auteur){
global $table_prefix;
	$query = "SELECT auteur FROM ".$table_prefix."_forum WHERE id_auteur=$id_auteur";
	$nb_mess = "";
	$result_auteurs = spip_query($query);
	$nb_mess = spip_num_rows($result_auteurs);
	return $nb_mess;
} // nb_messages

/*
 *   +----------------------------------+
 *    Nom du Filtre :    citation                                            
 *   +----------------------------------+
 *    Date : vendredi 11 novembre 2006
 *    Auteur :  BoOz
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     affiche le texte a citer
 *   +-------------------------------------+
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
*/
/*
function spipbb_barre_forum_citer($texte, $lan)
{
	include_ecrire('inc/layer');

	if (!$premiere_passe = rawurldecode(_request('retour_forum'))) {
		if($GLOBALS['citer']){

			$id_citation = $GLOBALS['id_forum'] ;
			$query = "SELECT * FROM spip_forum WHERE id_forum=$id_citation";
			$result = spip_query($query);
			$row = spip_fetch_array($result);
			//ajout de la citation
			$texte="\{\{$row[auteur] $lan:}}\n\n<quote>\n$row[texte]\n</quote>\n";
		}
	}

	if (!$GLOBALS['browser_barre'])
		return "<textarea name='texte' rows='12' class='forml' cols='40'>$texte</textarea>";
	static $num_formulaire = 0;
	$num_formulaire++;
	include_spip('inc/barre');
	return afficher_barre("document.getElementById('formulaire_$num_formulaire')", true) .
	  "<textarea name='texte' rows='12' class='forml' cols='40' id='formulaire_$num_formulaire' onselect='storeCaret(this);' onclick='storeCaret(this);' onkeyup='storeCaret(this);' ondbclick='storeCaret(this);'>$texte</textarea>";
} // barre_forum_citer
*/

/*
 *   +----------------------------------+
 *    Nom du Filtre :    citation                                            
 *   +----------------------------------+
 *    BASE : ... Date : vendredi 11 novembre 2006 - Auteur :  BoOz
 *    
 *    MODIF .. SCOTY .. 29/10/06 .. -> spip 1.9.1/2 
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     affiche le texte à citer    
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.spip-contrib.net/Pagination,663
*/

function barre_forum_citer($texte, $lan, $rows, $cols, $lang='') {
	if (!$premiere_passe = rawurldecode(_request('retour_forum'))) {
		if(_request('citer')=='oui'){
			$id_citation = _request('id_forum') ;
			$query = "SELECT auteur, texte FROM spip_forum WHERE id_forum=$id_citation";
		    $result = spip_query($query);
		    $row = spip_fetch_array($result);
		    $aut_cite=$row['auteur'];
		    $text_cite=$row['texte'];
		    
			//ajout de la citation
			$texte="{{ $aut_cite $lan }}\n<quote>\n$text_cite</quote>\n";
		}
	}
	return barre_textarea($texte, $rows, $cols, $lang);
}
?>
