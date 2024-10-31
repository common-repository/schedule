<?php

require_once('../../../wp-load.php');

global $wpdb;

$table_name = $wpdb->prefix . 'schedule';

$charset_collate = $wpdb->get_charset_collate();

$now = time();

if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name ) {
    error_log( 'Table exists' . ': ' . print_r( $table_name, true ) );
} else {
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    subject tinytext NOT NULL,
    alarm datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    description text NOT NULL,
    status int(11) NOT NULL,
    PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

$subject = esc_sql($_POST['subject']);
$alarm = strtotime($_POST['datetime']);
$datetime = date_create(new DateTimeZone( 'UTC' ))->format($alarm);

$da = new DateTime("@$datetime");
$description = esc_sql($_POST['description']);
$status = 1;
$wpdb->insert( $table_name, array(
    'subject' => $subject,
    'alarm' => $da->format('Y/m/d H:i:s'),
    'description' => $description,
    'status' => $status
) );

header("location: ../../../wp-admin/admin.php?page=schedule_dot_li_configuration");

?>
