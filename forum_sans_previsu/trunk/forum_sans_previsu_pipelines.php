<?php
function forum_sans_previsu_formulaire_verifier($flux){
	if($flux['args']['form'] === 'forum'){

		unset($flux['data']['previsu']);
		unset($flux['data']['message_erreur']);
		}
	return $flux;
}
?>
