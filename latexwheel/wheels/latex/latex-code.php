<?php
function tw_echappe_code_latex($code){
	return '<html>'.echappe_html($code[0],'latex').'</html>';	
}

function tw_code_latex($code){
	if (count($code)>1)
		return echappe_html("<html>\begin{minted}{".strtolower($code[2])."}\n",'latex');
	else
		return 	echappe_html("\n\end{minted}</html>",'latex');
}

function tw_cadre_latex($code){
	if (count($code)>1)
		return echappe_html("<html>\begin{minted}[linenos]{".strtolower($code[2])."}\n",'latex');
	else
		return echappe_html("\n\end{minted}</html>",'latex');
}
?>