<?php

/** 
 * @wordpress-plugin 
 * Plugin Name: Static Site Generator
 * Description: Adds ability to build site as a static site
 * Version: 1.0
 * Author: Ben Greene
 * Author URI: https://benrgreene.com
 * License: GPL v2 or later 
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt 
 */

require_once('utils.php');
require_once('hooks.php');
require_once('config-writer.php');
require_once('page-builder.php');
require_once('settings.php');
