<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_access_user extends CI_Migration {
	
	public function up()
	{
		$prefix = "acu" ;
		$table_name = $this->db->dbprefix("access_user");	
		$user_prefix = "usr" ;
		$user_table = $this->db->dbprefix("user") ;
		$this->db->query(
				"CREATE TABLE {$table_name} (
				{$prefix}_id INT(11) NOT NULL AUTO_INCREMENT ,
				{$prefix}_level INT(11) NOT NULL , 
				{$prefix}_user_id INT(11) NOT NULL , 
				{$prefix}_friend_id INT(11) NOT NULL , 
				CONSTRAINT acu_pk PRIMARY KEY ({$prefix}_id) ,
				CONSTRAINT acu_user_fk FOREIGN KEY ({$prefix}_user_id) REFERENCES {$user_table} ({$user_prefix}_id) ON DELETE RESTRICT ON UPDATE CASCADE ,
				CONSTRAINT acu_friend_fk FOREIGN KEY ({$prefix}_friend_id) REFERENCES {$user_table} ({$user_prefix}_id) ON DELETE RESTRICT ON UPDATE CASCADE 
				) ENGINE=INNODB
				  DEFAULT CHARSET = utf8  
				  DEFAULT COLLATE = utf8_unicode_ci
				  ;"
						);
	}

	public function down()
	{
		$this->dbforge->drop_table('access_user');
	}

}