<?php

include_spip('inc/futilitaire');

function fabrique_spipad_post_creation($data, $destination_plugin, $destination_ancien_plugin) {

	// charger le Futilitaire
	$futil = new Futilitaire($data, $destination_plugin, $destination_ancien_plugin);

	// deplacer les fichiers crees, dont celui-ci
	$futil->deplacer_fichiers(array(
		'fabrique_spipad_post_creation.php', 
		'saisies/thematique.html',
		'saisies-vues/thematique.html',
		'monrep/monfichier.txt',
		
	));

	// ajouter du code dans lang/spipad.php
$lignes =
<<<EOF

// Pascal
'pertinencepapapapa' => 'Pertinencepapapap',
EOF;

	$futil->ajouter_lignes('lang/spipad_fr.php', -3, 0, fabrique_tabulations($lignes, 1));


	// prive/squelettes/contenu/configurer_spipad.html
$lignes =
<<<EOF

<fieldset><legend> <b>Paramètrage type d'annonce</b> </legend>
<label>Choisir : </label>

<select name="grpmot_type">
	<option value=""> Choisir le groupe de mots de référence </option>
<BOUCLE_secteurs(GROUPES_MOTS) {par titre}>
	<option value="#ID_GROUPE" [(#ENV{grpmot_type}|=={#ID_GROUPE}|?{selected="selected"})]>#TITRE </option>
</BOUCLE_secteurs>

</select>
</fieldset>


EOF;

	$futil->ajouter_lignes('formulaires/configurer_spipad.html', 11, 5, $lignes);
}

?>