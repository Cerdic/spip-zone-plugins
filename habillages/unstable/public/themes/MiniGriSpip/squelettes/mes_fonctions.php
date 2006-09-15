<?php

// Copyright 2005 Maxime GEORGE-BOURREAU
// Adaptation pour SPIP 1.9 - 2006 © Fredo Mkb

// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

function menu($texte,$presence_rubrique_active=true) {

// Fonction menu

// Renvoie une chaine de caractères contenant du code HTML nécessaire à l'affichage
// d'un menu arborescent dans SPIP.

// Paramètres :

// 1. Mettre dans $texte l'identifiant de rubrique active, si on veut faire apparaitre une rubrique active.
// 2. Mettre $presence_rubrique_active à false si on ne veut pas faire apparaitre de rubrique active.

// Sortie :

// Code HTML nécessaire à l'affiche d'un menu arborescent, sous la forme suivante :

// 1. Chaque rubrique est listée sous la forme :
// <li class="menu-itemX">une rubrique...</li> 
// avec X la profondeur de la rubrique (0=racine du site) 

// 2. Si une rubrique est active, elle est de la forme :
// <li class="menu-itemX menu-selected">une rubrique...</li> 

if ($presence_rubrique_active) $rubrique_active=$texte;
else $rubrique_active=-1;
$rubriques=array();
$requete = mysql_query( "SELECT titre, id_rubrique, id_parent from spip_rubriques order by id_parent, titre");
while ($ligne = mysql_fetch_assoc($requete)) array_push($rubriques,$ligne);
mysql_free_result($requete);
return menu_rec($rubriques,$rubrique_active,0,0);
}

function menu_rec($rubriques,$rubrique_active,$pere,$rang) {

// Fonction menu_rec

// Utilisée par la fonction menu. Explore l'arborescence des rubriques de façon récursive.

	$sortie = '';
	for ($i=0;$i<count($rubriques);$i++) {
		if ($rubriques[$i]['id_parent']==$pere) {
			$sortie .='<li class="mgs_menu_item_'.$rang;
			if ($rubrique_active==$rubriques[$i]['id_rubrique']) $sortie .= ' mgs_menu_selected';
			$sortie .= '"><a href="spip.php?rubrique';
			$sortie .= $rubriques[$i]['id_rubrique'];
			$titre=ereg_replace("^[0-9]+[.][ ]","",$rubriques[$i]['titre']);
			$sortie .= '">'.$titre.'</a>';
			$nb_articles = nombre_articles_rubrique($rubriques[$i]['id_rubrique']);
			// Ajout du nombre d'articles publies existants dans la rubrique
			$sortie .= '&nbsp;<small>('.$nb_articles.')</small></li>';
			//Ici ajouter condition pour ne pas afficher toutes les noeuds
			$sortie .= menu_rec($rubriques,$rubrique_active,$rubriques[$i]['id_rubrique'],$rang+1);
		}
	}
	return $sortie;
}

function nombre_articles_rubrique($id_rubrique) {
// Fonction pour retourner le nombre d'articles publies existants dans une rubrique ($id_rubrique)
	
	$requete = mysql_query("SELECT id_article FROM spip_articles WHERE id_rubrique=$id_rubrique AND statut='publie'");
	$nbr = mysql_num_rows($requete);
	mysql_free_result($requete);
	return $nbr;
}

function numero_message_forum_article($id_article,$id_forum) {
// Fonction pour retourner le numero incremental d'un message du forum ($id_forum) d'un article ($id_article) lors d'une reponse
	
	$requete = mysql_query("SELECT id_forum FROM spip_forum WHERE id_article=$id_article AND id_forum<=$id_forum AND statut='publie'");
	$nbr = mysql_num_rows($requete);
	mysql_free_result($requete);
	return $nbr;
}

function nombre_articles_auteur($id_auteur) {
// Fonction pour retourner le nombre d'articles rediges par un auteur ($id_auteur) 
	
	$requete = mysql_query("SELECT id_article FROM spip_auteurs_articles WHERE id_auteur=$id_auteur");
	$nbr = mysql_num_rows($requete);
	mysql_free_result($requete);
	return $nbr;
}

function statut_article($id_article) {
// Fonction pour retourner le "statut" d'un article ($id_article) soit ('prepa', 'prop', 'publie', 'refuse', 'poubelle')
	
	$requete = mysql_query("SELECT statut FROM spip_articles WHERE id_article=$id_article");
	$statuts = mysql_fetch_assoc($requete);
	mysql_free_result($requete);
	return $statuts[statut];
}

function nombre_articles_publies_auteur($id_auteur) {
// Fonction pour retourner le nombre d'articles publies par un auteur ($id_auteur) 
	
	$articles = array();
	$requete = mysql_query("SELECT id_article FROM spip_auteurs_articles WHERE id_auteur=$id_auteur");
	while ($ligne = mysql_fetch_assoc($requete)) array_push($articles,$ligne);
	mysql_free_result($requete);
	$nbr = 0;
	foreach ($articles as $val) {
		$statut = statut_article($val[id_article]);
		if ($statut == 'publie') {$nbr += 1;};
	}
	return $nbr;
}


?>
