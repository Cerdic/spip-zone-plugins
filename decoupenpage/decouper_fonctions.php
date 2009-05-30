<?php
/*
 *   +----------------------------------+
 *    Nom du Filtre : decouper_en_page                                               
 *   +----------------------------------+
 *    Date : mardi 28 janvier 2003
 *    Auteur :  "gpl"  : gpl@macplus.org                                      
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     Il sert a pr�senter un article sur plusieurs pages
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/article.php3?id_article=62
*/


function decouper_en_page($texte) {
        global $artsuite, $var_recherche;
        
        if (empty($artsuite)) $artsuite = 0;
        
        $page = split('\+\+\+\+', $texte);
        
        $num_pages = count($page);

        // Si une seule page ou num�ro ill�gal, alors retourner tout le texte.
        // Cas sp�cial : si var_recherche positionn�, tout renvoyer pour permettre � la surbrillance
	    // de fonctionner correctement.
        if ($num_pages == 1 || !empty($var_recherche) || $artsuite < 0 || $artsuite > $num_pages) {
                return $texte;
        } 

        $p_prec = $artsuite - 1;
        $p_suiv = $artsuite + 1;
        $uri_art = generer_url_article($GLOBALS['id_article']);
        $uri_art .= strpos($uri_art, '?') ? '&' : '?';
        
        switch (TRUE) {
                case ($artsuite == 0):
                        $precedent = "";
                        $suivant = "<A HREF='" . $uri_art . "artsuite=" . $p_suiv . "'>&gt;&gt;</A>";
                        break;
                case ($artsuite == ($num_pages-1)):
                        $precedent = "<A HREF='" . $uri_art . "artsuite=" . $p_prec . "'>&lt;&lt;</A>";
                        $suivant = "";
                        break;
                default:
                        $precedent = "<A HREF='" . $uri_art . "artsuite=" . $p_prec . "'>&lt;&lt;</A>";
                        $suivant = "<A HREF='" . $uri_art . "artsuite=" . $p_suiv . "'>&gt;&gt;</A>";
                        break;
        }
        
        for ($i = 0; $i < $num_pages; $i++) {
                $j = $i;
                if ($i == $artsuite) {
                        $milieu .= " <B>" . ++$j . "</B> ";
                } else {
                        $milieu .= " <A HREF='" . $uri_art . "artsuite=$i'>" . ++$j . "</A> ";
                }
        }

        // Ici, on peut personnaliser la pr�sentation
        $resultat = "<P><DIV CLASS='pagination'>$precedent $milieu $suivant</DIV></P>";
        $resultat .= $page[$artsuite];
        $resultat .= "<P CLASS='pagination'><DIV CLASS='pagination'>$precedent $milieu $suivant</DIV></P>";
        return $resultat;
}
// FIN du Filtre decouper_en_page

?>
