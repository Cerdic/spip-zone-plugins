<script language="JavaScript1.2" type="text/javascript">
<!--
	function check_pwd() {
		if(document.adduser.user.value=="" || document.adduser.home_dir.value=="") {
			alert("<?php echo _T('spixplorer:"miscfieldmissed"'); ?>");
			return false;
		}
		if(document.adduser.pass1.value!=document.adduser.pass2.value) {
			alert("<?php echo _T('spixplorer:"miscnopassmatch"'); ?>");
			return false;
		}
		return true;
	}
// -->
</script>