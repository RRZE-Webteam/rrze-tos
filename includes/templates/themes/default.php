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
			<?php echo $content; ?>
		</main>
	</section>
<?php
get_footer();
