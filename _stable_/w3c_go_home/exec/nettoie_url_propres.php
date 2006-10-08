<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2005                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


include_spip("inc/indexation");
include_spip("inc/logos");
include_spip("inc/presentation");
include_spip("inc/distant");


function exec_nettoie_url_propres(){
//
// Recupere les donnees
//

debut_page(_L("Tous les Documents"), "documents", "documents");
debut_gauche();


//////////////////////////////////////////////////////
// Boite "voir en ligne"
//

debut_boite_info();

echo propre(_L('Cette page r&eacute;initialise toutes les url propres du site'));

fin_boite_info();



debut_droite();

global $connect_statut;
if ($connect_statut != '0minirezo') {
	echo "<strong>"._T('avis_acces_interdit')."</strong>";
	fin_page();
	exit;
}

echo "Articles :";
echo spip_query("UPDATE spip_articles SET url_propre=''")."<br/>";
echo "Rubriques :";
echo spip_query("UPDATE spip_rubriques SET url_propre=''")."<br/>";
echo "Breves :";
echo spip_query("UPDATE spip_breves SET url_propre=''")."<br/>";
echo "Mots :";
echo spip_query("UPDATE spip_mots SET url_propre=''")."<br/>";
echo "Auteurs :";
echo spip_query("UPDATE spip_auteurs SET url_propre=''")."<br/>";


fin_page();
}

?>