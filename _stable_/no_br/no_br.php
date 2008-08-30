<?php
// Filtre typographique exposants pour langue francaise, par Vincent Ramos
// <spip_dev AD kailaasa PVNCTVM net>, sous licence GNU/GPL.
// Ce filtre emprunte les expressions régulières publiees par Raphaël Meyssen
// sur <http://www.spip-contrib.net/Filtre-typographique-exposants> et 
// ne fonctionne que pour le francais.
// Ce filtre est aussi utilisé dans le plugin tweaks.


function no_br_interdire_br($texte){

	$texte = preg_replace("/\n_ +/S", "\n\n", $texte);
	$texte = preg_replace("/<br\ ?\\/?>/", "\n\n", $texte);
	return $texte;
}
?>