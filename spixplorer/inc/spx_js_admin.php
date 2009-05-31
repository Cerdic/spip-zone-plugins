<script language="JavaScript1.2" type="text/javascript">
<!--
	function check_pwd() {
		if(document.chpwd.newpwd1.value!=document.chpwd.newpwd2.value) {
			alert("<?php echo _T('spixplorer:miscnopassmatch'); ?>");
			return false;
		}
		if(document.chpwd.oldpwd.value==document.chpwd.newpwd1.value) {
			alert("<?php echo _T('spixplorer:miscnopassdiff'); ?>");
			return false;
		}
		return true;
	}
	
	
	// Edit / Delete
	
	function Edit() {
		document.userform.action2.value = "edituser";
		document.userform.submit();
	}
	
	function Delete() {
		var ml = document.userform;
		var len = ml.elements.length;
		var user;
		for (var i=0; i<len; ++i) {
			var e = ml.elements[i];
			if(e.name == "user" && e.checked) {
				user=e.value;
				break;
			}
		}
		
		if(confirm("<?php echo _T('spixplorer:miscdeluser'); ?>")) {
			document.userform.action2.value = "rmuser";
			document.userform.submit();
		}
	}

// -->
</script>