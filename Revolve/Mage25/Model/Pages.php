<?php
namespace Revolve\Mage25\Model;
use Magento\Framework\Model\AbstractModel;
class Pages extends AbstractModel
{
    /**
     * Define resource model
     */
    
    
   protected $areaCollectionFactory;
   
   public $arHandle;
   public $showControls = false;
   public $maximumBlocks = -1;
   public $areaID = 0; 
   public $_mBlock;
   
   public function __construct(
  
  
   \Magento\Framework\Model\Context $context,
   \Magento\Framework\Registry $registry,
   \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
   \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
   
   array $data = []
   ){
	   
	   parent::__construct($context, $registry, $resource, $resourceCollection, $data);
	   
   }
    
    protected function _construct()
    {
		$this->_init('Revolve\Mage25\Model\ResourceModel\Pages');
	}
	
	public function findPages($keyword){
		return $this->getResource()->findPages($keyword);
	}
	
	 public function getIdFromString($href){
	   
	 $urlparts = str_replace('{{', '', $href);
	 $urlparts = str_replace('}}', '', $urlparts);
	 $urlparts = explode('-', $urlparts);
	 return $urlparts[1];
	   
   }
	
	public function getPageName($pageID){ // {{category-id}},{{product-id}},{{page-id}}
		
		if(preg_match('/{{/', $pageID) and preg_match('/}}/', $pageID) ){
					      
					     $urlparts = str_replace('{{', '', $pageID);
						 $urlparts = str_replace('}}', '', $urlparts);
						 $urlparts = explode('-', $urlparts);
						 switch($urlparts[0]){
							 case "product":
							 	return $this->getProductName($pageID);
							 break;
							 case "category":
							 	return $this->getCategoryName($pageID);
							 break;
							 case "page":
							 	return $this->getCmsPageName($pageID);
							 break;
						 }
		}
		
	}
	
	public function getProductName($productID){
		$productID=$this->getIdFromString($productID);
		 $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$urlHelper = $objectManager->get('\Revolve\Mage25\Helper\Url');
		return $urlHelper->getProductObject($productID)->getName();
		
	}
	
	public function getCategoryName($categoryID){
		$categoryID=$this->getIdFromString($categoryID);
		 $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$urlHelper = $objectManager->get('\Revolve\Mage25\Helper\Url');
		return $urlHelper->getCategoryObject($categoryID)->getName();
	}
	
	public function getCmsPageName($pageID){
		$pageID=$this->getIdFromString($pageID);
		
		 $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$urlHelper = $objectManager->get('Magento\Cms\Model\Page')->load($pageID);
		return $urlHelper->getTitle();
		
	}
	
	
	

}