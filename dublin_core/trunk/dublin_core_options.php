<?php

function dublin_core() {
        if ($GLOBALS['page']['contexte']['id_article']>0)
                echo recuperer_fond('dublin_core_article', array('id_article'=>$GLOBALS['page']['contexte']['id_article']));
}

