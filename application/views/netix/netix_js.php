<script src="<?php echo base_url();?>public/js/jquery.min.js"></script>
<script src="<?php echo base_url();?>public/js/bootstrap.js"></script>
<script src="<?php echo base_url();?>public/js/fastclick.js"></script>
<script src="<?php echo base_url();?>public/js/nprogress.js"></script>
<script src="<?php echo base_url();?>public/js/icheck.min.js"></script>
<script src="<?php echo base_url();?>public/js/jquery.slimscroll.js"></script>

<script src="<?php echo base_url();?>public/js/notify/pnotify.js"></script>
<script src="<?php echo base_url();?>public/js/notify/pnotify.buttons.js"></script>
<script src="<?php echo base_url();?>public/js/notify/pnotify.nonblock.js"></script>
<script src="<?php echo base_url();?>public/js/sweetalert.min.js"></script>

<script src="<?php echo base_url();?>public/js/moment.js"></script>
<script src="<?php echo base_url();?>public/js/datepicker/daterangepicker.js"></script>
<script src="<?php echo base_url();?>public/js/datepicker/datetimepicker.min.js"></script>

<script src="<?php echo base_url();?>public/js/select/select.min.js"></script>
<script src="<?php echo base_url();?>public/js/select/ajax-select.min.js"></script>
<script src="<?php echo base_url();?>public/js/switchery.min.js"></script>

<script src="<?php echo base_url();?>public/js/custom.min.js"></script>
<script src="<?php echo base_url();?>public/js/vue/vue.js"></script>
<script src="<?php echo base_url();?>public/js/vue/vue-resource.min.js"></script>

<script>
	var url = "<?php echo base_url();?>";
	var sistema_url = window.location; sistema_url = String(sistema_url).split("/w/");

	if (sistema_url[1] != undefined && sistema_url[1] != "") {
		netix_controller = sistema_url[1];
	}else{
		netix_controller = "administracion/dashboard";
	}
</script>