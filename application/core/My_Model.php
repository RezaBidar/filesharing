<?php
/**
 * My Base Model
 *
 * CRUD functions 
 * 
 * @package FileSharing
 */
class My_Model extends CI_Model{
	
	protected $_table_name = '';
	protected $_primary_key = '';
	
	
	public function get($id=NULL,$single=NULL,$custom_method = NULL)
	{
		$method ='';
		if($id!=NULL)
		{
			$this->db->where($this->_primary_key,$id);
			$method = 'row';
		}
		elseif ($single)
		{
			$method = 'row';
		}
		else 
		{
			$method = 'result';
		}
		
		// if custom method didnt null then method = custom method 
		$method = ($custom_method != NULL) ? $custom_method : $method ;
		
		return $this->db->get($this->_table_name)->$method();
	}
	
	/**
	 * 
	 * @param array $where
	 * @param string $single
	 */
	public function get_by($where, $single=FALSE )
	{
		$this->db->where($where);
		return $this->get(NULL,$single);
	}
	
	public function save($data,$id = NULL)
	{
	    
		//insert 
		if($id === NULL)
		{
			isset($data[$this->_primary_key]) || $data[$this->_primary_key] = NULL; 
			$this->db->set($data);
			$this->db->insert($this->_table_name);
			$id = $this->db->insert_id();
			return $id;
		}	
		else 
		{ // update
			$this->db->set($data);
			$this->db->where($this->_primary_key,$id);
			$this->db->update($this->_table_name);	
			return $id ;
		}

	}
	
	public function delete($data)
	{
	    
		if(!$data)
		{
			return FALSE;
		}
		else
		{
			if(is_array($data)) 
				$this->db->where($data);
			else 
				$this->db->where($this->_primary_key,$data);
			$this->db->limit(0);
			$this->db->delete($this->_table_name);
			return TRUE ;
		}
		
		return FALSE ;
	}

	public function get_table_name()
	{
		return $this->_table_name ;
	}
	
}