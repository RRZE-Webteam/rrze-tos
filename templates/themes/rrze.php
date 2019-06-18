<?php

/*
* TOS rrze-2015 theme template
*/

namespace RRZE\Tos;

defined('ABSPATH') || exit;

get_header(); ?>

<div id="sidebar" class="sidebar">
    <?php get_sidebar('page'); ?>
</div><!-- .sidebar -->

<div id="content" class="site-content">
    <div id="primary" class="content-area">
	    <main id="main" class="site-main" role="main">
            <article id="rrze-tos">

                <header class="entry-header">
                    <?php printf('<h1 class="entry-title">%s</h1>', $title); ?>
                </header><!-- .entry-header -->

                <div class="entry-content">
		     <div id="rrze-tos">
                    <?php echo $content; ?>
		     </div>
                </div><!-- .entry-content -->

            </article><!-- #rrze-tos -->

	    </main><!-- .site-main -->
    </div><!-- .content-area -->
</div><!-- .site-content -->

<?php get_footer(); ?>
