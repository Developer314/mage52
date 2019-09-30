<?php
namespace Revolve\Mage25\Model;
use Magento\Framework\Model\AbstractModel;

class Block extends AbstractModel
{
    /**
     * Define resource model
     */
    
    
   protected $areaCollectionFactory;
   
   public $arHandle;
   public $showControls = false;
   public $maximumBlocks = -1;
   public $areaID = 0; 
   
   public function __construct(
   \Revolve\Mage25\Model\ResourceModel\Area\CollectionFactory $areaCollectionFactory,
   \Magento\Framework\Model\Context $context,
   \Magento\Framework\Registry $registry,
   \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
   \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
   array $data = []
   ){
	   $this->areaCollectionFactory = $areaCollectionFactory;
	   
	   parent::__construct($context, $registry, $resource, $resourceCollection, $data);
	   
   }
    
    protected function _construct()
    {
		$this->_init('Revolve\Mage25\Model\ResourceModel\Block');
	}
	
	public function installBlockType($class){
		 
		$message = [];
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$field_helper = $objectManager->get($class);
		
		try{
		
				$data = [	'btHandle'=>$class,
							'btName'=>$field_helper->getBlockTypeName(),
							'btDescription'=>$field_helper->getBlockTypeDescription(),
							'btActive'=>1,
							'btInterfaceWidth'=>$field_helper->btInterfaceWidth,
							'btInterfaceHeight'=>$field_helper->btInterfaceHeight
						];
				$sqlData = $field_helper->installDB();
				$this->getResource()->install($data, $sqlData);
				$message = ['success'=>1,'message'=>$field_helper->getBlockTypeName().__(' Successfully installed'),'installed_blocks'=>$this->getInstalledBlockTypes()];
		}catch(Exception $e){
			$message = ['success'=>0,'message'=>$e->getMessage()];
		}
		
		return $message;
		
	}
	
	/*public function installBlockTable($class){
		
		$sqlData = $class::installDB();
		$this->getResource()->installBlockTable($sqlData);
	}*/
	
	public function isBlockTypeExists($class){
		
		return $this->getResource()->getBlockByHandle($class);
	}
	
	public function getInstalledBlockTypes(){
		return $this->getResource()->getInstalledBlockTypes();	
	}
	
	public function restoreBlockFromTrash($arID, $btID, $bID){
		return $this->getResource()->restoreBlockFromTrash($arID, $btID, $bID);	
	}
	
	public function deleteBlockFromTrash($arID, $btID, $bID){
		return $this->getResource()->deleteBlockFromTrash($arID, $btID, $bID);	
	}
	
	public function refreshBlockType($btID){
		$message = [];
		$blockType = $this->getBlockTypeByID($btID);
		$class = $blockType['btHandle'];
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$field_helper = $objectManager->get($class);
		
		
		try{
		
				$data = [	
							'btName'=>$field_helper->getBlockTypeName(),
							'btDescription'=>$field_helper->getBlockTypeDescription(),
							'btActive'=>1,
							'btInterfaceWidth'=>$field_helper->btInterfaceWidth,
							'btInterfaceHeight'=>$field_helper->btInterfaceHeight
						];
				
				$sqlData = $field_helper->updateDB();
				$this->getResource()->refreshBlockType($data, $class, $sqlData);	
				$message = ['success'=>1,'message'=>$field_helper->getBlockTypeName().__(' Successfully Refreshed'),'installed_blocks'=>$this->getInstalledBlockTypes()];
				
		}catch(Exception $e){
			$message = ['success'=>0,'message'=>$e->getMessage()];
		}
		
		return $message;
	}
	
	public function removeBlockType($btID){
		$blockType = $this->getResource()->getBlockTypeByID($btID);
		$class = $blockType['btHandle'];
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$field_helper = $objectManager->get($class);
		
		
		$this->getResource()->removeBlockType($blockType, $field_helper);	
		$message = ['success'=>1,'message'=>$field_helper->getBlockTypeName().__(' Successfully Deleted'),'installed_blocks'=>$this->getInstalledBlockTypes()];
		return $message;
	}
	
	public function getBlockTypeByID($btID){
		return $this->getResource()->getBlockTypeByID($btID);
	}
	
	public function updateBlockCustomTemplate($btID, $bID, $areaID, $filename){
		return $this->getResource()->updateBlockCustomTemplate($btID, $bID, $areaID, $filename);
	}
	
	public function getBlocksInThisArea($arID){
		return $this->getResource()->getBlocksInThisArea($arID);
	}
	
	public function getAreaBlockData($bID,$btID, $arID){
		return $this->getResource()->getAreaBlockData($bID,$btID, $arID);
	}
	
	public function getBlocksfromArchive($arID){
		
		return $this->getResource()->getBlocksfromArchive($arID);
	}
	
	
	
	public function getBlockDataBybIDAndBtID($bID,$btID){
		$blockTypeData = $this->getBlockTypeByID($btID);
		/*
			Array
				(
				    [btID] => 59
				    [btHandle] => \Revolve\Mage25\Block\Html\Controller
				    [btName] => HTML 1
				    [btDescription] => HTML Content.
				    [btActive] => 1
				    [btInterfaceWidth] => 600
				    [btInterfaceHeight] => 465
				    [pkgID] => 0
				    [btTable] => 
				)
			*/
		
		$collectionData = [];
		
		$class = $blockTypeData['btHandle'];
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$field_helper = $objectManager->get($class);
		
		
		$table = $field_helper->btTable;
		$collectionData['blockType'] = $blockTypeData;
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName($table);
		
		
        $select  = $connection->select()
            ->from($tableName, '*')
            ->where('bID = :bID');
        $binds   = ['bID' => $bID];
        $collectionData['blockData']= $connection->fetchRow($select, $binds);
		return $collectionData;
		
	}
	
	function getVendorTemplates($btID){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('mage25_vendor_templates');
		
		$select  = $connection->select()
            ->from($tableName, '*')
            ->where('btID = :btID');
        $binds   = ['btID' => $btID];
        return $connection->fetchAll($select, $binds);
		
		
	}
	
	function isVendorTemplateExists($filename){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('mage25_vendor_templates');
		
		$select  = $connection->select()
            ->from($tableName, '*')
            ->where('filename = :filename');
        $binds   = ['filename' => $filename];
        $data = $connection->fetchAll($select, $binds);
        if(sizeof($data) > 0) return true;
        return false;
	}
	
	function insertVendorTemplate($btID, $filename){
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('mage25_vendor_templates');
		$data = ['btID'=>$btID,'filename'=>$filename];
		$data = $connection->insert($tableName, $data);
		
		return true;
		
		
	}
	
	function deleteVendorTemplate($btID, $filename){
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('mage25_vendor_templates');
		$sql = "DELETE from ".$tableName." where btID=".$btID." and filename='".$filename."'";
	    $connection->query($sql);
		return true;
		
	}
    
    
    
}
