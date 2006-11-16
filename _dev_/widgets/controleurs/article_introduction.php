<?php
// un controleur php + html
// html == avec un modele, controleurs/article_introduction.html)
function controleurs_article_introduction_dist($regs) {
    list(,$widget,$type,$champ,$id) = $regs;
    $valeur = valeur_colonne_table($type, array('descriptif', 'chapo', 'texte'), $id);
    if ($valeur === false) {
	    return array("$type $id $champ: " . _U('widgets:pas_de_valeur'), 6);
    }

	$n = new Widget('article-introduction-' . $id, $valeur,
			array('hauteurMini' => 234));
    
    $contexte = array(
    	'h_descriptif' => (int)ceil($n->hauteur*2/13),
		'h_chapo' => (int)ceil($n->hauteur*4/13),
		'h_texte' => (int)ceil($n->hauteur*4/13));
    $html = $n->formulaire($contexte);
    $status = NULL;

	return array($html, $status);
}

?>
