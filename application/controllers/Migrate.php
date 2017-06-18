<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migrate controller 
 *
 * to run migration 
 * first set migration version in config/migration.php
 * then run this controller
 */
class Migrate extends CI_Controller
{

        public function index()
        {
                $this->load->library('migration');

                if ($this->migration->current() === FALSE)
                {
                        show_error($this->migration->error_string());
                }
                else 
                {
                	$migrate = $this->migration->find_migrations();
                        foreach ($migrate as $key => $value) 
                        {
                                echo '<p>' . $key . ' - ' . $value . '</p>' ;
                        }
                }
        }

}