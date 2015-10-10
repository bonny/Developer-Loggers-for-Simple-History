<?php

return;

class BackUpWordPress_pluginlogger extends SimpleLogger {

	public $slug = __CLASS__;

	function getInfo() {

		$arr_info = array(
			"name" => "BackUpWordPress",
			"description" => "Logs backups",
			"capability" => "manage_options",
			"messages" => array(
				'backup_done' => __('Got a 404-page when trying to visit "{request_uri}"', "simple-history"),
			),
			"labels" => array(
				"search" => array(
					"label" => _x("Pages not found (404 errors)", "User logger: 404", "simple-history"),
					"options" => array(
						_x("Pages not found", "User logger: 404", "simple-history") => array(
							"page_not_found",
						),
					),
				), // end search
			), // end labels
		);

		return $arr_info;

	}

	function loaded() {

		add_action("hmbkp_backup_complete", array($this, "on_hmbkp_backup_complete"), 10, 1);

	}

	function on_hmbkp_backup_complete($backup) {
		#error_log(__CLASS__ . " on_hmbkp_backup_complete");
		$this->info("Backup done", array(
			"backup_type" => $backup->get_type(),
			"backup_archive_filepath" => $backup->get_archive_filepath(),
			"backup_archive_filename" => $backup->get_archive_filename(),
			"backup_database_dump_filepath" => $backup->get_database_dump_filepath(),
			"backup_database_dump_filename" => $backup->get_database_dump_filename(),
			"backup_archive_method" => $backup->get_archive_method(),
			"backup_mysqldump_method" => $backup->get_mysqldump_method(),
		));

	}

}
