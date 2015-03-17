<?php
/**
 * @file Class to programmatically export entries from a Gravity Form
 * @author Tom Jenkins tom@itsravenous.com
 */

// Dependencies
require('export-class.php');
require('lib/mail/class.phpmailer.php');

// Config
require('./config.php');
require($config['wp_root'].'/wp-config.php');

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

// Export form entries to CSV file
$options = $config;
$options['date_from'] = strtotime('-1 day');
$csv_file = $exporter->export_entries($options);

// Email CSV to addresses defined in config
$mail = new PHPMailer;
$mail->Host = $config['mail']['host'];
$mail->From	= $config['mail']['from'];
$mail->FromName = $config['mail']['from_name'];

foreach ($config['mail']['recipients'] as $recipient) {
	$mail->AddAddress($recipient);
}
$mail->IsHTML(true);
$mail->Subject = $config['mail']['subject'];
$mail->AddAttachment($csv_file);
$mail->Body = "Please find attacted file $csv_file.";

if($mail->Send()){
	echo 'mail sent';
	unlink($csv_file);
	die;
}else{
	echo 'sending error';
}

?>
