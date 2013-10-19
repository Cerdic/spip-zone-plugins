<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/saisies/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	
    "analyse_moyenne_temperature_label"         =>"Analyse : température de corps noirs",
    "analyse_moyenne_temperature_explication"   =>"Pour l'analyse des réponses, afficher la moyenne des températures de corps noirs.",
    "temperature_corps_noir_label"              =>"Équivalence entre couleur perçue et température de corps noir",
    "temperature_corps_noir_explication"        =>"Il n'existe pas de relation simple entre le maximum d'émission d'un corps noir et la couleur perçue par l'œil (voir <a href='http://media4.obspm.fr/public/FSU/pages_corps-noir/temperature-couleur-observer.html' class='spip_out'>l'explication de l'Observatoire de Paris-Meudon</a>). C'est pourquoi pour que l'analyse de la température de corps noirs soit correcte à partir des résultats du formulaire, il vous faut indiquer la correspondance entre la couleur visuelle et la temperature de corps noir, exprimée en K. Sous la forme <code>codecouleur|temperature</code> (une par ligne)." ,       
    "temperature_moyenne"                       =>"Température moyenne",
    "choix_couleur_datas_explication"=>"Vous devez indiquez un choix par ligne sous la forme <code>code couleur|Label du choix</code>. Le code couleur peut-être :
	<br />
	-Un code CSS/HTML non précédé de <code>#</code>. Par exemple <code>ffccff</code>.<br />
	-Une longueur d'onde dans le vide, exprimée en nm, écrite sous la forme <code>lamdalongueur_d'onde</code>. Par exemple : <code>lamda500</code>. La longuer d'onde doit être comprise entre 380 (inclu) et 780 (inclu) nm.
	",
	"choix_couleur_defaut_choix1"=>"Couleur 1",
	"choix_couleur_defaut_choix2"=>"Couleur 2",
	"choix_couleur_defaut_choix3"=>"Couleur 3",
	"choix_couleur_titre"=>"Un choix de couleurs",
	"choix_couleur_explication"=>"Permet de choisir une couleurs parmis plusieurs.",
	
	
);

?>
