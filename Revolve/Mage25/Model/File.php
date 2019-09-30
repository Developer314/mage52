<?php
namespace Revolve\Mage25\Model;
use Magento\Framework\Model\AbstractModel;

class File extends AbstractModel
{
    /**
     * Define resource model
     */
    
    
   protected $fileCollectionFactory;
   
  
   
   public function __construct(
 
   \Revolve\Mage25\Model\ResourceModel\File\CollectionFactory $fileCollectionFactory,
   \Magento\Framework\Model\Context $context,
   \Magento\Framework\Registry $registry,
   \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
   \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
   
   array $data = []
   ){
	   $this->fileCollectionFactory = $fileCollectionFactory;
	  
	   parent::__construct($context, $registry, $resource, $resourceCollection, $data);
	   
   }
    
    protected function _construct()
    {
		$this->_init('Revolve\Mage25\Model\ResourceModel\File');
	}
	
	
	public function getFileSets(){
		
		return $this->getResource()->getFileSets();
		
	}
	
	public function addUpdateFileset($fsName, $fsID){
		return $this->getResource()->addUpdateFileset($fsName,$fsID);
	}
	
	
	public function deleteFileSet($fsID){
		return $this->getResource()->deleteFileSet($fsID);
	}
	
	public function updateFileSetFileSortorder($fsID, $fID){
		return $this->getResource()->updateFileSetFileSortorder($fsID, $fID);
	}
	
	public function getFidsFromFsID($fsID){
		return $this->getResource()->getFidsFromFsID($fsID);
	}
	
	
	
	public function saveFile($data, $fileSets = array()){
		return $this->getResource()->saveFile($data, $fileSets);
	}
	
	public function updateFile($data, $fileSets = array(), $fID){
		return $this->getResource()->updateFile($data, $fileSets, $fID);
	}
	
	
	
	public function getFileSetsOfFile($fID){
		return $this->getResource()->getFileSetsOfFile($fID);
	}
	
	public function getFileByID($fID){
		return $this->getResource()->getFileByID($fID);
	}
	
	public function removeFromFileSets($fID){
		return $this->getResource()->removeFromFileSets($fID);
	}
	
	public function deleteFile($fID){
		return $this->getResource()->deleteFile($fID);
	}
	
	public function getFilesByFileSetID($fsID){
		return $this->getResource()->getFilesByFileSetID($fsID);
	}
	
	
    
    
    
}
