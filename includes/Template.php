<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

class Template
{
    /**
     * [getContent description]
     * @param  string $template [description]
     * @param  array  $data     [description]
     * @return string           [description]
     */
    public static function getContent($template = '', $data = [])
    {
        return self::parseContent($template, $data);
    }

    /**
     * [parseContent description]
     * @param  string $template [description]
     * @param  array  $data     [description]
     * @return string           [description]
     */
    protected static function parseContent($template, $data)
    {
        $templateFile = self::getTemplateLocale($template);
        if (! $templateFile || empty($data)) {
            return '';
        }
        $parser = new Parser();
        return $parser->parse($templateFile, $data);
    }

    /**
     * [getTemplateLocale description]
     * @param  string $template [description]
     * @return string           [description]
     */
    protected static function getTemplateLocale($template)
    {
        $pluginDirPath = plugin_dir_path(RRZE_PLUGIN_FILE);
        $locale = Locale::getLocale();
        $format = '%1$sincludes/templates/contents/%2$s-%3$s.html';

        $templateFile = sprintf($format, $pluginDirPath, $template, $locale);
        if (is_readable($templateFile)) {
            return $templateFile;
        }

        $templateFile = '';
        $langCode = Locale::getLangCode();
        $localeFallback = Locale::getLocaleFallback();
        if (isset($localeFallback[$langCode])) {
            $templateFile = sprintf($format, $pluginDirPath, $template, $localeFallback[$langCode]);
        }
        return is_readable($templateFile) ? $templateFile : '';
    }
}
