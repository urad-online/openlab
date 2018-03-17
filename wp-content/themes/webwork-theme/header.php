<!DOCTYPE html>

<html <?php language_attributes(); ?>>

	<head>

		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" >

		<?php if ( is_singular() ) wp_enqueue_script( "comment-reply" ); ?>

		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?>>

		<div class="big-wrapper">

			<div class="header-cover section bg-dark-light no-padding">

				<div class="header section">
					<img class="banner-img" src="<?php echo get_stylesheet_directory_uri() . '/images/OLWW_BANNER.png'; ?>" alt="OpenLab WeBWorK logo" />
				</div> <!-- /header -->

			</div> <!-- /bg-dark -->

			<div class="navigation section no-padding bg-dark">

				<div class="navigation-inner section-inner">

					<div class="toggle-container hidden">

						<div class="nav-toggle toggle">

							<div class="bar"></div>
							<div class="bar"></div>
							<div class="bar"></div>

							<div class="clear"></div>

						</div>

						<div class="search-toggle toggle">

							<div class="metal"></div>
							<div class="glass"></div>
							<div class="handle"></div>

						</div>

						<div class="clear"></div>

					</div> <!-- /toggle-container -->

					<div class="blog-search hidden">

						<?php get_search_form(); ?>

					</div>

					<ul class="blog-menu">

						<?php if ( has_nav_menu( 'primary' ) ) : ?>
							<?php wp_nav_menu( array(
								'container' => '',
								'items_wrap' => '%3$s',
								'theme_location' => 'primary',
								'walker' => new hemingway_nav_walker
							) ) ?>
						<?php else : ?>
							<?php wp_list_pages( array(
								'container' => '',
								'title_li' => '',
							) ) ?>
						<?php endif; ?>

						<div class="clear"></div>

					 </ul>

					 <ul class="mobile-menu">
						<?php if ( has_nav_menu( 'primary' ) ) {
							wp_nav_menu( array(
								'container' => '',
								'items_wrap' => '%3$s',
								'theme_location' => 'primary',
								'walker' => new hemingway_nav_walker
							) );
						} else {
							wp_list_pages( array(
								'container' => '',
								'title_li' => ''
							));
						} ?>
					 </ul>

				</div> <!-- /navigation-inner -->

			</div> <!-- /navigation -->
