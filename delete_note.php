<?php
require_once('../../../wp-load.php');

global $wpdb;

$id = esc_sql($_GET['id']);

$table_name = $wpdb->prefix . 'schedule';

$charset_collate = $wpdb->get_charset_collate();

$results = $wpdb->get_results( "DELETE FROM {$table_name} WHERE id={$id}", ARRAY_A );

header("location: ../../../wp-admin/admin.php?page=schedule_dot_li_configuration");
?>
