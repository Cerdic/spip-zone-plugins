<?php
// This is a SPIP-ACS language file  --  Ceci est un fichier langue de SPIP-ACS

$GLOBALS[$GLOBALS['idx_lang']] = array(

'nom' => 'D&eacute;mocratie',
'description' => 'Gère la publication sur le site à partir d\'un système de notation.',
'info' => 'Les articles avec une note supérieure au seuil de publication sont affichés sur le site. Les autres sont accessibles sur la page des <a href="../?page=proposes">articles proposés</a> pour permettre leur évaluation.',
'help' => 'Ce composant facilite la gestion coopérative de l\'activité éditoriale d\'un site, en permettant un processus de validation collectif très simple à utiliser. Il peut fonctionner en mode "Démocratie", si l\'on ouvre le vote à tous les visiteurs, ou en mode "Oligarchie" en limitant le droit de vote aux visiteurs enregistrés, aux rédacteurs, et/ou aux administrateurs.<br /><br />Dans le mode "directe", les articles proposés sont non seulement les articles publiés, mais aussi les articles proposes: la publication dépend de la notation, sans exiger l\'intervention d\'un administrateur (qui peut quand même intervenir: les articles refuses ou mis à la poubelle ne sont pas proposés).<br /><br />La configuration du composant "Démocratie" et celle du plugin "notation" déterminent ensemble un mode de prise de décision de publication.<br /><br />.Fonctionne avec le plugin notation : ne JAMAIS désactiver "notation" avant d\'avoir désactivé ce composant.',

'SeuilPublic' => 'Note seuil de publication',
'Directe' => 'Activer le mode "directe"',
);
?>
