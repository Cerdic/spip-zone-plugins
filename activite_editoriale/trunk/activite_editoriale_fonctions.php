<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// obtenir l'age de la rubrique en nombre de jour
function age_rubrique($date_str) {
	return intval((time() - strtotime($date_str)) / (60 * 60 * 24));
}
