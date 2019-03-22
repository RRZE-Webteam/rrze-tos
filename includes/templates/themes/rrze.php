<?php

/*
* TOS rrze-2015 theme template
*/

namespace RRZE\Tos;

defined('ABSPATH') || exit;

function breadcrumb($title = '')
{
    global $options;

    $link = '<li><a href="%1$s" itemprop="url"><span itemprop="title">%2$s</span></a></li>';
    $text = '<span class="divider">  &#187; </span>&nbsp;<li class="current"><span aria-current="location">%s</span></li>';

    echo '<div id="breadcrumbs" class="breadcrumbs clear" role="navigation"><h2>' . __('Sie sind hier:&nbsp;', 'rrze-2015') . '</h2><ul>';
    printf($link, home_url('/'), $options['text-startseite']);
    printf($text, $title);
    echo '</ul></div><!-- .breadcrumbs -->';
}
get_header();
breadcrumb($title); ?>

<?php get_sidebar('page'); ?>

<div id="content" class="site-content">
    <div id="primary" class="content-area">
	    <main id="main" class="site-main" role="main">
            <article id="rrze-tos">

                <header class="entry-header">
                    <?php printf('<h1 class="entry-title">%s</h1>', $title); ?>
                </header><!-- .entry-header -->

                <div class="entry-content">
                    <?php echo $content; ?>
                </div><!-- .entry-content -->

            </article><!-- #rrze-tos -->

	    </main><!-- .site-main -->
    </div><!-- .content-area -->
</div><!-- .site-content -->

<?php get_footer(); ?>
