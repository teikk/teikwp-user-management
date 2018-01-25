<?php if( isset($_GET['key']) && isset($_GET['login']) ): ?>
	<?php do_action( 'fwpum/password-reset/errors' ); ?>
	<form name="resetpassform" id="resetpassform" method="POST" class=" fwpum-resetpass fwpum-validate" autocomplete="off">
		<div class="fwpum__field">
			<label><?php _e( 'New password', 'fwpum' ) ?><br>
				<input type="password" name="password" id="password" size="20" value="" autocomplete="off"/>
			</label>
		</div>
		<div class="fwpum__field">
			<label><?php _e( 'Repeat new password', 'fwpum' ) ?><br>
				<input type="password" name="confirm_password" id="confirm_password" size="20" value="" autocomplete="off" data-parsley-equalto="#password"/>
			</label>
		</div>
		<p class="description"><?php echo wp_get_password_hint(); ?></p>
		<?php wp_nonce_field( 'reset-password', '_fwpum_reset-password-final', false, true ); ?>
		<div class="fwpum__field">
			<button type="submit"><?php _e('Apply','fwpum'); ?></button>
			<input type="hidden" name="key" value="<?php echo $_GET['key']; ?>">
			<input type="hidden" name="login" value="<?php echo $_GET['login']; ?>">
		</div>
	</form>
<?php else: ?>
	<form method="POST" action="<?php echo wp_lostpassword_url(); ?>" class="fwpum-reset-password fwpum-validate">
		<?php do_action( 'fwpum/password-reset/errors' ); ?>
		<div class="fwpum__field">
			<label>
				<?php _e('Enter your e-mail or username','fwpum'); ?><br>
				<input type="text" name="user_login" id="username" required>
			</label>
		</div>
		<div class="fwpum__field">
			<?php wp_nonce_field( 'reset-password', '_fwpum_reset-password', false, true ); ?>
			<button type="submit"><?php _e('Apply','fwpum'); ?></button>
		</div>
	</form>
<?php endif; ?>