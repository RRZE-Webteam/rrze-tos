<?php

/*
* TOS FAU-Einrichtungen theme template
*/

namespace RRZE\Tos;

defined('ABSPATH') || exit;

global $post;

if (function_exists('fau_initoptions')) {
    $options = fau_initoptions();
} else {
    $options = array();
}

if (isset($options['breadcrumb_root'])) {
    $delimiter	 = $options['breadcrumb_delimiter']; // = ' / ';
    $home		 = $options['breadcrumb_root']; // __( 'Startseite', 'fau' ); // text for the 'Home' link
    $before	     = $options['breadcrumb_beforehtml']; // '<span class="current">'; // tag before the current crumb
    $after	     = $options['breadcrumb_afterhtml']; // '</span>'; // tag after the current crumb
    $showcurrent = $options['breadcrumb_showcurrent'];

    $breadcrumb  = '<nav aria-labelledby="bc-title" class="breadcrumbs">';
    $breadcrumb .= '<h2 class="screen-reader-text" id="bc-title">' . __('Breadcrumb', 'fau') . '</h2>';
    if (get_theme_mod('breadcrumb_withtitle')) {
        $breadcrumb .= '<p class="breadcrumb_sitetitle" role="presentation">' . get_bloginfo('title') . '</p>' . PHP_EOL;
    }
    $homeLink = home_url('/');
    $breadcrumb .= '<a href="' . $homeLink . '">' . $home . '</a>' . $delimiter;
    $breadcrumb .= $showcurrent ? $before . $title . $after : '';
    $breadcrumb .= '</nav>';
}
get_header(); ?>

<section id="hero" class="hero-small">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<?php echo $breadcrumb; ?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<h1><?php echo $title; ?></h1>
			</div>
		</div>
	</div>
</section>

<div id="content">
	<div class="container">

		<div class="row">
			<div class="col-xs-12">
				<main>
					<div id="rrze-tos">
					<?php echo $content; ?>
					</div>    
				</main>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
