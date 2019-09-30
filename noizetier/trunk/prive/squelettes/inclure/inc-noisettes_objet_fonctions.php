<?php

// En principe superflu car le formulaire editer_composition_objet présent sur la page inclus déjà ce fichier,
// mais on ne sait jamais
if (test_plugin_actif('compositions')) {
	include_spip('inc/compositions');
}