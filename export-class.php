<?php
/**
 * @file Class to export entries from a gravity form to a csv file
 * @author Tom Jenkins tom@itsravenous.com
 */

require_once('./gravity-class.php');

Class rv_gravity_export {

	public function __construct ($options) {
		
	}

	public function export_entries($options) {

		$form_id = $options['form_id'];
		$form = rv_gravity::get_form($form_id);
		$fields = rv_gravity::get_form_fields($form_id);
		$entries = rv_gravity::get_form_entries($form_id);

		$csv_rows = array();
		foreach ($entries as $entry) {
			$csv_row = array();
			$in_checkbox = FALSE;
			foreach ($fields as $field) {

				if ($field->type == 'section' || $field->type == 'page') {
					$csv_row[] = '-';
					continue;
				}

				$ids = array();
				if ($field->inputs) {
					foreach ($field->inputs as $input) {
						$ids[] = $input->id;
					}
				} else {
					$ids = array($field->id);
				}

				if (!$in_checkbox) {
					// Get value for field from entry
					if ($field->type == 'checkbox') {
						// Get value for field from entry
						$values = array();
						foreach ($ids as $id) {
							$value = array_filter($entry->values, function ($val) use($id) {
								return (string) $val->field_id == (string) $id;
							});
							if (empty($value)) {
								$values[] = '-';
							} else {
								$value_reduced = end($value);
								$values[] = $value_reduced->value;
							}
						}
						$csv_row[] = str_replace('-; ', '', str_replace('; -', '', implode('; ', $values)));
					} else {
						foreach ($ids as $id) {
							$value = array_filter($entry->values, function ($val) use($id) {
								return (string) $val->field_id == (string) $id;
							});
							if (empty($value)) {
								$value = '-';
							} else {
								$value = end($value);
								$value = $value->value;
							}

							$csv_row[] = $value;
						}
					}
				} else {
					$csv_row[] = '-';
				}

				// Skip inner checkbox fields - we aggregate values from parent
				$in_checkbox = ($field->type == 'checkbox' && count($field->inputs) > 1) || ($in_checkbox && strpos($ids[0], '.') !== FALSE);
			}

			// Add entry meta
			$csv_row[] = $entry->date_created;
			$csv_row[] = $entry->ip;
			$csv_row[] = $entry->source_url;
			$csv_row[] = $entry->user_agent;

			$csv_rows[]= $csv_row;
		}
		
		// Format CSV header from fields array
		$csv_header = rv_gravity::get_form_labels_by_id($form_id, array(
			'show_sections' => TRUE,
			'remove_toplevel_field_labels' => TRUE,
		));

		// Add meta fields to header
		$csv_header[] = 'Date submitted';
		$csv_header[] = 'IP Address';
		$csv_header[] = 'Source URL';
		$csv_header[] = 'User Agent';

		// Build filename
		$filename = 'export/export-'.preg_replace('/[^A-Za-z0-9-]+/', '-', $form->title).'-'.date('Y-m-d-H-i-s').'.csv';

		// Write to buffer
		$output = fopen($filename, 'w') or die("Can't open $filename");
		fputcsv($output, $csv_header);
		foreach($csv_rows as $row) {
		    fputcsv($output, $row);
		}

		// Serve buffer as download
		fclose($output) or die("Can't close $filename");

		return $filename;
		
	}

}

?>
