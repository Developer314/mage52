<?php
namespace Revolve\Mage25\Model\ResourceModel;
class Block extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     * https://www.ashsmith.io/magento2/module-from-scratch-module-part-2-models/
     */
    
    
    
    
    protected function _construct()
    {
    	$this->_init('mage25_block_types', 'btID');   //here id is the primary key of custom table
    	
    	
    }
    
    
    public function install($data, $sql){
	    //$data = ['arHandle'=>$arHandle];
	    $adapter = $this->getConnection();
		$data = $adapter->insert($this->getMainTable(), $data);
		if(is_array($sql)){
			foreach($sql as $sq){
				$adapter->query($sq);
			}
		}else{
			if(trim($sql) !=''){
				$adapter->query($sql);
			}
		}
	    return $this->getBlockByHandle($data['btHandle']);
    }
    
    public function getBlockByHandle($btHandle){
	 	$adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getMainTable(), '*')
            ->where('btHandle = :btHandle');
        $binds   = ['btHandle' => $btHandle];
        
		return $adapter->fetchRow($select, $binds);   
    }
    
    public function installBlockTable($sql){
	    //$adapter = $this->getConnection();
	    
	    
	    
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	    $installer = $objectManager->get('Magento\Framework\Setup\SchemaSetupInterface');
	    
	   $installer->startSetup();
        $installer->getConnection()->query($sql);
        $installer->endSetup();
	   
    }
    
    public function getInstalledBlockTypes($btActive = 1){
	    $adapter = $this->getConnection();
        
        $btActive = 1;
        $select  = $adapter->select()
            ->from($this->getMainTable(), '*')
            ->where('btActive = :btActive');
        $binds   = ['btActive' => $btActive];
		return $adapter->fetchAll($select, $binds);
		
    }
    
    
    public function getBlockTypeByID($btID){
	    $adapter = $this->getConnection();
        
        $btActive = 1;
        $select  = $adapter->select()
            ->from($this->getMainTable(), '*')
            ->where('btActive = :btActive AND btID = :btID');
        $binds   = ['btActive' => $btActive, 'btID' => $btID];
		return $adapter->fetchRow($select, $binds);
    }
    
    public function refreshBlockType($data, $class, $sql){
	    //$data = ['arHandle'=>$arHandle];
	    $adapter = $this->getConnection();
	    $data = $adapter->update($this->getMainTable(), $data, ['btHandle = ?'=>$class]);
	    if(is_array($sql)){
			foreach($sql as $sq){
				$adapter->query($sq);
			}
		}else{
			if(trim($sql) !=''){
				$adapter->query($sql);
			}
		}
	    return $this->getBlockByHandle($class);
    }
    
    public function removeBlockType($blockType, $blockObject){
	    $adapter = $this->getConnection();
	    $class = $blockType['btHandle'];
	    $sql = "DELETE from ".$this->getMainTable()." where btID=".$blockType['btID'];
	    $adapter->query($sql);
	    
	     $sql = "DELETE from mage25_area_blocks where btID=".$blockType['btID'];
	    $adapter->query($sql);
	    
	    //Now deleting block table
	    
	     if(property_exists($blockObject, 'btTable' )){
	    
		    $btTable = $blockObject->btTable;
		    if($btTable !=''){
		    	$sql = "DROP TABLE  IF EXISTS ".$btTable;
		    	$adapter->query($sql);
		    }
	    
	    }
	    
	    if(property_exists($blockObject, 'associatedTables' )){
		    $associatedTables = $blockObject->associatedTables;
		    foreach($associatedTables as $_asTable){
			    $sql = "DROP TABLE  IF EXISTS ".$_asTable;
			    $adapter->query($sql);
		    }
	    }
	    
	   
    }
    
    public function getBlocksInThisArea($arID){
	    
	    	$connection = $this->getConnection();
	    	$tableName = $connection->getTableName('mage25_area_blocks');
	       
	        $select  = $connection->select()
	            ->from($tableName, '*')
	            ->order('sort_order','asc')
	            ->where('arID = :arID AND trash = 0');
	        $binds   = ['arID' => $arID];
			return $connection->fetchAll($select, $binds);
    }
    
    public function updateBlockCustomTemplate($btID, $bID, $areaID, $filename){
	    $adapter = $this->getConnection();
	  
	    
	    
	    $adapter->update('mage25_area_blocks', ['fileName' => $filename], ['arID = ?'=>[$areaID], 'btID = ?'=>[$btID], 'bID = ?'=>[$bID]]);
	    return true;
	    
    }
    
    public function getAreaBlockData($bID,$btID, $arID){
	    
	    	$connection = $this->getConnection();
	    	$tableName = $connection->getTableName('mage25_area_blocks');
	       
	        $select  = $connection->select()
	            ->from($tableName, '*')
	            ->order('sort_order','asc')
	            ->where('arID = :arID AND bID = :bID AND btID = :btID AND trash = 0');
	        $binds   = ['arID' => $arID, 'bID'=>$bID, 'btID' => $btID];
		 $data = $connection->fetchAll($select, $binds);
		 if(is_array($data)) return $data[0];
		 return array();
	    	
    }
    
    
    public function getBlocksfromArchive($arID){
	   
	    $connection = $this->getConnection();
	    	$tableName = $connection->getTableName('mage25_area_blocks');
	       
	        $select  = $connection->select()
	            ->from($tableName, '*')
	            ->order('sort_order','asc')
	            ->where('arID = :arID AND trash = 1');
	        $binds   = ['arID' => $arID];
			return $connection->fetchAll($select, $binds);
    }
  
	public function restoreBlockFromTrash($arID, $btID, $bID){
		$data = ['trash'=>'0'];
	    $adapter = $this->getConnection();
	    $data = $adapter->update('mage25_area_blocks', $data, ['arID = ?'=>[$arID], 'btID = ?'=>[$btID], 'bID = ?'=>[$bID]]);
		
		
	    return true;
	}
	
	
	public function deleteBlockFromTrash($arID, $btID, $bID){
		
		$blockTypeData = $this->getBlockTypeByID($btID);
		
		$class = $blockTypeData['btHandle'];
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$blockObject = $objectManager->get($class);
		
		$table = $blockObject->btTable;
		
		
	    $adapter = $this->getConnection();
	    //$data = $adapter->update('mage25_area_blocks', $data, ['arID = ?'=>[$arID], 'btID = ?'=>[$btID], 'bID = ?'=>[$bID]]);
	    
	  //  $class = $data['block_type_data']['btHandle'];
	    //   $table = $class::$btTable;
		
		
		
		$sql = "DELETE from mage25_area_blocks where btID=".$btID." and bID=".$bID.' and arID='.$arID;
	    $adapter->query($sql);
	    
	    $sql = "DELETE from ".$table." where bID=".$bID;
	    $adapter->query($sql);
		
		
		
		
	    return true;
	}
    
    
    
    
}