<?php
/**
 * @file Gravity forms helper/model class
 * @author Tom Jenkins tom@itsravenous.com
 */

Class rv_gravity {
	public static function get_form($form_id) {
		$result = mysql_query("SELECT * FROM wp_rg_form WHERE id='$form_id' LIMIT 1");
		if ($result) {
			$form = mysql_fetch_object($result);
		} else {
			die("Couldn't find form with id $form_id");
		}

		return $form;
	}

	public static function get_form_meta($form_id) {
		$result = mysql_query("SELECT display_meta FROM wp_rg_form_meta WHERE form_id='$form_id' LIMIT 1");

		$row = mysql_fetch_object($result);
		$meta = unserialize($row->display_meta);;
		return $meta;
	}

	public static function get_form_fields($form_id) {
		$meta = self::get_form_meta($form_id);
		return $meta['fields'];
	}

	public static function get_entry_detail($entry_id) {
		$result = mysql_query("SELECT * FROM wp_rg_lead_detail WHERE lead_id=$entry_id");

		$values = array();
		while ($row = mysql_fetch_object($result)) {
			$values[] = $row;
		}

		return $values;
	}

	public static function get_form_labels_by_id($form_id) {
		// Get fields
		$fields = self::get_form_fields($form_id);

		$labels_by_id = array();
		foreach($fields as $field) {
			if ($field['inputs'] && $field['type'] != 'checkbox') {
				foreach ($field['inputs'] as $input) {
					$labels_by_id[(string) $input['id']] = $input['label'];
				}
			} else {
				$labels_by_id[(string) $field['id']] = $field['label'];
			}
		}

		return $labels_by_id;
	}

	public static function get_form_entries($form_id) {

		// Get labels keyed by ID
		$labels_by_id = self::get_form_labels_by_id($form_id);
		
		// Get entries
		$result = mysql_query("SELECT * from wp_rg_lead WHERE form_id=$form_id");
		$entries = array();
		while ($row = mysql_fetch_object($result)) {
			$values = self::get_entry_detail($row->id);
			$values = array_map(function ($value) use($labels_by_id) {
				$new_value = new StdClass();
				$new_value->label = $labels_by_id[(string) $value->field_number];
				$new_value->value = $value->value;
				$new_value->field_id = $value->field_number;

				return $new_value;
			}, $values);
			$row->values = $values;
			$entries[] = $row;
		}

		return $entries;
	}
}

?>