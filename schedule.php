<?php
/*
 * Plugin Name: Schedule
 * Plugin URI: https://github.com/treefy
 * Description: Manage your calendar appointments and time schedule.
 * Author: treefy
 * Author URI: https://github.com/treefy/
 * Version: 1.0.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Repo: https://github.com/treefy
*/

/*

We love open source projects. We love WP and many other collaborative projects.
Feel free to edit, add or remove whatever you want in Schedule

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/


if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'cron_schedules', 'myprefix_add_tw_seconds_cron_schedule' );
function myprefix_add_tw_seconds_cron_schedule( $schedules ) {
    $schedules['twseconds'] = array(
        'interval' => 20, // in seconds
        'display'  => __( 'Once each 20 seconds' ),
    );

    return $schedules;
}

// Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'myprefix_my_cron_action_tw_seconds' ) ) {
    wp_schedule_event( time(), 'twseconds', 'myprefix_my_cron_action_tw_seconds' );
}

// Hook into that action that'll fire weekly
//add_action( 'myprefix_my_cron_action_tw_seconds', 'myprefix_function_tw_seconds_to_run' );
function myprefix_function_tw_seconds_to_run() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'schedule';

    $charset_collate = $wpdb->get_charset_collate();

    $results = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE status=1 AND alarm < now();", ARRAY_A );
    if (!empty($results)) {
        foreach ($results as $r)
        {
            $id = $r['id'];
            $start = date_create($r['alarm']);
            $end = date_create(date('Y-m-d H:i:s'));
            $diff=date_diff($end,$start);
            $elapsedTime = '';
            if ($diff->y > 0) $elapsedTime .= $diff->y.' days ';
            if ($diff->h > 0) {
                if (strlen($elapsedTime) > 0)
                {
                    $elapsedTime .= 'and '.$diff->h.' hours ';
                }
                else {
                    $elapsedTime .= $diff->h.' hours ';
                }
            }
            if ($diff->i > 0)
            {
                if (strlen($elapsedTime) > 0)
                {
                    $elapsedTime .= 'and '.$diff->i.' minutes ';
                }
                else {
                    $elapsedTime .= $diff->i.' minutes ';
                }
            }
            if (strlen($elapsedTime) > 0) {
                $elapsedTimeString = '('.$elapsedTime.'ago)';
            }
            $subject = '[SCHEDULE] '.$r['subject'];
            $body = str_replace('\r\n','<br>',$results[0]['description']).'<br><br>'.$elapsedTimeString;
            wp_mail(get_option('admin_email'), $subject, $body);
            $update = $wpdb->get_results( "UPDATE {$table_name} SET status=0 WHERE id={$id}", ARRAY_A );


        }
    }
}


if ( !function_exists("mge_mg_schedule_add_button_in_pages_and_posts") ) {
    function mge_mg_schedule_add_button_in_pages_and_posts($content) {

        $options = get_option( 'schedule_dot_li_content_settings' );
    }
}

add_action( 'admin_menu', 'mge_mg_schedule_dot_li_add_admin_menu' );
add_action( 'admin_init', 'mge_mg_schedule_dot_li_settings_init' );

function mge_mg_schedule_dot_li_add_admin_menu() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'schedule';
    $charset_collate = $wpdb->get_charset_collate();
    $now = time();
    $results = $wpdb->get_results( "SELECT COUNT(id) AS counter FROM {$table_name} WHERE status=1 AND alarm < now();", ARRAY_A );
    $counter = $results[0]['counter'];
    $menuString = $counter > 0 ? 'Schedule <span class="update-plugins count-'.$counter.'"><span class="plugin-count">' . number_format_i18n($counter) . '</span></span>' : 'Schedule';
    add_menu_page( 'Schedule', $menuString, 'manage_options', 'schedule_dot_li_configuration', 'schedule_dot_li_configuration_content_page' );
}

function mge_mg_schedule_dot_li_settings_init() {


    register_setting( 'SchedulePluginCustomContent', 'schedule_dot_li_content_settings' );

    add_settings_section(
        'schedule_dot_li_custom_content_to_bottom_of_post_section',
        __( '', 'schedule_dot_li_add_custom_content_to_bottom_of_post' ),
        '',
        'SchedulePluginCustomContent'
    );

    add_settings_field(
        'schedule_dot_li_call_to_action_content_value_field',
        __( 'Call to action text:', 'schedule_dot_li_add_custom_content_to_bottom_of_post' ),
        'schedule_call_to_action_text_field_section_render',
        'SchedulePluginCustomContent',
        'schedule_dot_li_custom_content_to_bottom_of_post_section'
    );


    add_settings_field(
        'schedule_dot_li_date_field_content_value_field',
        __( 'Date field:', 'schedule_dot_li_add_custom_content_to_bottom_of_post' ),
        'schedule_dot_li_date_field_section_render',
        'SchedulePluginCustomContent',
        'schedule_dot_li_custom_content_to_bottom_of_post_section'
    );

    add_settings_field(
        'schedule_dot_li_time_field_content_value_field',
        __( 'Time field:', 'schedule_dot_li_add_custom_content_to_bottom_of_post' ),
        'schedule_dot_li_time_field_section_render',
        'SchedulePluginCustomContent',
        'schedule_dot_li_custom_content_to_bottom_of_post_section'
    );

    add_settings_field(
        'schedule_dot_li_name_field_content_value_field',
        __( 'Name field:', 'schedule_dot_li_add_custom_content_to_bottom_of_post' ),
        'schedule_dot_li_name_field_section_render',
        'SchedulePluginCustomContent',
        'schedule_dot_li_custom_content_to_bottom_of_post_section'
    );

    add_settings_field(
        'schedule_dot_li_email_field_content_value_field',
        __( 'Email field:', 'schedule_dot_li_add_custom_content_to_bottom_of_post' ),
        'schedule_dot_li_email_field_section_render',
        'SchedulePluginCustomContent',
        'schedule_dot_li_custom_content_to_bottom_of_post_section'
    );


    add_settings_field(
        'schedule_dot_li_phone_field_content_value_field',
        __( 'Phone field:', 'schedule_dot_li_add_custom_content_to_bottom_of_post' ),
        'schedule_dot_li_phone_field_section_render',
        'SchedulePluginCustomContent',
        'schedule_dot_li_custom_content_to_bottom_of_post_section'
    );

    add_settings_field(
        'schedule_dot_li_submit_field_content_value_field',
        __( 'Submit field:', 'schedule_dot_li_add_custom_content_to_bottom_of_post' ),
        'schedule_dot_li_submit_field_section_render',
        'SchedulePluginCustomContent',
        'schedule_dot_li_custom_content_to_bottom_of_post_section'
    );

}

function schedule_call_to_action_text_field_section_render() {

    $options = get_option( 'schedule_dot_li_content_settings' );
    ?>
    <input type='text' name='schedule_dot_li_content_settings[schedule_dot_li_call_to_action_content_value_field]' value='<?php

    echo mik_mge_filter_schedule_add_action_plugin_return_valid_value_to_schedule('schedule_dot_li_call_to_action_content_value_field');

    ?>' cols='' style='width:100%' >
    <?php
}


function schedule_dot_li_name_field_section_render() {

    $options = get_option( 'schedule_dot_li_content_settings' );

    ?>
    <input type='text' name='schedule_dot_li_content_settings[schedule_dot_li_name_content_value_field]' value='<?php

    echo mik_mge_filter_schedule_add_action_plugin_return_valid_value_to_schedule('schedule_dot_li_name_content_value_field');

    ?>' cols='' style='width:100%' >
    <?php
}


function schedule_dot_li_date_field_section_render() {

    $options = get_option( 'schedule_dot_li_content_settings' );
    ?>
    <input type='text' name='schedule_dot_li_content_settings[schedule_dot_li_date_content_value_field]' value='<?php

    echo mik_mge_filter_schedule_add_action_plugin_return_valid_value_to_schedule('schedule_dot_li_date_content_value_field');

    ?>' cols='' style='width:100%' >
    <?php
}


function schedule_dot_li_time_field_section_render() {

    $options = get_option( 'schedule_dot_li_content_settings' );
    ?>
    <input type='text' name='schedule_dot_li_content_settings[schedule_dot_li_time_content_value_field]' value='<?php

    echo mik_mge_filter_schedule_add_action_plugin_return_valid_value_to_schedule('schedule_dot_li_time_content_value_field');
    ?>' cols='' style='width:100%' >
    <?php
}

function schedule_dot_li_email_field_section_render() {
    $options = get_option( 'schedule_dot_li_content_settings' );
    ?>
    <input type='text' name='schedule_dot_li_content_settings[schedule_dot_li_email_content_value_field]' value='<?php

    echo mik_mge_filter_schedule_add_action_plugin_return_valid_value_to_schedule('schedule_dot_li_email_content_value_field');

    ?>' cols='' style='width:100%' >
    <?php
}

function schedule_dot_li_phone_field_section_render() {
    $options = get_option( 'schedule_dot_li_content_settings' );
    ?>
    <input type='text' name='schedule_dot_li_content_settings[schedule_dot_li_phone_content_value_field]' value='<?php

    echo mik_mge_filter_schedule_add_action_plugin_return_valid_value_to_schedule('schedule_dot_li_phone_content_value_field');

    ?>' cols='' style='width:100%' >
    <?php
}

function schedule_dot_li_submit_field_section_render() {
    $options = get_option( 'schedule_dot_li_content_settings' );
    ?>
    <input type='text' name='schedule_dot_li_content_settings[schedule_dot_li_submit_content_value_field]' value='<?php

    echo mik_mge_filter_schedule_add_action_plugin_return_valid_value_to_schedule('schedule_dot_li_submit_content_value_field');

    ?>' cols='' style='width:100%' >
    <?php
}

# Save
function schedule_dot_li_configuration_content_page() {
    define('SCHEDULE_PLUGIN_PATH', plugin_dir_url( __FILE__ ));
    wp_register_style('kv_js_time_style' , SCHEDULE_PLUGIN_PATH. 'css/jquery-ui-timepicker-addon.css');
    wp_enqueue_style('kv_js_time_style');
    if (is_ssl()) {
        $protocol = 'https://';
    }
    else {
        $protocol = 'http://';
    }
    wp_enqueue_style('jquery-style', $protocol.'ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
    wp_enqueue_script('jquery-script', $protocol.'code.jquery.com/ui/1.10.4/jquery-ui.js');
    wp_enqueue_script('jquery-time-picker' ,  SCHEDULE_PLUGIN_PATH. 'js/jquery-ui-timepicker-addon.js',  array('jquery' ));
    myprefix_function_tw_seconds_to_run();
    ?>
    <h2>Schedule new alarm note</h2>
    <form action="../wp-content/plugins/schedule/save_note.php" method="post">
        <span>Subject:</span><br><input id="subject" type="text" name="subject"><br>
        <span>Alarm date:</span><br><input type="text" id="datetime" name="datetime"><br>
        <span>Description:</span><br><textarea cols="50" rows="10" name="description"></textarea><br>
        <input type="submit" value="Schedule Alarm">
    </form>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $('#datetime').datetimepicker({
                timeFormat: "HH:mm",
                dateFormat : 'yy-mm-dd'
            });
            $('#datetime').on('click', function(e) {
                e.preventDefault();
                $(this).attr("autocomplete", "off");
            });
            $('#datetime').attr("autocomplete", "off");
            $('#subject').on('click', function(e) {
                e.preventDefault();
                $(this).attr("autocomplete", "off");
            });
            $('#subject').attr("autocomplete", "off");
        });

    </script>



    <?php

    global $wpdb;
    $table_name = $wpdb->prefix . 'schedule';
    $charset_collate = $wpdb->get_charset_collate();
    $now = time();
    $results = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE (status=1 OR status=0) AND alarm < now();", ARRAY_A );
    if (!empty($results))
    {
        echo '<h2>To check alarms</h2>';
        foreach ($results as $r)
        {
            $start = date_create($r['alarm']);
            $end = date_create(date('Y-m-d H:i:s'));
            $diff=date_diff($end,$start);
            $elapsedTime = '';
            if ($diff->d > 0) $elapsedTime .= $diff->d.' days';
            if ($diff->h > 0) {
                if (strlen($elapsedTime) > 0)
                {
                    $elapsedTime .= ','.$diff->h.' hours';
                }
                else {
                    $elapsedTime .= ' '.$diff->h.' hours';
                }
            }
            if ($diff->i > 0)
            {
                if (strlen($elapsedTime) > 0)
                {
                    $elapsedTime .= ' and '.$diff->i.' minutes';
                }
                else {
                    $elapsedTime .= ' '.$diff->i.' minutes';
                }
            }
            if (strlen($elapsedTime) > 0) {
                $elapsedTimeString = '('.$elapsedTime.' ago)';
            }
            echo '<pre><a href="../wp-content/plugins/schedule/read_note.php?id='.$r['id'].'">'.esc_html($r['subject']).'</a> <span style="font-size: 11px;">('.$elapsedTimeString.')</span></pre>';
        }
        echo '<hr>';
    }

    $results = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE status=1 AND alarm > now();", ARRAY_A );
    if (!empty($results))
    {
        echo '<h2>Active alarms</h2>';
        foreach ($results as $r)
        {
            echo '<pre><a href="../wp-content/plugins/schedule/read_note.php?id='.$r['id'].'">'.esc_html($r['subject']).'</a> <span style="font-size: 11px;">Schedule time <strong>'.$r['alarm'].'</strong></span></pre>';
        }
        echo '<hr>';
    }

    $results = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE status=2 AND alarm < now();", ARRAY_A );
    if (!empty($results)) {
        echo '<h2>Checked alarms</h2>';
        foreach ($results as $r)
        {
            $start = date_create($r['alarm']);
            $end = date_create(date('Y-m-d H:i:s'));
            $diff=date_diff($end,$start);
            $elapsedTime = '';
            if ($diff->d > 0) $elapsedTime .= $diff->d.' days';
            if ($diff->h > 0) {
                if (strlen($elapsedTime) > 0)
                {
                    $elapsedTime .= ', '.$diff->h.' hours';
                }
                else {
                    $elapsedTime .= ' '.$diff->h.' hours';
                }
            }
            if ($diff->i > 0)
            {
                if (strlen($elapsedTime) > 0)
                {
                    $elapsedTime .= ' and '.$diff->i.' minutes';
                }
                else {
                    $elapsedTime .= ' '.$diff->i.' minutes';
                }
            }
            if (strlen($elapsedTime) > 0) {
                $elapsedTimeString = $elapsedTime.' ago';
            }
            echo '<pre><a href="../wp-content/plugins/schedule/read_note.php?id='.$r['id'].'">'.esc_html($r['subject']).'</a> <span style="font-size: 11px;">('.$elapsedTimeString.')</span></pre>';
        }
    }
}

function mik_mge_filter_schedule_add_action_plugin_return_valid_value_to_schedule($key)
{
    $options = get_option( 'schedule_dot_li_content_settings' );
    if ( isset( $options[$key] ) && !empty( $options[$key] )) {
        return esc_html($options[$key]);
    }
    else {
        $defaultValues = [
            'schedule_dot_li_call_to_action_content_value_field' => 'Request a demo',
            'schedule_dot_li_date_content_value_field' => 'Date',
            'schedule_dot_li_time_content_value_field' => 'Time',
            'schedule_dot_li_name_content_value_field' => 'Your Name (required)',
            'schedule_dot_li_email_content_value_field' => 'Your Email (required)',
            'schedule_dot_li_phone_content_value_field' => 'Phone',
            'schedule_dot_li_submit_content_value_field' => 'Request a demo'
        ];
        return $defaultValues[$key];
    }
}

# Uninstall plugin
register_uninstall_hook( __FILE__, 'uninstall_mik_ge_cs_schedule_plugin_action' );
function uninstall_mik_ge_cs_schedule_plugin_action() {
    $options = get_option( 'schedule_dot_li_content_settings' );
    # Clear at uninstall
    $option_to_delete = 'schedule_dot_li_content_settings';
    delete_option( $option_to_delete );
}
