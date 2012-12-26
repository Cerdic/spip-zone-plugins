<?php

$GLOBALS['i18n_gallery_fr'] = array(
// A
'afficher_barre_laterale' => 'Afficher la barre laterale',

// C
'chemins_appli' => 'Chemins des applications',
'chemin_gallery' => 'Chemin du r&eacute;pertoire d\'installation de Gallery 2',
'chemin_gallery_explication' => 'en principe un sous-r&eacute;pertoire du SPIP: "/gallery/" 
        ou "/mon/repertoire/spip/gallery/"',
'chemin_spip' => 'Chemin du SPIP',
'chemin_spip_explication' => 'si votre SPIP est install&eacute; &agrave; la racine du serveur: "/", 
        sinon son chemin complet: "/mon/repertoire/spip/"',
'choix_squelette' => 'Choix du squelette pour int&eacute;grer Gallery',
'choix_squelette_integration' => 'Ce plugin propose plusieurs variantes pour le squelette qui "emballe"
        Gallery. Choisissez celle qui correspond le mieux &agrave; votre site',
'configuration_g2' => 'Configuration du plugin Gallery 2',

// D
'descriptif_configuration' => 'Parametres de configuration du connecteur SPIP pour Gallery 2',

// E
'erreur_insertion' => 'Erreur lors de l\'insertion de l\'item : v&eacute;rifiez que le plugin &quot;image block&quot; est activ&eacute;',
'explication_squelette_dist' => '("dist" correspond au squelette de SPIP par d&eacute;faut.)',
'erreur_getblock' => ' fonction getBlock() inconnue : v&eacute;rifiez que la version du plugin &quot;image block&quot; est &gt;= 1.1.9. et que la version de gallery2 &gt;= 2.3)',

// F
'fichier_embed_pas_trouve' => 'Le fichier de Gallery embed.php &agrave; inclure pour connecter SPIP n\'a pas &eacute;t&eacute; trouv&eacute;
        Verifiez son chemin dans la page de configuration CFG...',

// G
'gimg_modele' => 'Modele gimg',
'gimg_modele_presentation' => 'ce plugin fourni un modele <strong>&lt;gimg|item=XXX&gt;</strong> qui permet d\'afficher les image de Gallery 2
        avec le m&ecirc;me rendu que les balises <strong>&lt;imgYYY&gt;</strong> de SPIP 
        (<strong>|left</strong> ou <strong>|right</strong> fonctionnent &agrave; l\'identique)
        ou comme une simple balise HTML <strong>&lt;img src="mon-image.jpg"...&gt;</strong> si on passe le param&egrave;tre 
        <strong>|affiche=brut</strong> (<strong>&lt;gimg|item=XXX|affiche=brut&gt;</strong>). 
        <br/>Par d&eacute;faut la taille de l\'image sera celle configur&eacute;e ici  
        sauf si on ajoute un param&egrave;tre <strong>|taille=ZZZ</strong> (valeur en pixel)',
'gimg_taille' => 'Taille des images par d&eacute;faut (en pixels)',
'gimg_taille_explication' => 'sera remplac&eacute; par le parametre {taille=...} du mod&egrave;le si il en existe un', 
'g2photo_modele' => 'Modele gphoto',
'g2photo_modele_presentation' => 'ce plugin fourni un modele <strong>&lt;gphoto...&gt;</strong> qui permet 
        d\'afficher les vignettes des photos ou albums stock&eacute;es dans Gallery 2
        avec un lien qui les ouvre dans la page SPIP de Gallery (|left</strong> ou |right</strong> fonctionnent &agrave; l\'identique). 
        <br/>En principe ce mod&egrave;le n&eacute;cessite un parametre <strong>|item=1</strong> ou <strong>|item=1:2:3</strong> 
        pour afficher la(les) photo(s) ou album(s) correspondant(s) 
        <br/>Dans le cas de plusieurs items, on peut ajouter un parametre <strong>|sep=...du code html ou autre...</strong>: 
        le code "sep" est ins&eacute;r&eacute; entre les diff&eacute;rents photos/albums)
        <br/>Le parametre <strong>|item=...</strong> peut &ecirc;tre remplac&eacute; par un parametre 
        <strong>|dernieres=X</strong> qui permet d\'afficher les X derni&egrave;res photos. 
        Si on ajoute le param&egrave;tre <strong>|type=album</strong> ce sont les X derniers albums qui sont affich&eacute;s.
        <br/>Si on ne passe aucun de ces 2 param&egrave;tres on affiche une photo au hazard (ou un album si |type=album ).
        <br/>Dans tous les cas on transmet un &eacute;ventuel param&egrave;tre <strong>|taille=XXX</strong>: 
        qui surclasse la taille de la vignette par d&eacute;faut d&eacute;finie ici.
        <br/>Le lien sur l\'image cr&eacute;&eacute; par le mod&egrave;le peut &ecirc;tre supprim&eacute; si on passe
        un param&egrave;tre <strong>|lien=non</strong> ou remplac&eacute; par un autre url avec <strong>|lien=http://trux.tld</strong> ou remplac&eacute;
        par un lien sur l\'image elle m&ecirc;me avec <strong>|lien=img</strong> comme le fait SPIP dans le port-folio.
        <br/>Les &eacute;l&eacute;ments &agrave; afficher comme l&eacute;gende par d&eacute;faut sont ceux d&eacute;finis ici
        mais il est possible de ne rien afficher pour une photo en passant le param&egrave;tre <strong>|legende=non</strong>',
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
'masquer_barre_laterale' => 'Masquer la barre laterale',

// N
'non' => 'non',

// O
'oui' => 'oui',

// P
'plus_details' => '+ de d&eacute;tails',

// S
'squelette' => 'Squelette',
'squelette_gallery' => 'Le nom du fichier de squelette qui int&egrave;gre Gallery (sans l\'extension .html)',
'squelette_gallery_explication' => 'alternativement aux squelettes "gallerie.html" fournis dans ce plugin, 
        vous pouvez souhaiter utiliser un fichier de squelette avec un autre nom...',

// T
'test_modeles' => 'Test des mod&egrave;les du plugin Gallery 2',
'titre_gallery' => 'Connecteur Gallery 2',

// V
'voir_page_tests' => 'Voir le squelette de test de ces mod&egrave;les'


);
?>
