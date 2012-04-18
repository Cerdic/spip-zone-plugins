<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/faq-manuelsite?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// F
	'forum' => 'Les forums sont activés par défaut sur vos articles @complement@ ; ils sont désactivables au cas par cas... Les visiteurs peuvent donc réagir à vos articles... Vous serez prévenu par mail à chaque fois qu’un message est posté sur un de vos articles. Petit revers de médaille : les spams qui ne sont pas toujours évident à repérer et que vous devrez parfois gérer manuellement. Pour traiter un message de forum (le supprimer s’il ne vous plait pas ou le signaler comme spam si c’en est un) :
-* Dans le site public, sur la page de l’article, si vous êtes identifiés, il y a 2 boutons "Supprimer ce Message" ou "SPAM"
-* Dans l’espace privé, via le menu Activité / Suivre les Forums', # NEW
	'forum_q' => 'Ako riadiť diskusné fóra?',

	// I
	'img' => 'Il n’y a pas de « bonne » taille pour afficher une image dans un article. En tout cas, inutile d’envoyer une image de 3000 pixels de large, aucun écran ne pourra l’afficher dans son intégralité ! Sauf si le document est destiné à l’impression.
-* Si l’image doit être intégrée au texte d’un article, tout dépend de son contenu : si c’est un portrait, une hauteur de 200px devrait suffire sinon gare aux rides ; si c’est un beau paysage, on peut aller jusqu’à {{@largeur_max@}} pixels max de large.
-* Si l’image est prévue pour le porte-folio d’un article, ne pas dépasser 1000 pixels de large ou 600 pixels de haut.

{Attention, le poids max à ne pas dépasser est de 150M sans quoi le téléchargement sera refusé}.', # NEW
	'img_nombre' => 'Do článku sa dá do článku poslať viac fotiek jedným klikom:
-* Vybrané fotky skopírujte do priečinka na svojom pevnom disku
-* Upravte ich veľkosť
-* Vytvorte z nich súbor zip
-* Tento súbor zip pripojte k článku. Na konci sťahovania sa vás systém spýta, čo chcete so súborom urobiť, napríklad môžete všetky fotky vložiť do portfólia.',
	'img_nombre_q' => 'Ako ľahko napĺňať portfólio?',
	'img_ou_doc' => 'Na vkladanie obrázkov do textu článku používa najmä tag <code><imgXX|center>.</code> Ak však chcete zobraziť pod obrázkom jeho nadpis alebo popis, použite tag <code><docXX|center>.</code>',
	'img_ou_doc_q' => '<code><imgXX> alebo <docXX>?</code>',
	'img_q' => 'Akú veľkosť by mala mať moja fotka?',

	// S
	'son' => 'Préparer votre son au format mp3 en mono avec une fréquence de 11 ou 22 kHz et un bitrate (taux de compression) de 64kbps (ou plus si vous désirez une qualité supérieure).
	
Associer le fichier mp3 à votre article comme pour une image et lui donner un titre et éventuellement une description et un crédit. Enfin placer dans le corps de votre article à l’endroit souhaité <code><docXX|center|player></code>. Un lecteur flash apparaîtra alors dans votre site public pour permettre au visiteur de lancer le son. 
_ {Attention, la taille max d’un fichier est de 150M, soit environ une durée de 225 minutes}', # NEW
	'son_audacity' => 'Pour travailler un fichier audio, vous pouvez utiliser le logiciel Audacity (Mac, Windows, Linux) téléchargeable par ici [->http://audacity.sourceforge.net/]. Quelques astuces :
-* Après installation du logiciel, vous aurez besoin aussi de la librairie lame pour l’encodage mp3 [->http://audacity.sourceforge.net/help/faq?s=install&item=lame-mp3].
-* Pour passer le fichier en mono : Menu {Pistes/Piste stéréo vers mono}
-* Pour créer le fichier mp3 : Menu {Fichier/Exporter}
-* Pour régler le bitrate : Menu {Fichier/Exporter/Options/Qualité}', # NEW
	'son_audacity_q' => 'Ako pripraviť súbor so zvukom?',
	'son_q' => 'Ako pridať k článku súbor so zvukom?',

	// T
	'thumbsites' => 'V rubrike {{@rubrique@}} kliknite na tlačidlo "Odkázať na stránku". Zadajte adresu stránky a potvrďte stránku; systém sa pokúsi získať názov, popis a obrázok stránky online.  Ak je to potrebné, upravte názov a popis. Ak obrázok nebol vytvorený automaticky, urobte snímku obrazovky a vložte ju ako logo stránky s rozmermi 120 x 90 pixelov.',
	'thumbsites_q' => 'Ako vytvoriť odkaz na (inú) stránku na stránke s odkazmi?',
	'trier' => 'Čísla pred názvami článkov/rubrík/ dokumentov umožňujúce manipulovať s poradím, v akom sa zobrazí. Syntax tvorí číslo,  za ktorým nasleduje bodka a medzera',
	'trier_q' => 'Ako nastaviť správne poradie zobrazenia článkov, rubrík alebo pripojených súborov?',

	// V
	'video_320x240' => 'Préparer votre vidéo au format flv (streaming flash) en 320x240 pixels avec un bitrate (taux de compression) de 400kbps et un son en mono/64kbps. Pour convertir un fichier vidéo, vous pouvez utiliser le logiciel avidemux (Mac, Windows, Linux) téléchargeable par ici [->http://www.avidemux.org/]. 

Associer le fichier créé à votre article comme un document joint, lui donner un titre, éventuellement une description et un crédit, et une taille (largeur 320, hauteur 240). Enfin placer dans le corps de votre article à l’endroit souhaité <code><docXX|center|video></code>. Un lecteur flash apparaîtra alors dans votre site public pour permettre au visiteur de lancer la vidéo.
_ {Attention, la taille max d’un fichier est de 150M, soit environ une durée de 37.5 minutes}', # NEW
	'video_320x240_q' => 'Ako pridať video k článku?',
	'video_dist' => 'Ak sa vaše video nachádza na stránke DailyMotion, YouTube alebo Viméo  a máte ho na novej karte vášho prehliadača, prejdite na stránku, kde sa video nachádza a skopírujte internetovú adresu videa. Na stránke úprav svojho článku kliknite na tlačidlo "Pridať video" a prilepte jeho internetovú adresu. Potom vložte do textu článku <code><videoXX|center></code>',
	'video_dist_q' => 'Ako k článku pridať video z dailymotion (youtube, atď.)?'
);

?>
