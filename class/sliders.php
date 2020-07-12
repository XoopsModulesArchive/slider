<?php
class Sliders
{
	
	 var $db;
     var $table;
	 var $contenttable;
	 var $totalsliders;
	 
	function __construct()
	{
		$this->db =& Database::getInstance();
        $this->table = $this->db->prefix("slider");
		$this->contenttable = $this->db->prefix("slider_content");
		
		$sql = "SELECT count('id') FROM ".$this->table;
		$countresult =$this->db->query($sql);
		list($fullnumrows) = $this->db->fetchRow($countresult);
		
		$this->totalsliders =$fullnumrows;	
	}
	
	function getAllSliders($min=0, $show=10, $order = "mydate DESC")
	{
		global $myts;
		$myts =& MyTextSanitizer::getInstance();
		
		$return_array = array();
		
		$sql = "SELECT * FROM ".$this->table." ORDER BY ".$order;
		$result =$this->db->query($sql,$show,$min);
		
		$i = 0;
		$j =0;
		while($myrow = $this->db->fetchArray($result)) 
		{
			$return_array[$i]['aa'] = $i+1;
			$return_array[$i]['id'] = $myrow['id'];
			$return_array[$i]['title'] = $myts->htmlSpecialChars($myrow['title']);
			$return_array[$i]['description'] = $myts->undohtmlSpecialChars($myrow['description']);
			$return_array[$i]['date'] = date("d/m/Y",$myrow['mydate']);
			$return_array[$i]['timestamp'] = $myrow['mydate'];
			$return_array[$i]['active'] = $myrow['active'];
			
			$slidessql = "SELECT * FROM ".$this->contenttable." WHERE sid=".$myrow['id'];
			$slideresult =$this->db->query($slidessql);
			
			while($myrow = $this->db->fetchArray($slideresult)) 
			{
				$return_array[$i]['slides'][$j] = $myrow;
				$j++;
			}
			$i++;
		} 
		return $return_array;
	}
	
	function GetSliderbyID($id)
	{
		global $myts;
		$myts =& MyTextSanitizer::getInstance();
		
		$return_array = array();
		
		$sql = "SELECT * FROM ".$this->table." WHERE id=".$id;
		$result =$this->db->query($sql);
		
		$i = 0;
		$j=0;
		while($myrow = $this->db->fetchArray($result)) 
		{
			
			$return_array[$i]['aa'] = $i+1;
			$return_array[$i]['id'] = $myrow['id'];
			$return_array[$i]['title'] = $myts->htmlSpecialChars($myrow['title']);
			$return_array[$i]['description'] = $myts->undohtmlSpecialChars($myrow['description']);
			$return_array[$i]['date'] = date("d/m/Y",$myrow['mydate']);
			$return_array[$i]['timestamp'] = $myrow['mydate'];
			$return_array[$i]['active'] = $myrow['active'];
			
			$slidessql = "SELECT * FROM ".$this->contenttable." WHERE sid=".$id;
			$slideresult =$this->db->query($slidessql);
			
			while($myrow = $this->db->fetchArray($slideresult)) 
			{
				$return_array[$i]['slides'][$j] = $myrow;
				$j++;
			}
			$i++;
		} 
		
		return $return_array;
	}
	
	function getActiveSlider()
	{
		global $myts;
		$myts =& MyTextSanitizer::getInstance();
		$sql = "SELECT * FROM ".$this->table." WHERE active=1";
		$result =$this->db->query($sql);
		$j =0;
		$i =0;
		$return_array = array();
		
		while($myrow = $this->db->fetchArray($result)) 
		{
			$return_array['id'] = $myrow['id'];
			$return_array['title'] = $myts->htmlSpecialChars($myrow['title']);
			$return_array['description'] = $myts->undohtmlSpecialChars($myrow['description']);
			$return_array['date'] = date("d/m/Y",$myrow['mydate']);
			$return_array['timestamp'] = $myrow['mydate'];
			$return_array['active'] = $myrow['active'];
			
			$slidessql = "SELECT * FROM ".$this->contenttable." WHERE sid=".$myrow['id'];
			$slideresult =$this->db->query($slidessql);
			
			while($myrow = $this->db->fetchArray($slideresult)) 
			{
				$return_array[$i]['slides'][$j] = $myrow;
				$j++;
			}
			$i++;
		} 
		return $return_array;
	}
	
	function getActiveSliderID()
	{
		global $myts;
		$myts =& MyTextSanitizer::getInstance();
		$sql = "SELECT * FROM ".$this->table." WHERE active=1";
		$result =$this->db->query($sql);
		$return_array = array();
		while($myrow = $this->db->fetchArray($result)) 
		{
			$return_array['id'] = $myrow['id'];
		} 
		return $return_array['id'];
	}
	
	function GetSlidebyID($id)
	{
		global $myts;
		$myts =& MyTextSanitizer::getInstance();
		
		$return_array = array();
		
		$sql = "SELECT * FROM ".$this->contenttable." WHERE id=".$id;
		$result =$this->db->query($sql);
		
		$i = 0;
		
		while($myrow = $this->db->fetchArray($result)) 
		{
			
			$return_array[$i]['aa'] = $i+1;
			$return_array[$i]['id'] = $myrow['id'];
			$return_array[$i]['sid'] = $myrow['sid'];
			$return_array[$i]['title'] = $myts->htmlSpecialChars($myrow['title']);
			$return_array[$i]['description'] = $myts->undohtmlSpecialChars($myrow['description']);
			$return_array[$i]['image'] = $myrow['image'];
			$i++;
		} 
		
		return $return_array;
	}
	
	function getCount()
	{
		return $this->totalsliders;
	}
}
?>