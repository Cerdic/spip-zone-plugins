<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/manuel_site/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// F
	'forum' => 'Les forums sont activés par défaut sur vos articles @complement@ ; ils sont désactivables au cas par cas... Les visiteurs peuvent donc réagir à vos articles... Vous serez prévenu par mail à chaque fois qu’un message est posté sur un de vos articles. Petit revers de médaille : les spams qui ne sont pas toujours évident à repérer et que vous devrez parfois gérer manuellement. Pour traiter un message de forum (le supprimer s’il ne vous plait pas ou le signaler comme spam si c’en est un) :
-* Dans le site public, sur la page de l’article, si vous êtes identifiés, il y a 2 boutons "Supprimer ce Message" ou "SPAM"
-* Dans l’espace privé, via le menu Activité / Suivre les Forums',
	'forum_q' => 'Comment gérer les forums ?',

	// I
	'img' => 'Il n’y a pas de « bonne » taille pour afficher une image dans un article. En tout cas, inutile d’envoyer une image de 3000 pixels de large, aucun écran ne pourra l’afficher dans son intégralité ! Sauf si le document est destiné à l’impression.
-* Si l’image doit être intégrée au texte d’un article, tout dépend de son contenu : si c’est un portrait, une hauteur de 200px devrait suffire sinon gare aux rides ; si c’est un beau paysage, on peut aller jusqu’à {{@largeur_max@}} pixels max de large.
-* Si l’image est prévue pour le porte-folio d’un article, ne pas dépasser 1000 pixels de large ou 600 pixels de haut.

{Attention, le poids max à ne pas dépasser est de {{@poids_max@}}Mo sans quoi le téléchargement sera refusé}.',
	'img_nombre' => 'Il est possible d’envoyer en un clic plusieurs photos dans un article :
-* Copier les photos choisies dans un dossier de votre disque dur
-* Les redimensionner à la bonne taille
-* Les insérer dans un fichier zip
-* Joindre ce fichier zip à l’article. A la fin du téléchargement, il vous est demandé ce que vous voulez faire du fichier, vous pouvez alors déposer par exemple toutes les photos dans le port-folio.',
	'img_nombre_q' => 'Comment remplir facilement un portfolio ?',
	'img_ou_doc' => 'On utilise majoritairement le tag <code><imgXX|center></code> pour insérer une image dans un texte d’article. Mais si on veut aussi afficher le titre ou la description sous l’image, il faut utiliser <code><docXX|center></code>.',
	'img_ou_doc_q' => '<code><imgXX> ou <docXX></code> ?',
	'img_q' => 'Quelle taille doit faire ma photo ?',

	// S
	'son' => 'Préparer votre son au format mp3 en mono avec une fréquence de 11 ou 22 kHz et un bitrate (taux de compression) de 64kbps (ou plus si vous désirez une qualité supérieure).
	
Associer le fichier mp3 à votre article comme pour une image et lui donner un titre et éventuellement une description et un crédit. Enfin placer dans le corps de votre article à l’endroit souhaité <code><docXX|center|player></code>. Un lecteur flash apparaîtra alors dans votre site public pour permettre au visiteur de lancer le son. 
_ {Attention, la taille max d’un fichier est de 150M, soit environ une durée de 225 minutes}',
	'son_audacity' => 'Pour travailler un fichier audio, vous pouvez utiliser le logiciel Audacity (Mac, Windows, Linux) téléchargeable par ici [->http://audacity.sourceforge.net/]. Quelques astuces :
-* Après installation du logiciel, vous aurez besoin aussi de la librairie lame pour l’encodage mp3 [->http://audacity.sourceforge.net/help/faq?s=install&item=lame-mp3].
-* Pour passer le fichier en mono : Menu {Pistes/Piste stéréo vers mono}
-* Pour créer le fichier mp3 : Menu {Fichier/Exporter}
-* Pour régler le bitrate : Menu {Fichier/Exporter/Options/Qualité}',
	'son_audacity_q' => 'Comment préparer un son ?',
	'son_q' => 'Comment ajouter un son à un article ?',

	// T
	'thumbsites' => 'Cliquer sur « Référencer un site » dans la rubrique {{@rubrique@}}. Renseigner l’url du site, et valider, le système va essayer de récupérer le titre, la description et une vignette du site en ligne.  Corriger le titre et la description si nécessaire. Si la vignette n’est pas générée automatiquement, faire une capture écran et l’insérer comme logo du site en 120x90 pixels.',
	'thumbsites_q' => 'Comment référencer un site dans la page de liens ?',
	'trier' => 'Les numéros devant les titres des articles / rubriques / documents, permettent de gérer leur ordre d’affichage. La syntaxe est un nombre suivi d’un point et d’un espace',
	'trier_q' => 'Comment gérer l’ordre d’affichage des articles / rubriques / documents ?',

	// V
	'video_320x240' => 'Préparer votre vidéo au format flv (streaming flash) en 320x240 pixels avec un bitrate (taux de compression) de 400kbps et un son en mono/64kbps. Pour convertir un fichier vidéo, vous pouvez utiliser le logiciel avidemux (Mac, Windows, Linux) téléchargeable par ici [->http://www.avidemux.org/]. 

Associer le fichier créé à votre article comme un document joint, lui donner un titre, éventuellement une description et un crédit, et une taille (largeur 320, hauteur 240). Enfin placer dans le corps de votre article à l’endroit souhaité <code><docXX|center|video></code>. Un lecteur flash apparaîtra alors dans votre site public pour permettre au visiteur de lancer la vidéo.
_ {Attention, la taille max d’un fichier est de 150M, soit environ une durée de 37.5 minutes}',
	'video_320x240_q' => 'Comment ajouter une vidéo à un article ?',
	'video_dist' => 'Si votre vidéo est hébergée sur DailyMotion, YouTube ou Viméo, dans un nouvel onglet de votre navigateur, aller sur la page de visionnage de la vidéo, et copier l’url. Dans la page d’édition de votre article cliquer sur "Ajouter une vidéo" et coller l’url. Insérer alors dans la zone texte de l’article <code><videoXX|center></code>',
	'video_dist_q' => 'Comment ajouter une vidéo dailymotion (youtube,...) à un article ?'
);

?>
