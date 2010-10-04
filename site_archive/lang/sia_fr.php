<?php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'sia' => 'Sitre Archive',
	'sia_titre' => 'Site Archive',
	
	'comportement_' => 'Comportement : ',
	'config_sia' => 'Configuration compl&#233;mentaire du plugin Site Archive (SiA)',
	'logo_plugin' => 'Logo du plugin',
	
	'complements_' => 'Configuration compl&#233;mentaire : ',

	'strict_mode_label' => 'Mode strict',
	'strict_mode_aide' => 'Par d&#233;faut, le script d&#39;archive ne
		tient pas compte des erreurs renvoy&#233;es par le serveur.
		Cette erreur est traduite par le code de valeur 8 renvoy&#233;
		par wget. Activez ce mode strict pour forcer le script
		d&#39;archive &#224; tenir compte de ce type d&#39;erreur.',

	'simulation_mode_label' => 'Mode simulation',
	'simulation_mode_aide' => 'Ce mode valide le fichier verrou, mais ne lance
		pas le script d&#39;archivage via la commmande batch.<br />
		En validant cette option, et en rechargeant la page contenant au
		moins un lien d&#39;appel sur un archivage (un mod&#232;le), 
		vous pouvez lancer le script d&#39;archivage en vous pla&#231;ant
		dans la racine de votre site via votre terminal,
		puis en lan&#231;ant le script ./plugins/site_archive/bin/site_archive.sh<br />
		D&#233;tail: si vous avez mis en place plusieurs liens d&#39;archive
		et si vous d&#233;sirez g&#233;n&#233;rer ces archives dans ce mode, vous devez appelez
		le script d&#39;archive manuellement autant de fois qu&#39;il y a de liens
		(autant de fois qu&#39;il y a de fichier &#60;votre-archive&#62;.todo dans tmp/).<br />
		Pour supprimer le job, supprimez simplement le fichier &#60;job&#62;.todo
		et son lock du r&#233;pertoire tmp/ de votre site.',

	'random_wait_label' => 'Acc&#232;s al&#233;atoires : ',
	'random_wait_aide' => 'Attente al&#233;atoire entre chaque requ&#234;te 
		afin d&#39;&#233;viter de surcharger le serveur. ',

	'level_label_' => 'Profondeur (level) : ',
	'level_aide' => 'Profondeur maximale pour la r&#233;cursion.
		Par d&#233;faut, la profondeur maximale est fix&#233;e &#224; 5. ',

	'user_agent_label_' => 'Agent utilisateur (user-agent) : ',
	'user_agent_aide' => 'Lorsque le visiteur se connecte sur un
		site web, il signe sa visite dans les en-t&#234;tes de
		sa requ&#234;te. Le serveur web peut donc identifier le type
		de visiteur gr&#226;ce &#224; ce user-agent.<br />
		Cette option vous permet de modifier la valeur de &#171; User-Agent &#187;.
		Par d&#233;faut: Wget/&#60;version&#62;'
);
