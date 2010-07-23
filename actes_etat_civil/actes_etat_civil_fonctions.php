<?php
/* generation des formulaires */
// pour mise en place des select
function etat_civil_generer_select($select, $nbre){
	$out = '';
	for ($i = 0; $i <= $nbre; $i++ ){
		if($select == $i) {
			$out .= '<option value="'.$i.'" selected="selected">'._T('etat_civil:lien_parent_'.$i).'</option>';
		} else {
			$out .= '<option value="'.$i.'">'._T('etat_civil:lien_parent_'.$i).'</option>';
		}
		$out .= "\n";
	}
	return $out;
}

// pour les boutons radio
function etat_civil_generer_radio($val_select, $num, $class){
	$out = '<input type="radio" class="radio '.$class.'" name="type_acte" id="type_acte_'.$num.'" value="'.$num.'"';
	if ($num == $val_select) $out .= ' checked="checked"';
	$out .= ' />';
	$out .= '<label for="type_acte_'.$num.'">'._T('etat_civil:acte_etat_civil_'.$num).'</label>';
	return $out;
}

// pour affichage chaines de langues
function en_clair($n, $chaine){
	return _T('etat_civil:'.$chaine.'_'.$n);
}
?>