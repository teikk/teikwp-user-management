<?php $errors = get_query_var( 'fwpum_errors' );?>
<?php if( $errors->get_error_codes() ): ?>
	<ul class="fwpum_errors">
		<?php foreach ($errors->get_error_messages() as $key => $error):?>
			<li class="fwpum_errors__error"><?php echo $error; ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php if( isset( $_GET['checkmail'] ) ): ?>
	<ul class="fwpum_errors">
		<li class="fwpum_errors__success"><?php _e("Check your email for further instructions",'fwpum'); ?></li>
	</ul>
<?php endif; ?>