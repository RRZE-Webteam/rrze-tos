<?php

/* Quit */
defined('ABSPATH') || exit;

get_header(); ?>

<?php if (!is_front_page()) { ?>
    <div id="sidebar" class="sidebar">
        <?php get_sidebar('page'); ?>
    </div><!-- .sidebar -->
<?php } ?>
<div id="primary" class="content-area">
    <div id="content" class="site-content" role="main">

    </div>
</div>
<?php get_footer(); ?>
