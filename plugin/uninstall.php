<?php

/**
* Trigger this file on plugin uninstall
* @package AgePlugin
*/

if(!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

global $wpdb;
$wpdb->query("DELETE FROM wp_usermeta WHERE meta_key = 'age'");