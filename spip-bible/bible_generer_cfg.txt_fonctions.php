<?php
function bible_generer_cfg($i){
	$tableau_traduction = bible_tableau('traduction');
	$tableau_separateur = bible_tableau('separateur');
	$police = bible_tableau('police');
	
	$texte = '<form action="#SELF" method="post">
[<div>(#ENV{_cfg_}|form_hidden)</div>]
	
	<ul>
	<div id="explication"><bible:cfg_explication:></div>';
	foreach ($tableau_separateur as $lang=>$j){
		$texte .= '<li>
					<label for="traduction_'.$lang.'"><:bible:cfg_traduction_'.$lang.':></label>
					<select name="traduction_'.$lang.'"  id="traduction_'.$lang.'">'
					;
		foreach ($tableau_traduction as $traduction=>$tableau){
			if ($lang==$tableau['lang']){
				$texte .='<option value="'.$traduction.'" [selected="(#ENV{traduction_'.$lang.'}|=={'.$traduction.'})"]>
				'.
				traduction_longue($traduction).
						'
						
						</option>
						';
			
			}
		
		
		} 
		$texte.= 	'</select>
				
			</li>';

		
	}
		


	$texte.='<li>
					<label for="numeros"><:bible:cfg_numeros:></label>
					<input type="checkbox" name="numeros"  id="numeros" [checked="(#ENV{numeros})"]  value="oui" />
				
			</li>
			
			<li>
					<label for="retour"><:bible:cfg_retour:></label>
					<input type="checkbox" name="retour"  id="retour" [checked="(#ENV{retour})"]  value="oui" />
				
			</li>
			
			<li>
					<label for="ref"><:bible:cfg_ref:></label>
					<input type="checkbox" name="ref"  id="ref" [checked="(#ENV{ref})"]  value="oui" />
				
			</li>
			
			
		</ul>
	';
	
	foreach ($police as $i=>$polices){
	$texte .= '<li>
				<label for="police_'.$i.'"><:bible:police_'.$i.':></label><select name="police_'.$i.'"><option value="" [selected="(#ENV{police_'.$i.'}|=={non})"]><:item_non:></option>';
		foreach ($polices as $j){
			$texte .= "<option value='".$j."' [selected='(#ENV{police_".$i."}|=={".$j."})']>".$j."</option>";
		
		
		}
	$texte .= "</li>";
	}
		

	$texte .='<p class="boutons"><input type="submit" name="_cfg_ok" value="<:OK:>" />
	<input type="submit" name="_cfg_delete" value="<:Supprimer:>" /></p>
</fieldset></li>
</form>
</div>';
	return $texte;
}


?>