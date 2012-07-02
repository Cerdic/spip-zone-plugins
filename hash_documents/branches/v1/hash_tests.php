<?php

/*

## tests pour hasher les documents
@require 'ecrire/inc_version.php';
include_spip('hash_fonctions');

# hasher_adresser_document
foreach (array(
	'IMG/mp3/toto.mp3' => 'mp3/1/c/9/toto.mp3',
	'mp3/toto.mp3' => 'mp3/1/c/9/toto.mp3',
	'IMG/mp3/a/b/c/toto.mp3' => false,
	'x' => false,
) as $s => $r) {
	if (($a = hasher_adresser_document($s)) !== $r)
		echo 'hasher_adresser_document: '.var_export($s, true).' => '.var_export($a, true).' != '.var_export($r, true)."\n";
}

#var_dump(hasher_deplacer_document(1));

spip_timer('hash');
var_dump(count(hasher_deplacer_n_documents(10000, true)));
var_dump(spip_timer('hash'));
*/
?>
