<?php

#   +----------------------------------+
#    Nom du Filtre : licence   
#   +----------------------------------+
#    date : 11/04/2007
#    auteur :  fanouch - lesguppies@free.fr
#    version: 0.1
#    licence: GPL
#   +-------------------------------------+
#    Fonctions de ce filtre :
#	permet de lier une licence à un article 
#   +-------------------------------------+
# Pour toute suggestion, remarque, proposition d ajout
# reportez-vous au forum de l article :
# http://www.spip-contrib.net/fr_article2147.html
#   +-------------------------------------+

function balise_LICENCE_dist ($p)
{
	$p->code = "licence_affiche(".champ_sql('id_article', $p).")";
	$p->interdire_scripts = false;
	return $p;
}

function licence_affiche ($id_article)
{
	global $table_prefix, $licence_licences;
	
	$sql = "SELECT id_licence FROM ".$table_prefix."_articles WHERE id_article = '".$id_article."'";
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	$article = mysql_fetch_assoc($req);
	$licence = $licence_licences[$article['id_licence']];
	if (!empty($licence))
	{
		if (!empty($licence['icon']))
		{
			$text = $licence['name']."<br><img src='"._DIR_PLUGIN_LICENCE."img_pack/".$licence['icon']."' title='".htmlspecialchars($licence['description'], ENT_QUOTES)."'>";
			if (!empty($licence['link']))
				$text = "<a href='".$licence['link']."'>".$text."</a>";
		}
		else
			$text = $licence['name'];
	}
	else $text = "Sans licence";

	return $text;
}


?>