<?php
/**
 * Plugin GeneSpip pour Spip 2.0
 * Licence GPL
 * Repris du plugins Pays
 *
 */

function peupler_base_genespip() {

	sql_insertq_multi('spip_genespip_type_evenements', array(
		array('id_type_evenement'=>'1','type_evenement'=>'naissance','clair_evenement'=>'<multi>[fr]Naissance<multi>'),
		array('id_type_evenement'=>'2','type_evenement'=>'dece','clair_evenement'=>'<multi>[fr]D&eacute;c&eacute;s</multi>'),
		array('id_type_evenement'=>'3','type_evenement'=>'mariage','clair_evenement'=>'<multi>[fr]Mariage</multi>'),
		)
	);
}
?>
