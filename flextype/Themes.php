<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\View\View;
use Flextype\Component\Registry\Registry;

class Themes
{
    /**
     * Private construct method to enforce singleton behavior.
     *
     * @access private
     */
    public function __construct()
    {
        // Get current theme
        $theme = Registry::get('settings.theme');

        // Set empty themes items
        Registry::set('themes', []);

        // Create Unique Cache ID for Theme
        $theme_cache_id = md5('theme' . filemtime(PATH['themes'] . '/' . $theme . '/' . 'settings.yaml') .
                                        filemtime(PATH['themes'] . '/' . $theme . '/' . $theme . '.yaml'));

        // Get Theme mafifest file and write to settings.themes array
        if (Cache::contains($theme_cache_id)) {
            Registry::set('themes.' . Registry::get('settings.theme'), Cache::fetch($theme_cache_id));
        } else {
            if (Filesystem::has($theme_settings = PATH['themes'] . '/' . $theme . '/' . 'settings.yaml') and
                Filesystem::has($theme_config = PATH['themes'] . '/' . $theme . '/' . $theme . '.yaml')) {
                $theme_settings = YamlParser::decode(Filesystem::read($theme_settings));
                $theme_config = YamlParser::decode(Filesystem::read($theme_config));
                $_theme = array_merge($theme_settings, $theme_config);
                Registry::set('themes.' . Registry::get('settings.theme'), $_theme);
                Cache::save($theme_cache_id, $_theme);
            }
        }
    }


    /**
     * Get partials for current theme
     *
     * @access public
     * @return array
     */
    public function getPartials() : array
    {
        $partials = [];

        // Get partials files
        $_partials = Filesystem::listContents(PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/partials/');

        // If there is any partials file then go...
        if (count($_partials) > 0) {
            foreach ($_partials as $partial) {
                if ($partial['type'] == 'file' && $partial['extension'] == 'php') {
                    $partials[$partial['basename']] = $partial['basename'];
                }
            }
        }

        // return partials
        return $partials;
    }

    /**
     * Get templates for current theme
     *
     * @access public
     * @return array
     */
    public function getTemplates() : array
    {
        $templates = [];

        // Get templates files
        $_templates = Filesystem::listContents(PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/templates/');

        // If there is any template file then go...
        if (count($_templates) > 0) {
            foreach ($_templates as $template) {
                if ($template['type'] == 'file' && $template['extension'] == 'php') {
                    $templates[$template['basename']] = $template['basename'];
                }
            }
        }

        // return templates
        return $templates;
    }
}
