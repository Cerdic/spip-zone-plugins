<?php
function sale($texte){
	// PRETRAITEMENTS
		$texte = str_replace("\n\r", "\r", $texte); // echapper au greedyness de preg_replace
		$texte = str_replace("\n", "\r", $texte);
		
		// virer les commentaires html (qui cachent souvent css et jajascript)
		$texte = preg_replace("/<!--.*-->/Uims", "", $texte);
		
		$texte = preg_replace("/<(script|style)\b.+?<\/\\1>/i", "", $texte);
		// <pre>...</pre>
		if (preg_match_all('@<pre>.*</pre>@Uims', $texte, $regs, PREG_SET_ORDER)) {
			foreach ($regs as $reg) {
				$texte = str_replace($reg[0], str_replace("\r", "<br>", $reg[0]), $texte);
			}
		}
		// itals
		$texte = preg_replace(",<(i|em)( [^>\r]*)?".">(.+)</\\1>,Uims", "{\\3}", $texte);
		
		// gras (pas de {{ pour eviter tout conflit avec {)
		$texte = preg_replace(",<(b|h[4-6])( [^>]*)?".">(.+)</\\1>,Uims", "@@b@@\\3@@/b@@", $texte);
		// liens
		$texte = preg_replace(",<a[ \t\n\r][^<>]*href=[^<>]*(http[^<>'\"]*)[^<>]*>(.*?)<\/a>,uims", "[\\2->\\1]", $texte);
		// intertitres
		$texte = preg_replace(",<(h[1-3])( [^>]*)?".">(.+)</\\1>,Uims", "\r{{{ \\3 }}}\r", $texte);
		// tableaux
		$texte = preg_replace(",<tr( [^>]*)?".">,Uims", "<br>\r", $texte);
		$texte = preg_replace(",<t[hd]( [^>]*)?".">,Uims", " | ", $texte);
		
		
		// POST TRAITEMENT
		$texte = str_replace("\r", "\n", $texte);
		
		// SUPPRIME LES TAGS
		if (eregi("<title.*>(.*)</title>", $texte, $regs))
			$titre = textebrut($regs[1]);
		$texte = textebrut($texte);
		
		// Suite tableaux
		$texte = preg_replace(",\n[| ]+\n,", "", $texte);
		$texte = preg_replace(",\n[|].+?[|].+?[|].+,", "\\0|\r", $texte);
		
		// retablir les gras
		$texte = preg_replace(",@@b@@(.*)@@/b@@,Uims","<b>\\1</b>",$texte);
		$texte = preg_replace(",@@/?b@@,"," ",$texte);
		$texte = preg_split("/\r\n|\n\r|\n|\r/", $texte);
		
return implode("\n\n",$texte);
}

?>