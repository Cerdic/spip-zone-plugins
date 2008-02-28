<?php

//seul un secteur peut etre un blog
$types['secteur'][] = 'blog';

//les sous-rubriques d'un blog s'appellent des categories
$types['rubrique']['blog'] = 'categorie';

// les articles de blog sont des billets
$types['article']['blog'] = 'billet';
$types['article']['categorie'] = 'billet';

?>