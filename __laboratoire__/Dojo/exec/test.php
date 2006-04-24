<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_test(){
	echo '
	<html>
	<head>
	<script src="../plugins/Dojo/img_pack/dojo.js" type="text/javascript"></script>
<script type="text/javascript">dojo.require("dojo.widget.Editor");</script>
	</head>
	<body>
	
	<textarea dojoType="Editor" name="editorContent"
    items="bold;italic;underline;strikethrough;">
    some content
</textarea>

<div dojoType="Editor" 
    items="bold;italic;underline;strikethrough;">
    some content
</div>

	</body>
	</html>	
	';
	
	
}

?>