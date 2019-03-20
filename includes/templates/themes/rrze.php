<?php

/*
* TOS rrze-2015 theme template
*/

namespace RRZE\Tos;

defined('ABSPATH') || exit;

get_header();
?>
    <div id="primary" class="content-area">
    	<div id="content" class="site-content" role="main">
    		<?php echo $content; ?>
    	</div>
    </div>
<?php
get_footer();
