<?php

// modification de http://doc.spip.org/@barre_textarea
function cocomment_barre_textarea($texte, $uniq_id, $rows, $cols, $lang='') {
  $num_textarea = $uniq_id;
  include_spip('inc/layer'); // definit browser_barre

  $texte = entites_html($texte);
  if (!$GLOBALS['browser_barre'])
	return "<textarea name='texte' id='textarea_$num_textarea' rows='$rows' class='forml' cols='$cols'>$texte</textarea>";

  include_spip ('inc/barre');
  return afficher_barre("document.getElementById('textarea_$num_textarea')", true, $lang) .
	"
<textarea name='texte' rows='$rows' class='forml' cols='$cols'
id='textarea_$num_textarea'
onselect='storeCaret(this);'
onclick='storeCaret(this);'
onkeyup='storeCaret(this);'
ondblclick='storeCaret(this);'>$texte</textarea>";
}


?>
