<?php
gconnect_get_header();

do_action( 'bp_before_member_settings_template' );
?>
	<h1 class="entry-title"><?php bp_displayed_user_fullname() ?>'s Profile</h1>
	<h3 id="bread-crumb"><?php _e( 'General Settings', 'buddypress' ); ?></h3>
	<div id="item-header">
		<?php gconnect_locate_template( array( 'members/single/member-header.php' ), true ); ?>
	</div><!-- #item-header -->

	<div id="item-nav">
		<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
			<ul>
				<?php bp_get_displayed_user_nav(); do_action( 'bp_member_options_nav' ); ?>
			</ul>
			<div class="clear"></div>
		</div>
	</div><!-- #item-nav -->

	<div id="item-body" role="main">

		<?php do_action( 'bp_before_member_body' ); ?>

		<div class="item-list-tabs no-ajax" id="bpsubnav">
			<ul>
				<?php bp_get_options_nav(); do_action( 'bp_member_plugin_options_nav' ); ?>
			</ul>
			<div class="clear"></div>
		</div><!-- .item-list-tabs -->

		<h3><?php _e( 'General Settings', 'buddypress' ); ?></h3>

		<?php do_action( 'bp_template_content' ) ?>

		<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post" class="standard-form" id="settings-form">
			<div class="settings-section username-section">
				<label for="username">Username (required)</label>
				<input type="text" id="username" disabled="disabled" value="<?php bp_displayed_user_username() ?>" />
				<p class="description">Your username cannot be changed</p>
			</div>

			<div class="settings-section email-section">
				<label for="email_visible">Account Email Address (required)</label>
				<input type="text" name="email_visible" id="email_visible" value="<?php echo bp_get_displayed_user_email(); ?>" class="settings-input" disabled="disabled" />
				<input type="hidden" name="email" value="<?php echo bp_get_displayed_user_email() ?>" />
				<p class="description">Your email address cannot be changed. If your City Tech email address has changed, contact us for assistance.</p>
			</div>

			<div class="settings-section current-pw-section">
				<label for="pwd">Current Password</label>
				<input type="password" name="pwd" id="pwd" size="16" value="" class="settings-input small" />
				<p class="description">Required to update email or change current password. <a href="<?php echo site_url( add_query_arg( array( 'action' => 'lostpassword' ), 'wp-login.php' ), 'login' ); ?>" title="<?php _e( 'Password Lost and Found', 'buddypress' ); ?>"><?php _e( 'Lost your password?', 'buddypress' ); ?></a></p>
			</div>

			<div class="settings-section change-pw-section">
				<label for="pass1">Change Password</label>
				<input type="password" name="pass1" id="pass1" size="16" value="" class="settings-input small" />

				<label for="pass1">Confirm Change Password</label>
				<input type="password" name="pass2" id="pass2" size="16" value="" class="settings-input small" />

				<p class="description">Leave blank for no change</p>
			</div>

			<div class="settings-section name-section">
				<label for="fname">First Name (required)</label>
				<input type="text" name="fname" id="fname" value="<?php echo bp_get_profile_field_data( array( 'field' => 'First Name' ) ) ?>" />

				<label for="lname">Last Name (required)</label>
				<input type="text" name="lname" id="lname" value="<?php echo bp_get_profile_field_data( array( 'field' => 'Last Name' ) ) ?>" />
			</div>

			<?php do_action( 'bp_core_general_settings_before_submit' ); ?>

			<div class="submit">
				<input type="submit" name="submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?>" id="submit" class="auto" />
			</div>

			<?php do_action( 'bp_core_general_settings_after_submit' ); wp_nonce_field( 'bp_settings_general' ); ?>
		</form>
		<?php do_action( 'bp_after_member_body' ); ?>
	</div><!-- #item-body -->
<?php
do_action( 'bp_after_member_settings_template' );

gconnect_get_footer();
?>