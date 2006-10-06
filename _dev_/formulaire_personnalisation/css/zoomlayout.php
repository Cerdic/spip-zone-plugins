<?php

@header ("content-type:text/css");

if($_COOKIE['spip_personnalisation_use'] =='zoom' OR $_COOKIE['spip_personnalisation_use'] =='zoominverse') { 
?>

/*  ------------------------------------------
/*  Disposition a l'ecran des blocs principaux
/*  ------------------------------------------ */
body {
	font-size:1.5em !important;
}

#page {
	width: 100% !important;
}

#conteneur { width: 100% !important; }

#conteneur #contenu {
	float: none !important;
	width: width: 100% !important;
}

#conteneur #navigation {
	float: none !important;
	width: width: 100% !important;
}

/* Blocs du contenu (c-a-d. la colonne principale) */
#contenu .contre-encart {
	float: none !important;
	width: 100% !important;
}

#contenu .encart {
	float: none !important;
	width: 100% !important;
}

<?php //} elseif($_COOKIE['usecss'] =='inverse' OR $_COOKIE['usecss'] =='zoominverse'){
} if ($_COOKIE['spip_personnalisation_use'] =='inverse' OR $_COOKIE['spip_personnalisation_use'] =='zoominverse'){
?>


/*  ------------------------------------------
/*  Correction des styles HTML par defaut
/*  ------------------------------------------ */
body {
	background: #000 !important;
	color: #CCC !important;
}


/*  Habillage general des menus de navigation
---------------------------------------------- */
.rubriques, .breves, .syndic, .forums, .divers {
	background: #333 !important;
	border: 1px solid #666 !important;
}

.menu-titre {
	border-bottom: 1px dotted #666 !important;
	background: #333 !important;
}

.cartouche .titre {
	color: #FFF !important; }

.texte { color: #FFF !important;}


.page_plan #contenu h2 {
	background: #333 !important;
	border: 1px solid #666 !important; }

legend { 
	background: #000 !important; /* Sinon, superposition dans MSIE */
}

.spip_cadre, .forml {
	background: #CCC !important; }

.spip_bouton input {
	background: #666 !important; }

/* Reponse du formulaire */
.reponse_formulaire { color: #F00 !important; }
.forum-chapo .forum-titre, .forum-chapo .forum-titre a { color: #CCC !important; }
ul .forum-chapo { background: #333 !important; }


#signatures td.signature-date {
	background: #333 !important;
}

#signatures td.signature-nom {
	background: #333 !important;
}

#signatures td.signature-message {
	background: #333 !important;
}

/*  ------------------------------------------
/*  Couleurs des liens
/*  ------------------------------------------ */
a { text-decoration: none; color: #CCC !important; }
a.spip_note { color: #F60 !important; } /* liens vers notes de bas de page */
a.spip_in { color: #F60 !important; } /* liens internes */
a.spip_out, a.spip_url { color: #6C0 !important; } /* liens sortants */
a.spip_glossaire { color: #09F !important; } /* liens vers encyclopedie */
.on { font-weight: bold; color: #FFF !important; } /* liens exposes */

<?php } ?>



