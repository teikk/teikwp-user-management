<form method="POST" class="fwpum-register fwpum-validate">
	<?php do_action( 'fwpum/register/errors' ); ?>
	<div class="fwpum__field">
		<label>
			<?php _e('Nazwa użytkownika','fwpum'); ?><br>
			<input type="text" name="user_name" required>
		</label>
	</div>
	<div class="fwpum__field">
		<label>
			<?php _e('Hasło','fwpum'); ?><br>
			<input type="password" name="password" id="password" required>
		</label>
	</div>	
	<div class="fwpum__field">
		<label>
			<?php _e('Powtórz hasło','fwpum'); ?><br>
			<input type="password" name="confirm_password" id="confirm-password" required data-parsley-equalto="#password">
		</label>
	</div>
	<div class="fwpum__field">
		<label>
			<?php _e('Email','fwpum'); ?><br>
			<input type="email" name="email" required>
		</label>
	</div>
	<div class="fwpum__field">
		<label>
			<?php _e('Regulamin','fwpum'); ?><br>
			<input type="checkbox" name="accept" value="1" required>
		</label>
	</div>
	<div class="fwpum__field">
		<?php wp_nonce_field( 'register', '_fwpum_register', false, true ); ?>
		<button type="submit" value="register">Zarejestruj</button>
	</div>
</form>