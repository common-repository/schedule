<body style="font-family: Arial; margin: 20px;">
<?php
require_once('../../../wp-load.php');

global $wpdb;

$id = esc_sql($_GET['id']);

$table_name = $wpdb->prefix . 'schedule';

$charset_collate = $wpdb->get_charset_collate();

$results = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE id={$id}", ARRAY_A );

$note = date_create($results[0]['alarm']);
$now = date_create(date('Y-m-d H:i:s'));

if ($note < $now) {
    $update = $wpdb->get_results( "UPDATE {$table_name} SET status=2 WHERE id={$id}", ARRAY_A );
}
$description = esc_html($results[0]['description']);
echo '<h1>'.esc_html($results[0]['subject']).'</h1>';
echo '<h2 style="font-size: 12px;">Schedule time: ('.esc_html($results[0]['alarm']).')</h2>';
echo '<p style="border: solid 1px #e6e1e1; padding: 10px;">'.str_replace('\r\n','<br>',$description).'</p>';

echo '<br><br><a class=inline" style="text-decoration: none; background-color: #d2d2da; color: #000; padding: 10px; margin: 10px; font-size: 11px;" href="../../../wp-admin/admin.php?page=schedule_dot_li_configuration">&larr; BACK</a>';
echo '<a class=inline" style="text-decoration: none; background-color: #dc3d3d; color: white; padding: 10px; margin: 10px; font-size: 11px;" href="delete_note.php?id='.$id.'" onclick="return confirm(\'DELETE. Are you sure?\')">DELETE</a>';
?>
<div style="margin-top: 50px;">
    <span style="font-size: 9px;"><strong>Schedule</strong> Wordpress Plugin. Simple. Powerful.</span>
</div>
</body>

<style>
    .inline {
        display: inline-block;
        border: 1px solid red;
        margin:10px;
    }
</style>

