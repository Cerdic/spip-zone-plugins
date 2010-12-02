<?php 



if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'liste_auteurs'=>'Voir la liste des auteurs',
	'auteurs_introduction'=>'Vous allez pouvoir rechercher, classer et modifier des auteurs.',
	'bouton_auteurs'=>'Liste des auteurs',
	'par_id_auteur'=>'ID',
	'par_nom'=>'Nom',
	'par_email'=>'Email',
	'gestion_statut_config'=>"Gestion des statuts d'auteurs",
	'gestion_statut'=>"Gestion des statuts personnalisés d'auteurs",
	'gestion_statut_introduction'=>"Gérez vos statuts d'auteur a votre bon vouloir",
	'nouveau_statut_code'=>'Code de statut :',
	'nouveau_statut_code_aide'=>'sur le format Xtexte ou X est un chiffre et texte un mot sans espace, exemple : 4membre , 7client',
	'nouveau_statut_libelle'=>'Libell&eacute; de statut :',
	'nouveau_statut_libelle_aide'=>'Ecrivez un libell&eacute; pour mieux comprendre le code saisi ci-dessus (les champs "multi" sont possibles)',
	'statut_spip'=>'<b>Liste des statuts r&eacute;serv&eacute; de spip :</b><br/> 0minirezo (Admin), 1comite (R&eacute;dacteur), 5poubelle, 6forum (Visiteur)',
	'statut_personnalise'=>'<b>Liste des statuts personnalis&eacute;s :</b>',
	'aucun_statut'=>"Il n'y a pas encore de statut personnalis&eacute;",
	'confirmer_suppression'=>'Vous allez supprimer !\nVoulez vous continuer ?'

);



$statut=statut_auteurs_get_statuts();
foreach ($statut as $code=>$libelle) {
	// on a ainsi un libelle de langue si on a utilisé un multi 
	$GLOBALS[$GLOBALS['idx_lang']][$code]=extraire_multi($libelle,$GLOBALS['idx_lang']);
}	
	

	


?>