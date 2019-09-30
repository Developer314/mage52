<?php
namespace Revolve\Mage25\Model\ResourceModel;
class File extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     * https://www.ashsmith.io/magento2/module-from-scratch-module-part-2-models/
     */
    
    
    
    
    protected function _construct()
    {
    	$this->_init('mage25_files', 'fID');   //here id is the primary key of custom table
    	
    	
    }
    
    
    public function getFileByID($fID){
	 	$adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getMainTable(), '*')
            ->where('fID = :fID');
        $binds   = ['fID' => $fID];
        
		return $adapter->fetchRow($select, $binds);   
    }
    
    public function getFileSets(){
	    $adapter = $this->getConnection();
       
        $select  = $adapter->select()
            ->from('mage25_file_sets', '*');
       
		return $adapter->fetchAll($select);
    }
    
    public function addUpdateFileset($fsName, $fsID){
	    $data = ['fsName'=>$fsName];
	    $adapter = $this->getConnection();
	    if($fsID>0){
			$data = $adapter->update('mage25_file_sets', $data, ['fsID = ?'=>$fsID]);
		}else{
			$data = $adapter->insert('mage25_file_sets', $data);
		}
		
	    return true;
    }
    
    public function deleteFileSet($fsID){
	     $adapter = $this->getConnection();
	     $adapter->query("DELETE FROM mage25_file_sets where fsID=".$fsID);
	     $adapter->query("DELETE FROM mage25_file_set_files where fsID=".$fsID);
	     return true;
    }
    
    public function deleteFile($fID){
	    $adapter = $this->getConnection();
	    $adapter->query("DELETE FROM mage25_files where fID=".$fID);
	    return true;
    }
    
    public function removeFromFileSets($fID){
	    $adapter = $this->getConnection();
	    $adapter->query("DELETE FROM mage25_file_set_files where fID=".$fID);
	    return true;
	     
    }
    
	public function updateFileSetFileSortorder($fsID, $fIDs){
		$adapter = $this->getConnection();
		$i=1;
		foreach($fIDs as $fID){
	    	$adapter->query("UPDATE mage25_file_set_files set sort_order=".$i." where fsID=".$fsID." AND fID=".$fID);
	    	$i++;
	    }
	    return true;
	}
    
    public function getFidsFromFsID($fsID){
	    $adapter = $this->getConnection();
       
        $select  = $adapter->select()
            ->from('mage25_file_set_files', '*')
             ->where('fsID = :fsID')
             ->order('sort_order','DESC');
        $binds   = ['fsID' => $fsID];
       
		return $adapter->fetchAll($select, $binds);
    }
    
    public function saveFile($data, $fileSets = array()){
	    $adapter = $this->getConnection();
	    $adapter->insert($this->getMainTable(), $data);
	    $fID = $adapter->lastInsertId($this->getMainTable());
	    //get Last FSID's Sort order
	    
	    if(sizeof($fileSets) > 0){
		    foreach($fileSets as $fsID){
			    $sortOrder = $this->getFidsFromFsID($fsID);
			    $sortOrder = sizeof($sortOrder);
			    $data = ['fsID'=>$fsID,'fID'=>$fID,'sort_order'=>($sortOrder+1)];
			    $adapter->insert('mage25_file_set_files', $data);
		    }
	    }
	    return true;
    }
    
    public function updateFile($data, $fileSets, $fID){
	    
	    $adapter = $this->getConnection();
	    $data = $adapter->update('mage25_files', $data, ['fID = ?'=>$fID]);
		
		if(sizeof($fileSets) > 0){
			
			$this->removeFromFileSets($fID);
			
		    foreach($fileSets as $fsID){
			    $sortOrder = $this->getFidsFromFsID($fsID);
			    $sortOrder = sizeof($sortOrder);
			    $data1 = ['fsID'=>$fsID,'fID'=>$fID,'sort_order'=>($sortOrder+1)];
			    $adapter->insert('mage25_file_set_files', $data1);
		    }
	    }
		
	    return true;
    }
    
    public function getFileSetsOfFile($fID){
	    $adapter = $this->getConnection();
       
        $select  = $adapter->select()
            ->from('mage25_file_set_files', '*')
             ->where('fID = :fID');
        $binds   = ['fID' => $fID];
       
		$fsIDs = $adapter->fetchAll($select, $binds);
		$newFSIDS = array();
		foreach($fsIDs as $ids){
			array_push($newFSIDS, $ids['fsID']);
		}
		return $newFSIDS;
    }
    
    public function getFilesByFileSetID($fsID){
	    return false;
	    
    }
    
   
    
    
    
    
}