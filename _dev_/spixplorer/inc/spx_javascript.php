<script src=".?page=jquery.js" type="text/javascript"></script>

<script language="JavaScript1.2" type="text/javascript">
<!--
jQuery(function() {
	// Checkboxes
	jQuery(".selitem").attr('checked', false).click(function() {
		jQuery(this).parents(".rowdata").toggleClass("rowdatasel");
		jQuery("#toggle_all")
			.attr('checked', this.checked ? 
				!jQuery(".selitem:not(:checked)").length : false);
   	});

	jQuery("#toggle_all").click(function() {
		jQuery(".selitem:"+(this.checked ? "not(:checked)" : "checked"))
			.attr('checked', this.checked)
			.parents(".rowdata").toggleClass("rowdatasel");
	});
});	
	
	function NumChecked() {
		return jQuery(".selitem:checked").length;
	}
	
<?php if (($GLOBALS['spx']["permissions"] & 01) == 01) { ?>
	
	// Copy / Move / Delete
	
	function Copy() {
		if(NumChecked()==0) {
			alert("<?php echo _T('spixplorer:miscselitems'); ?>");
			return;
		}
		document.selform.arg.value = document.selform.arg_copy_move.value;
		document.selform.hash.value = document.selform.hash_copy_move.value;
		document.getElementById("action").value = "spx_copy_move";
		document.selform.do_action.value = "copy";
		document.selform.submit();
	}
	
	function Move() {
		if(NumChecked()==0) {
			alert("<?php echo _T('spixplorer:miscselitems'); ?>");
			return;
		}
		document.selform.arg.value = document.selform.arg_copy_move.value;
		document.selform.hash.value = document.selform.hash_copy_move.value;
		document.getElementById("action").value = "spx_copy_move";
		document.selform.do_action.value = "move";
		document.selform.submit();
	}
	
	function Delete() {
		num=NumChecked();
		if(num==0) {
			alert("<?php echo _T('spixplorer:miscselitems'); ?>");
			return;
		}
		if(confirm("<?php echo _T('spixplorer:miscdelitems'); ?>")) {
			document.selform.arg.value = document.selform.arg_del.value;
			document.selform.hash.value = document.selform.hash_del.value;
			document.getElementById("action").value = "spx_del";
			document.selform.submit();
		}
	}
	
	function Archive() {
		if(NumChecked()==0) {
			alert("<?php echo _T('spixplorer:miscselitems'); ?>");
			return;
		}
		document.selform.namearch.value = document.creaform.mkname.value;
		document.selform.arg.value = document.selform.arg_archive.value;
		document.selform.hash.value = document.selform.hash_archive.value;
		document.getElementById("action").value = "spx_archive";
		document.selform.submit();
	}
	
	function Fichier()
	{
		if (document.creaform.mktype.value == 'archive') {
			Archive();
			return false;
		}
		return true;
	}
	
<?php } ?>

// -->
</script>
