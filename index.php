<?php
/**
 * @file Class to programmatically export entries from a Gravity Form
 * @author Tom Jenkins tom@itsravenous.com
 */

// Dependencies
require('export-class.php');

// Config
require('../wp-config.php');
require('./config.php');

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
$exporter->export_entries($config);

?>
