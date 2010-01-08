<?php

# dereferencer les URLs de facebook
# c'est possible s'il y a un lien dans le texte

function pivotsyndic_facebook($flux) {
	if (strstr($flux, 'facebook.com')
	AND $h = extraire_balise($flux, 'link')
	AND extraire_attribut($h, 'href') == 'http://www.facebook.com/'
	AND preg_match_all(',<entry>.*(<link.*/>).*</entry>,Uims', $flux, $regs, PREG_SET_ORDER)
	) {
		foreach ($regs as $reg) {

			if ($content = extraire_balise($reg[0], 'content')
			AND $a = extraire_balise(html_entity_decode($content), 'a')
			AND $u = extraire_attribut($a, 'href')) {
				if ($u1 = urldecode(parametre_url($u, 'u'))
				AND $u1 = preg_replace(',[?|&]utm_medium=.*,', '', $u1))
					$u = $u1;
				
				$item = str_replace($reg[1],
					'<link>'.$u.'</link>', $reg[0]);
				$flux = str_replace($reg[0], $item, $flux);
			}

		}
	}

	return $flux;
}
?>