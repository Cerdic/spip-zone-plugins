<?php

$GLOBALS[$GLOBALS['idx_lang']] = array(
	'titre_page' => 'Configuration des habillages de votre site',
	'gros_titre' => 'Configuration des habillages de votre site',
   	'help' => 'Choisissez l\'habillage plus validez. Pour l\'habillage de l\'espace priv&eacute;, il faut cliquer deux fois sur "valider" pour voir le nouveau jeu d\'ic&ocirc;nes remplacer l\'ancien. Sinon vous verrez le nouveau jeu d\'ic&ocirc;nes au prochain clic dans l\'espace priv&eacute;.

{{Pensez &agrave; bien cocher "Revenir &agrave; l\'habillage d\'origine" pour effacer tout effet du plugin sur vos fichiers.}}

{{Ajouter ou enlever des th&egrave;mes}}<br />
- Pour ajouter des th&egrave;mes pour l\'espace priv&eacute; : vous devez mettre vos icones ayant le m&ecirc;me nom et la m&ecirc;me extension que les images du dossier d\'origine ecrire/img_pack dans un dossier nomm&eacute; "img_pack", et mettre ce dossier dans un autre qui porte le nom de votre th&egrave;me. Il faut ensuite mettre ce dossier dans le r&eacute;pertoire "habillages/prive/themes" (dans le repertoire "themes" contenu dans le repertoire "prive" lui-meme contenu dans le repertoire du plugin; "habillages" est le nom d\'origine du plugin si vous ne l\'avez pas renomm&eacute;). {{Attention, vous ne pouvez rajouter pour l\'instant que des images peronnalis&eacute;es du dossier natif ecrire/img_pack.}}<br /><br />
- Pour ajouter des th&egrave;mes (des squelettes) pour l\'espace public : vous devez mettre vos squelettes dans un dossier nomm&eacute; "squelettes", et mettre ce dossier dans un autre qui porte le nom de votre th&agrave;me. Il faut ensuite mettre ce dossier dans le r&eacute;pertoire "habillages/public/themes" (dans le repertoire "themes" contenu dans le repertoire "public" lui-meme contenu dans le repertoire du plugin; "habillages" est le nom d\'origine du plugin si vous ne l\'avez pas renomm&eacute;).

- Tous les th&egrave;mes incluent un fichier theme.xml &agrave; la racine de leur dossier, qui donne le nom du th&egrave;me, son auteur, etc. Copiez en un existant et rempacer juste les mentions entre les balises pour le personnaliser.',
'texte_inc_config' => 'Les actions de cette page vont modifier votre fichier <i>ecrire/mes_options.php</i>. Il vous est conseill&eacute; de faire une sauvegarde de ce fichier tant que le plugin "Habillages" n\'est pas stabilis&eacute;.<P>Si vous n\'avez pas ouvert des droits d\'&eacute;criture sur le fichier ecrire/mes_options.php, ce n\'est pas la peine d\'utiliser cette page.</P><P>Nous vous recommandons de ne pas toucher &agrave; cette page si vous ne savez pas ce qu\'est le fichier <i>ecrire/mes_options.php</i> ni &agrave; quoi il sert.</P><P align="justify"><B>Plus
	g&eacute;n&eacute;ralement, il est fortement conseill&eacute;
	de laisser la charge de ces pages au webmestre principal de votre site.</B>',
'titre_habillage_prive' => 'HABILLAGE DE L\'ESPACE PRIVE',
'titre_habillage_public' => 'HABILLAGE DE L\'ESPACE PUBLIC (squelette)',)

?>
