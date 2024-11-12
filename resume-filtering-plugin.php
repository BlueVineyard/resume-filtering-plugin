<?php

/**
 * Plugin Name: Resume Filtering Plugin
 * Description: A plugin to filter resume listings by various criteria.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('RFP_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once RFP_PLUGIN_DIR . 'includes/class-resume-filtering-plugin.php';
require_once RFP_PLUGIN_DIR . 'includes/class-resume-filtering-ajax.php';

function rfp_enqueue_scripts()
{
    if (is_page() && has_shortcode(get_post()->post_content, 'resume_filter_form')) {
        wp_enqueue_style('rfp-jQueryUI', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        wp_enqueue_style('rfp-resume-filtering', plugin_dir_url(__FILE__) . 'assets/css/resume-filtering.css');
        wp_enqueue_script('jquery-ui-slider');

        wp_enqueue_script('rfp-resume-filtering', plugin_dir_url(__FILE__) . 'assets/js/resume-filtering.js', array('jquery'), null, true);
        wp_localize_script('rfp-resume-filtering', 'rfp_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
    }
}
add_action('wp_enqueue_scripts', 'rfp_enqueue_scripts');

add_action('wp_ajax_fetch_filtered_resumes', array('Resume_Filtering_Ajax', 'fetch_filtered_resumes'));
add_action('wp_ajax_nopriv_fetch_filtered_resumes', array('Resume_Filtering_Ajax', 'fetch_filtered_resumes'));
