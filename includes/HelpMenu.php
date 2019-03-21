<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

class HelpMenu
{
    /**
     * [protected description]
     * @var string
     */
    protected $screenMenuId;

    /**
     * [__construct description]
     * @param string $screenMenuId [description]
     */
    public function __construct($screenMenuId = '')
    {
        $this->screenMenuId = $screenMenuId;
        $this->setMenu();
    }

    /**
     * [setMenu description]
     */
    protected function setMenu()
    {
        $content = [
             '<p>' . __('Here comes the Context Help content.', 'rrze-tos') . '</p>',
         ];
        $help_tab = [
             'id' => $this->screenMenuId,
             'title' => __('Overview', 'rrze-tos'),
             'content' => implode(PHP_EOL, $content),
         ];
        $help_sidebar = sprintf(
            '<p><strong>%1$s:</strong></p><p><a href="http://blogs.fau.de/webworking">RRZE-Webworking</a></p><p><a href="https://github.com/RRZE-Webteam">%2$s</a></p>',
            __('For more information', 'rrze-tos'),
            __('RRZE Webteam on Github', 'rrze-tos')
         );

        $screen = get_current_screen();

        if ($screen->id != $this->screenMenuId) {
            return;
        }

        $screen->add_help_tab($help_tab);
        $screen->set_help_sidebar($help_sidebar);
    }
}
