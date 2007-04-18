<script language="JavaScript1.2" type="text/javascript">
<!--
	function check_pwd() {
		if(document.edituser.user.value=="" || document.edituser.home_dir.value=="") {
			alert("<?php echo _T('spixplorer:"miscfieldmissed"'); ?>");
			return false;
		}
		if(document.edituser.chpass.checked &&
			document.edituser.pass1.value!=document.edituser.pass2.value)
		{
			alert("<?php echo _T('spixplorer:"miscnopassmatch"'); ?>");
			return false;
		}
		return true;
	}
// -->
</script>