<?php

/*
* TOS default theme template
*/

namespace RRZE\Tos;

defined('ABSPATH') || exit;

get_header();
?>
	<section id="primary" class="content-area">
		<main id="main" class="site-main">
		     <div id="rrze-tos">
			<?php echo $content; ?>
		    </div>
		</main>
	</section>
<?php
get_footer();
