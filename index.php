<?php
/**
 * @file Class to programmatically export entries from a Gravity Form
 * @author Tom Jenkins tom@itsravenous.com
 */

// Dependencies
require('export-class.php');
require('../wp-config.php');

$db = array(
	'host' => DB_HOST,
	'user' => DB_USER,
	'password' => DB_PASSWORD,
	'name' => DB_NAME,
);

// // Create exporter
$exporter = new rv_gravity_export(array(
	'db' => $db,
));

// Export form entries
$export_options = array(
	'form_id' => 1,
);
$exporter->export_entries($export_options);


?>
