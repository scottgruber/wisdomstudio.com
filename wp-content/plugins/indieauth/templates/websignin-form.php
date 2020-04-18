<?php
$errors = new WP_Error();
login_header(
	__( 'Sign in with your website', 'indieauth' ),
	'',
	$errors
);
?>
<form name="loginform" id="loginform" action="<?php add_query_arg( 'action', 'websignin', wp_login_url() ); ?>" method="post">
	<div class="login-info">
		<p><?php _e( 'Sign in with your domain', 'indieauth' ); ?></p>
		<input class="input" type="url" name="websignin_identifier" placeholder="<?php _e( 'https://example.com', 'indieauth' ); ?>" />
	</div>
	<p class="submit">
	<?php
		// Hook to allow adding to form
		do_action( 'indieauth_login_form' );
	?>
		<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php _e( 'Sign in', 'indieauth' ); ?>" />
	</p>
	<p class="learn"><a href="https://indieweb.org/Web_sign-in" target="_blank"><?php _e( 'Learn about Web Sign-in', 'indieauth' ); ?></a></p>
</form>

<?php login_footer(); ?>

<style>
	.login-info img {
		width: 78px;
		display: block;
		margin: 0 auto;
		border-radius: 6px;
	}
	.login-info p {
		margin-top: 1em;
	}

	#login form p.submit {
		margin-top: 1em;
	}

	.learn {
		margin-top: 5em;
	}

	form input {
		width: 100%;
	}
</style>
