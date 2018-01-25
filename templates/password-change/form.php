<form method="POST" class="fwpum-change-password fwpum-validate">
	<?php do_action( 'fwpum/password-change/errors' ); ?>
	<div class="fwpum__field">
		<label>
			<?php _e('Old password','fwpum'); ?><br>
			<input type="password" name="old_password" id="old_password" required>
		</label>
	</div>
	<div class="fwpum__field">
		<label>
			<?php _e('New password','fwpum'); ?><br>
			<input type="password" name="new_password" id="new_password" required>
		</label>
	</div>
	<div class="fwpum__field">
		<label>
			<?php _e('Repeat your new password','fwpum'); ?><br>
			<input type="password" name="confirm_password" id="confirm_password" data-parsley-equalto="#new_password" required>
		</label>
	</div>
	<p class="description"><?php echo wp_get_password_hint(); ?></p>
	<div class="fwpum__field">
		<?php wp_nonce_field( 'change-password', '_fwpum_change-password', false, true ); ?>
		<button type="submit"><?php _e('Apply','fwpum'); ?></button>
	</div>
</form>