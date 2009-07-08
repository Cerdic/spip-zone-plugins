<?php

$GLOBALS['i18n_gallery_fr'] = array(

// C
'chemins_appli' => 'Chemins des applications',
'chemin_gallery' => 'Chemin du r&eacute;pertoire d\'installation de Gallery 2',
'chemin_gallery_explication' => 'en principe un sous-r&eacute;pertoire du SPIP: "/gallery/" 
        ou "/mon/repertoire/spip/gallery/"',
'chemin_spip' => 'Chemin du SPIP',
'chemin_spip_explication' => 'si votre SPIP est install&eacute; &agrave; la racine du serveur: "/", 
        sinon son chemin complet: "/mon/repertoire/spip/"',
'configuration_g2' => 'Configuration du plugin Gallery 2',

// D
'descriptif_configuration' => 'Parametres de configuration du connecteur SPIP pour Gallery 2',

// E
'erreur_insertion' => 'Erreur lors de l\'insertion de l\'item',

// F
'fichier_embed_pas_trouve' => 'Le fichier de Gallery embed.php &agrave; inclure pour connecter SPIP n\'a pas &eacute;t&eacute; trouv&eacute;
        Verifiez son chemin dans la page de configuration CFG...',

// G
'gimg_modele' => 'Modele gimg',
'gimg_modele_presentation' => 'ce plugin fourni un modele &lt;gimg|item=XXX&gt; qui permet d\'afficher les image de Gallery 2
        avec le m&ecirc;me rendu que les balises &lt;imgYYY&gt; de SPIP (|left ou |right fonctionnent &agrave; l\'identique)
        ou comme une simple balise HTML &lt;img src="mon-image.jpg"...&gt; si on passe le param&egrave;tre 
        |affiche=brut (&lt;gimg|item=XXX|affiche=brut&gt;). 
        <br/>Par d&eacute;faut la taille de l\'image sera celle configur&eacute;e ici  
        sauf si on ajoute un param&egrave;tre |taille=ZZZ (valeur en pixel)',
'gimg_taille' => 'Taille des images par d&eacute;faut (en pixels)',
'gimg_taille_explication' => 'sera remplac&eacute; par le parametre {taille=...} du mod&egrave;le si il en existe un', 
'g2photo_modele' => 'Modele gphoto',
'g2photo_modele_presentation' => 'ce plugin fourni un modele &lt;gphoto...&gt; qui permet 
        d\'afficher les vignettes des photos stock&eacute;es dans Gallery 2
        avec un lien qui les ouvre dans la page SPIP de Gallery (|left ou |right fonctionnent &agrave; l\'identique). 
        <br/>En principe ce mod&egrave;le n&eacute;cessite un parametre {item=1} ou {item=1:2:3} 
        pour afficher la(les) photo(s) correspondante(s) 
        (dans le cas de plusieurs items, on peut ajouter un parametre {sep=...du code html ou autre...}: 
        le code "sep" est ins&eacute;r&eacute; entre les diff&eacute;rentes photos)
        <br/>Le parametre {item=...} peut &ecirc;tre remplac&eacute; par un parametre 
        {dernieres=X} qui permet d\'afficher les X derni&egrave;res photos.
        <br/>Si on ne passe aucun de ces 2 param&egrave;tres on affiche une photo au hazard.
        <br/>Dans tous les cas on transmet un &eacute;ventuel param&egrave;tre {taille=XXX}: 
        qui surclasse la taille par d&eacute;faut d&eacute;finie ici.
        <br/>Le lien sur l\'image cr&eacute;&eacute; par le mod&egrave;le peut &ecirc;tre supprim&eacute; si on passe
        un param&egrave;tre {lien=non} ou remplac&eacute; par un autre url avec {lien=http://trux.tld} ou remplac&eacute;
        par un lien sur l\image elle m&ecirc;me avec {lien=img} comme le fait SPIP dans le port-folio.',
'g2photo_elements' => 'El&eacute;ments &agrave; afficher avec les vignettes des photos (ecrase le r&eacute;glage du module Image Block de Gallery)',
'g2photo_taille' => 'Taille des vignettes par d&eacute;faut (en pixels)',
'g2photo_taille_explication' => 'sera remplac&eacute; par le parametre {taille=...} 
        pass&eacute; au mod&egrave;le si il en existe un', 
'g2photo_elem_titre'=> 'titre',
'g2photo_elem_date'=> 'date',
'g2photo_elem_nbvues'=> 'nombre de visualisations',
'g2photo_elem_proprio'=> 'propri&eacute;taire',
'g2photo_elem_pleinetaille'=> 'affichage pleine taille',


// H
'habillage' => 'Habillage de Gallery 2 dans le squelette',

// I
'integrer_page_spip' => 'Int&eacute;grer le contenu de Gallery dans le DIV #page de SPIP?',
'integrer_page_spip_explication' => 'Le contenu des pages SPIP est contenu dans un DIV (avec l\'id #page) qui restreint leur largeur,
         au contraire de Gallery 2 qui utilise dans la majorit&eacute; de ses th&egrave;mes
         100% de la largeur. Si l\'option est &agrave; "oui", le contenu de Gallery
         sera int&eacute;gr&eacute; dans SPIP avec la largeur de #page. 
         <br/>Ce param&eacute;trage n\'est appliqu&eacute; que dans la mesure o&ugrave; vous ne modifiez
         pas le squelette "gallerie.html" fourni avec ce plugin!',
'integrer_affmasq_barre' => 'Int&eacute;grer un javascript qui permet d\'afficher/masquer la barre d\'outils lat&eacute;rale de Gallery 2?',                                     

// M
'masquer_gallery' => 'Masquer l\'en-t&ecirc;te et le pied de page de Gallery',

// N
'non' => 'non',

// O
'oui' => 'oui',

// P
'plus_details' => '+ de d&eacute;tails',

// S
'squelette_gallery' => 'Le nom du fichier de squelette qui int&egrave;gre Gallery (sans l\'extension .html)',
'squelette_gallery_explication' => 'ce plugin fourni un fichier "gallerie.html" mais vous pouvez souhaiter le renommer ou en utiliser un autre...',

// T
'titre_gallery' => 'Connecteur Gallery 2',

// V
'voir_page_tests' => 'Voir le squelette de test de ces mod&egrave;les'


);
?>
