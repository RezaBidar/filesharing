<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_file extends CI_Migration {
	
	public function up()
	{
		$prefix = "fil" ;
		$table_name = $this->db->dbprefix("file");	
		$user_prefix = "usr" ;
		$user_table = $this->db->dbprefix("user") ;
		//type > 1 = file , 2 = folder
		//link_mode > 0 = public , 1 = private
		$this->db->query(
				"CREATE TABLE {$table_name} (
				{$prefix}_id INT(11) NOT NULL AUTO_INCREMENT ,
				{$prefix}_name VARCHAR(125) NULL ,
				{$prefix}_file_type VARCHAR(125) NULL ,
				{$prefix}_file_ext VARCHAR(125) NULL ,
				{$prefix}_file_size DECIMAL(10,2) NULL ,
				{$prefix}_hash_name VARCHAR(125) NULL ,
				{$prefix}_link_id VARCHAR(125) NULL ,
				{$prefix}_link_mode INT(1) DEFAULT 0 ,
				{$prefix}_type INT(2) DEFAULT 1 ,
				{$prefix}_is_image BOOLEAN DEFAULT FALSE ,
				{$prefix}_image_width INT(2) NULL ,
				{$prefix}_image_height INT(2) NULL ,
				{$prefix}_image_type VARCHAR(125) NULL ,
				{$prefix}_user_id INT(11) NOT NULL , 
				{$prefix}_parent_id INT(11) NULL , 
				CONSTRAINT fil_pk PRIMARY KEY ({$prefix}_id) ,
				CONSTRAINT fil_user_fk FOREIGN KEY ({$prefix}_user_id) REFERENCES {$user_table} ({$user_prefix}_id) ON DELETE RESTRICT ON UPDATE CASCADE ,
				CONSTRAINT fil_parent_fk FOREIGN KEY ({$prefix}_parent_id) REFERENCES {$table_name} ({$prefix}_id) ON DELETE RESTRICT ON UPDATE CASCADE   
				) ENGINE=INNODB
				  DEFAULT CHARSET = utf8  
				  DEFAULT COLLATE = utf8_unicode_ci
				  ;"
						);
	}

	public function down()
	{
		$this->dbforge->drop_table('file');
	}

}