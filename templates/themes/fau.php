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


get_header(); 
if ( is_plugin_active( 'rrze-elements/rrze-elements.php' ) ) {
	    wp_enqueue_style('rrze-elements');
	    wp_enqueue_script('rrze-accordions');
	   
	}
?>


<section id="hero" class="hero-small">
	<div class="container hero-content">
		<div class="row">
		    <div class="col-xs-12">
			<?php 
			fau_breadcrumb();
			?>
			
		    </div>
		</div>
		<div class="row" aria-hidden="true" role="presentation">
		    <div class="col-xs-12">
			<p class="presentationtitle"><?php echo $title; ?></p>
		    </div>
		</div>
	</div>
</section>

<div id="content">
    <div class="content-container">
	<div class="content-row">
	    <main> 
		<h1 class="screen-reader-text"><?php  echo $title; ?></h1>
		<div  id="rrze-tos">
		    <?php echo $content; ?>
		</div>
	    </main>
	 </div>
    </div>
	    	

</div>
<?php 
get_template_part('template-parts/footer', 'social');
get_footer();
?>


