<?php
/**
 * @file Class to programmatically export entries from a Gravity Form
 * @author Tom Jenkins tom@itsravenous.com
 */

// Dependencies
require('export-class.php');
require('lib/mail/class.phpmailer.php');

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

// Export form entries to CSV file
$csv_file = $exporter->export_entries($config);

// Email CSV to addresses defined in config
$mail = new PHPMailer;
$mail->Host = "mail.grantuk.com";
$mail->From	= "service@grantuk.com";
$mail->FromName ="grantuk.com";

foreach ($config['recipients'] as $recipient) {
	$mail->AddAddress($recipient);
}
$mail->IsHTML(true);
$mail->Subject = "Export from product registration form";
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
