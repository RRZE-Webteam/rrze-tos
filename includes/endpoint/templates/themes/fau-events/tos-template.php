<?php
/* Quit */
defined( 'ABSPATH' ) || exit;

$values = (array) get_option( 'rrze_tos' );
include WP_PLUGIN_DIR . "/rrze-tos/includes/strings.php";
$strings = $template['a11y'];
get_header();

?>

<div class="content-wrap">
	<div id="blog-wrap" class="blog-wrap cf">
		<div id="primary" class="site-content cf rrze-calendar" role="main">
			<?php ( new RRZE\Tos\Tos_Endpoint() )->get_tos_content(); ?>
		</div><!-- end #primary -->
		<?php get_sidebar(); ?>
	</div><!-- end .blog-wrap -->
</div><!-- end .content-wrap -->

<?php get_footer(); ?>
