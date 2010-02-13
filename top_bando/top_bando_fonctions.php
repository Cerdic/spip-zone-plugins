<?php
// supprimer le # dans une chaîne (cf couleurs pour image_typo
function top_bando_suprime_diese($txt) {
    return str_replace('#', '', $txt);
}

?>