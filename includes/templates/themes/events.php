<?php

/*
* TOS FAU-Events theme template
*/

namespace RRZE\Tos;

defined('ABSPATH') || exit;

get_header();
?>
	<div class="content-wrap">
		<div id="blog-wrap" class="blog-wrap cf">
			<div id="primary" class="site-content cf rrze-calendar" role="main">
			    <div id="rrze-tos">
				<?php echo $content; ?>
			    </div>
			</div>
		</div>
	</div>

<?php
get_footer();
