<?php
/* insert le js externe pour Simile Timeline dans le <head>
 *
 *  Agenda Timeline par Kent1 (code inspiré de Fil et Toggg :-) )
 */

// Le pipeline affichage_final, execute a chaque hit sur toute la page
function Agendatimeline_affichage_final($page) {

    // sinon regarder rapidement si la page a des classes widget
	if (!strpos($page, 'class="timeline"'))
	return $page;

	if (strpos($page, 'class="timeline"'))
        $page = Agendatimeline_preparer_page($page);
	return $page;
}

function Agendatimeline_preparer_page($page) {

    $jsFile = find_in_path('js/timeline-api.js');

    $incHead = <<<EOH
<script src="{$jsFile}" type="text/javascript"></script>
EOH;

    return substr_replace($page, $incHead, strpos($page, '</head>'), 0);
}

?>