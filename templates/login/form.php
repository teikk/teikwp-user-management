<form method="POST" class="fwpum-login fwpum-validate">
	<?php do_action( 'fwpum/login/errors' ); ?>
	<div class="fwpum__field">
		<label>
			<?php _e('Username','fwpum'); ?><br>
			<input type="text" name="user_name" required>
		</label>
	</div>
	<div class="fwpum__field">
		<label>
			<?php _e('Password','fwpum'); ?><br>
			<input type="password" name="password" id="password" required>
		</label>
	</div>
	<div class="fwpum__field">
		<label>
			<?php _e('Remember me','fwpum'); ?><br>
			<input type="checkbox" name="remember" value="1">
		</label>
	</div>
	<div class="fwpum__field">
		<?php wp_nonce_field( 'login', '_fwpum_login', false, true ); ?>
		<button type="submit"><?php _e('Login','fwpum'); ?></button>
	</div>
</form>