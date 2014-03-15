<?php /* Template Name: People Archive */
get_header(); ?>

<?php	global $wp_query;
		$post_obj = $wp_query->get_queried_object(); ?>
	<!-- Note: the sidebar comes first in the markup, but appears on the right. This makes responsive styling easier -->
	<div id="sidebar" class="sidebar widget-area">
		<?php get_sidebar( 'group-archive' ); ?>
	</div><!--sidebar-->

	<div id="content" class="hfeed">
		<div <?php post_class(); ?>>
			<h1 class="entry-title"><?php echo $post_obj->post_title; ?> on the OpenLab</h1>
				<div class="entry-content">
					<div id="people-listing">
						<?php openlab_list_members('more' ); ?>
					</div><!--people-listing-->
				</div><!--entry-content-->
		</div><!--hentry-->
	</div><!--content-->

<?php get_footer();
