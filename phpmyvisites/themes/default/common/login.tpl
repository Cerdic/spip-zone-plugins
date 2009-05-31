{include file="common/header.tpl"}

<br/><br/><br/><br/><br/><br/>
	<div class="both"></div>
	{if $error_login}
		<h2>{'login_error'|translate}</h2>
	{/if}
	{include file=admin/form_header.tpl}
		<p align="center">{'login_protected'|translate:$a_link_phpmv}</p>
	<div class="centrer">
		<form {$form_data.attributes}>
			<table class="centrer">
			<tr>
				<td colspan="2">
					{$form_data.hidden}
				</td>
			</tr>
			<tr>
				<td>{'login_login'|translate}</td>
				<td>{$form_data.form_login.html}</td>
			</tr>
			<tr>
				<td>{'login_password'|translate}</td>
				<td>{$form_data.form_password.html}</td>
			</tr>
			</table>
			<div class="boutonsAction">
				{$form_data.submit.html}
			</div>
		</form>
	</div>
	
{include file="common/footer.tpl"}

