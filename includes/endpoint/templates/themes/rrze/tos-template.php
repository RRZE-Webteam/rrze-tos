<?php

/* Quit */
defined( 'ABSPATH' ) || exit;

$values = (array) get_option( 'rrze_tos' );

get_header(); ?>

<?php if ( ! is_front_page() ) { ?>
	<div id="sidebar" class="sidebar">
		<?php get_sidebar( 'page' ); ?>
	</div><!-- .sidebar -->
<?php } ?>
<div id="primary" class="content-area">
	<div id="content" class="site-content" role="main">
		<?php ( new RRZE\Tos\Tos_Endpoint() )->get_tos_content(); ?>
	</div>
</div>
<?php get_footer(); ?>
