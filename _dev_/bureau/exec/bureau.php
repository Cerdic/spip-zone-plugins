<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_bureau_dist() {
	bureau_args();
}

function bureau_args() {
	bureau();
}

/* 

Le bureau est composé de deux éléments principaux :
---------------------------------------------------

- la barre, contenant les menus, la barre des tâches et une petite zone info
- le bureau, contenant les fenêtres.

Liste des ID utilisé par le bureau :
------------------------------------

- #barre : identifie la barre
- #barre-menu : identifie la zone de la barre recevant les menus
- #barre-tache : identifie la zone de la barre recevant les taches
- #barre-infos : identifie la zone de la barre recevant des petites informations
- #bureau : identifie le bureau

la barre est composé de trois éléments :

- les menus, à gauche
- la barre des tâches, au centre
- les infos, à droite

Les fenetres sont identifiées par un ID unique du type : 
- fenetre-5f9f0c26e6866843594d03b9edbdc128
cet identifiant ce retrouve dans la barre des tâches sous la forme suivante :
- tache-fenetre-5f9f0c26e6866843594d03b9edbdc128

Cela permet d'associer la fenêtre à la barre des tâches

Les pipelines :
---------------

Les pipelines permettent à d'autres plugin d'insérer des menus, fenetres et informations lors du chargement du bureau.
Ils sont au nombre de trois :

BUREAU_menus : permet d'ajouter/retirer/modifier les menus de la barre
BUREAU_infos : permet d'ajouter/retirer/modifier les informations de la barre
BUREAU_fenetres : permet d'ajouter/retirer/modifier les fentres du bureau

Construire un menu :
--------------------

Il faut utiliser la fonction "bureau_barre_menu()" et ajouter ce qu'elle renvoie dans le flux
Cette fonction prend deux arguments :
- le label : peut être un texte, ou une balise img
- le contenu: doit être un tableau de la forme "label" => url

exemple : 
bureau_barre_menu("mon menu",
	array(
		"Mon plugin" => generer_url_bureau("mon_plugin")
));

La fonction generer_url_bureau permet de rediriger vers le bureau en "zappant" l'interface privée standard de spip.
Les fichiers cibles doivent être dans le répertoire "bureau" de votre plugin et doivent respecter la norme suivante :
function bureau_mafonction_dist()
function bureau_mafonction_args()
function mafonction()


Construire une fenetre :
------------------------

Il faut utiliser la fonction "bureau_fenetre()" et ajouter ce qu'elle renvoie dans le flux
Cette fonction prend trois arguments :
- le titre : un texte, ou une balise html
- le contenu : ce que vous voulez
- le style : en utilisant la syntaxe css (width, height, éventuellement top et left pour "zapper" le placement des fenetres par le bureau)

exemple :
bureau_fenetre("le titre de ma fenetre","<center>la contenu de ma fenetre</center>","width:205px;height:100px;");

*/
function bureau() {
	include_spip('inc/bureau_presentation');
	include_spip('inc/texte');

	$menus = array();
	$infos = array();
	$fenetres = array();

	$bureau = charger_fonction('bureau_charge','inc');
	echo $bureau();

	// construction des menus
	echo bureau_debut_menu();

	$menus[] = bureau_barre_menu('<img src="'.find_in_path('images/spip.png').'" />', 
				array('Se déconnecter/normal' => generer_url_action("logout","logout=prive"),
					'Visiter/normal' => url_de_base()));

	$menus[] = bureau_barre_menu('Actions',
				array('Explorer/fenetre' => generer_url_ecrire("bureau_explorer")));

	$menus = pipeline('BUREAU_menus', array(
			'args'=>array('bureau'=>'bureau'),
			'data'=>$menus
			));

	foreach ($menus as $menu) echo $menu;

	echo bureau_fin_menu();



	// la zone infos de la barre
	echo bureau_debut_infos();

	$infos[] = '<div class="infos">'.typo($GLOBALS['visiteur_session']['nom']).'</div>';
	$infos[] = '<div class="infos jclock"></div>';

	$infos = pipeline('BUREAU_infos', array(
			'args'=>array('bureau'=>'bureau'),
			'data'=>$infos
			));

	foreach($infos as $info) echo $info;

	echo bureau_fin_infos();


	// construction des fenetres
	echo bureau_debut();

	$contenu = '<h2>Bienvenue sur le Bureau</h2>'
		.'<p><b>Avertissement</b> :<br />Ceci est une version expérimentale.</p>'
		.'<p><b>Fonctions</b> :<br />'
		.'<ul><li>Barre des tâches</li><li>Drag&drop</li><li>Redimensionnement des fenetres</li><li>Fermer/ouvrir/minimiser/maximiser une fenêtre</li>'
		.'<li>Le menu "Action" donne accés à l\'explorateur</li></ul></p>';
	$fenetres[] =  bureau_fenetre('Bonjour', $contenu);

	$fenetres = pipeline('BUREAU_fenetres', array(
			'args'=>array('bureau'=>'bureau'),
			'data'=>$fenetres
			));

	foreach ($fenetres as $fenetre) echo $fenetre;

	echo bureau_fin();
}
?>
