
{include file=admin/form_header.tpl}
<p class="texte">{'admin_group_stitle'|translate}</p>
<form {$form_data.attributes}>

	<!-- Output hidden fields -->
	{$form_data.hidden}
	
	<!-- Display the fields -->
	
	<table align="center"> 
	{foreach from=$list_elements key=title item=data}

		<tr><td colspan="2">
			<table border=1 cellpadding=9 width=600>
			<tr><td colspan="2">{$title}</td></tr>

		{if sizeof($data)<2}
			<tr><td colspan="2"><font color="red">{'admin_no_user_group'|translate}</font></td></tr>
		{else}
			{foreach from=$data item=fieldname}
				
				<tr>
					<td>&nbsp;{$form_data.$fieldname.label}</td>
					<td>{$form_data.$fieldname.html}</td>
				</tr>
				
			{/foreach}
		{/if}
			
			</table>
		</td></tr>
		<tr><td><br/></td></tr>
	{/foreach}
</table>
	
<div class="boutonsAction">
{$form_data.submit.html}

{if $mod=='admin'}
{$form_data.back.html}
{/if}
</div>
	
</form>