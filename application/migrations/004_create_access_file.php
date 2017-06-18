<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_access_file extends CI_Migration {
	
	public function up()
	{
		$prefix = "acf" ;
		$table_name = $this->db->dbprefix("access_file");	
		$file_prefix = "fil" ;
		$file_table = $this->db->dbprefix("file") ;
		$user_prefix = "usr" ;
		$user_table = $this->db->dbprefix("user") ;
		$this->db->query(
				"CREATE TABLE {$table_name} (
				{$prefix}_id INT(11) NOT NULL AUTO_INCREMENT ,
				{$prefix}_user_id INT(11) NOT NULL , 
				{$prefix}_file_id INT(11) NOT NULL , 
				CONSTRAINT acf_pk PRIMARY KEY ({$prefix}_id) ,
				CONSTRAINT acf_user_fk FOREIGN KEY ({$prefix}_user_id) REFERENCES {$user_table} ({$user_prefix}_id) ON DELETE RESTRICT ON UPDATE CASCADE ,
				CONSTRAINT acf_file_fk FOREIGN KEY ({$prefix}_file_id) REFERENCES {$file_table} ({$file_prefix}_id) ON DELETE RESTRICT ON UPDATE CASCADE 
				) ENGINE=INNODB
				  DEFAULT CHARSET = utf8  
				  DEFAULT COLLATE = utf8_unicode_ci
				  ;"
						);
	}

	public function down()
	{
		$this->dbforge->drop_table('access_file');
	}

}