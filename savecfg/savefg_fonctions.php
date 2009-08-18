<?php
function savefg_find_infos_fond($fond, $etat='tout') {
	include_spip('inc/cfg');
	$tmp = new cfg($fond);
	if ($tmp->autoriser()){
		// titre
		if (!$titre = $tmp->form->param['titre'])
			$titre = $fond;
	}
	if ($etat == 'tout')
		return $titre.' ('.$fond.')';
	else
		return $titre;
}
?>