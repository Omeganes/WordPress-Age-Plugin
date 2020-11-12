<?php
/**
 * @package AgePlugin
 */
/*
Plugin Name: Age Plugin
Plugin URI: https://github.com/Omeganes/WordPress-Age-Plugin
Description: <strong>Adds an “Age” attribute to WordPress user.</strong>
Version: 1.0.0
Author: Raymond Youssef
Author URI: http://www.github.com/Omeganes
License: GPLv2 or later
Text Domain: age-plugin
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2005-2015 Automatic, Inc.
*/

defined('ABSPATH') or die('You can\'t access this file.');

define( 'AGEPLUGIN__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
require_once( AGEPLUGIN__PLUGIN_DIR . 'class.age-plugin.php' );

if ( class_exists('AgePlugin')) {
	$agePlugin = new AgePlugin();

	// activation 
	register_activation_hook(__FILE__, array($agePlugin, 'activate'));

	// deactivation
	register_activation_hook(__FILE__, array($agePlugin, 'deactivate'));

}