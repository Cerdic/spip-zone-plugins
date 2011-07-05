<?php

function tw_code_latex($code){
	if (count($code)>1)
		return "<html>\begin{minted}{".strtolower($code[2])."}\n";
	else
		return 	"\n\end{minted}</html>";
}

function tw_cadre_latex($code){
	if (count($code)>1)
		return "<html>\begin{minted}[linenos]{".strtolower($code[2])."}\n";
	else
		return 	"\n\end{minted}</html>";
}
?>