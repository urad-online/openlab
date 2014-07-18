<?php get_header() ?>
<div id="content-sidebar-wrap">
		<form action="" method="post" id="blogs-directory-form" class="dir-form">
	<div id="content">
		<div class="padder">


			<h3><?php _e( 'Site Directory', 'buddypress' ) ?><?php if ( is_user_logged_in() && bp_blog_signup_enabled() ) : ?> &nbsp;<a class="button" href="<?php echo bp_get_root_domain() . '/' . BP_BLOGS_SLUG . '/create/' ?>"><?php _e( 'Create a Site', 'buddypress' ) ?></a><?php endif; ?></h3>

			<?php //do_action( 'bp_before_directory_blogs_content' ) ?>


			<div id="blogs-dir-list" class="blogs dir-list">
				<?php locate_template( array( 'buddypress/blogs/blogs-loop.php' ), true ) ?>
			</div><!-- #blogs-dir-list -->

			<?php do_action( 'bp_after_directory_blogs_content' ) ?>

			<?php wp_nonce_field( 'directory_blogs', '_wpnonce-blogs-filter' ) ?>


		</div><!-- .padder -->
	</div><!-- #content -->

	<?php //locate_template( array( 'sidebar.php' ), true ) ?>
	<div id="sidebar" class="sidebar widget-area">
			<div id="blog-dir-search" class="dir-search">
				<?php bp_directory_blogs_search_form() ?>
			</div><!-- #blog-dir-search -->
			<div class="item-list-tabs">
				<ul>
					<li class="selected" id="blogs-all"><a href="<?php bp_root_domain() ?>"><?php printf( __( 'All Sites (%s)', 'buddypress' ), bp_get_total_blog_count() ) ?></a></li>

					<?php if ( is_user_logged_in() && bp_get_total_blog_count_for_user( bp_loggedin_user_id() ) ) : ?>
						<li id="blogs-personal"><a href="<?php echo bp_loggedin_user_domain() . BP_BLOGS_SLUG . '/my-blogs/' ?>"><?php printf( __( 'My Sites (%s)', 'buddypress' ), bp_get_total_blog_count_for_user( bp_loggedin_user_id() ) ) ?></a></li>
					<?php endif; ?>

					<?php do_action( 'bp_blogs_directory_blog_types' ) ?>

					<li id="blogs-order-select" class="last filter">

						<?php _e( 'Order By:', 'buddypress' ) ?>
						<select>
							<option value="active"><?php _e( 'Last Active', 'buddypress' ) ?></option>
							<option value="newest"><?php _e( 'Newest', 'buddypress' ) ?></option>
							<option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ) ?></option>

							<?php do_action( 'bp_blogs_directory_order_options' ) ?>
						</select>
					</li>
				</ul>
			</div><!-- .item-list-tabs -->

	</div>
		</form><!-- #blogs-directory-form -->
</div>
<?php get_footer() ?>