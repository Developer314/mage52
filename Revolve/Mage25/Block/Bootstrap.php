<?php
namespace Revolve\Mage25\Block;


class Bootstrap extends \Magento\Framework\View\Element\Template
{
    
    protected $_urlInterface;
    protected $_directory_list;
    protected $_authSession;
    protected $_areaHandle;
    protected $_catalogSession;
    protected $_request;
    public $registry;
    public $_mArea;
	protected $_mBlock;
	protected $_helperBackend;
	protected $_storeManager;
	 protected $_customerSession;
	
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
       
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
       \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\App\Request\Http $request,
        \Revolve\Mage25\Model\Area $areaFactory,
        \Magento\Framework\Registry $registry,
        \Revolve\Mage25\Model\Block $blockFactory,
        \Magento\Backend\Helper\Data $helperBackend,
        array $data = []
    )
    {
	   
	   
	   
	    parent::__construct($context, $data);
        $this->_storeManager = $context->getStoreManager();
        $this->_urlInterface = $context->getUrlBuilder();
        $this->directory_list = $directory_list;
        $this->_authSession = $context->getBackendSession();
        $this->_catalogSession = $catalogSession;
        $this->_mArea = $areaFactory;
        $this->registry = $registry;
        $this->_mBlock = $blockFactory;
        $this->_helperBackend = $helperBackend;
        $this->_customerSession = $customerSession;
        $this->_request = $request;
        
    }

       
       public function getMediaUrl(){
	       return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
       }


       public function getUrlInterfaceData()
       {
           echo $this->_urlInterface->getCurrentUrl() . '<br />';

           echo $this->_urlInterface->getUrl() . '<br />';

           echo $this->_urlInterface->getUrl('test/data/22') . '<br />';

           echo $this->_urlInterface->getBaseUrl() . '<br />';
       }

       /**
       ** http://blog.chapagain.com.np/magento-2-get-current-url-base-url/
       **/
       public function getSiteUrl($current = false, $customPage = ''){

         if($current){
           return $this->_urlInterface->getCurrentUrl();
         }else{
           return $this->_urlInterface->getUrl($customPage);
         }

       }


       /**
        Ref URL: https://www.extensionsmall.com/blog/get-absolute-path-file-magento-2/
        http://www.mage-world.com/blog/create-a-module-with-custom-database-table-in-magento-2.html
       **/


       public function getDirectoryPath($folder=''){

         if($folder == ''){
          return $this->directory_list->getRoot();
        }else{
          $this->directory_list->getPath($folder);
        }

       }

       
       
       public function isAdminLoggedIn(){
	      
	      /*
	      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerSession = $objectManager->create('Magento\Customer\Model\Session');
		$customerSession->_logout();
		*/
		 return $this->_customerSession->getCustomer() && $this->_customerSession->getCustomer()->getData('mage25_editor');
	      
	 }
       
       public function outputViewImage($imagePath){
	       
       }
       
       public function isEditMode(){
	       
	      //$params = $this->request->getParams();
	       
	      if($this->_request->getParam('mage25_action') == 'editpage'){
		      $this->_catalogSession->setEditMode(true);
	      }
	      
	      if($this->_request->getParam('mage25_action') == 'exitedit'){
		      $this->_catalogSession->setEditMode(false);
	      }
	      
	      if(!$this->isAdminLoggedIn()){
		      $this->_catalogSession->setEditMode(false);
	      }

		  return $this->_catalogSession->getEditMode();
	       
       }
       
       public function getCurrentCmsPageID(){
	       $objectManagerCms = \Magento\Framework\App\ObjectManager::getInstance();
		  $cmsPage = $objectManagerCms->get('\Magento\Cms\Model\Page');
		  return $cmsPage->getId();
       }
       
       public function getCurrentCategoryID(){
	       $category = $this->registry->registry('current_category');
		  return $category->getId();
       }
       
       public function getCurrentProductID(){
	       $product = $this->registry->registry('current_product');
		  return $product->getId();
       }
       public function display($arHandle, $global = false){
	       
	       //catalog_category_view
	       //cms_page_view
	       //catalog_product_view
	       
	       
	       if(!$global){
		       $actionName = $this->_request->getFullActionName();
		       switch($actionName){
			       case "cms_page_view":
			       	$arHandle = $arHandle.' [Page ID : '.$this->getCurrentCmsPageID().']';
			       break;
			       case "catalog_category_view":
			       	$arHandle = $arHandle.' [Category ID : '.$this->getCurrentCategoryID().']';
			       break;
			       
			       case "catalog_product_view":
			       	$arHandle = $arHandle.' [Product ID : '.$this->getCurrentProductID().']';
			       break;
			       
			       default:
			       $arHandle = $arHandle.' ['.strtoupper($actionName).']';
			       break;
			       
		       }
	       }
	       
	       $this->_mArea->display($arHandle, $this->isEditMode());
       }
       
       
       
       public  function save($data){
	       
	        
	       $class = $data['block_type_data']['btHandle'];
	       
	       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		  $field_helper = $objectManager->get($class);
	       
	       
	       $table = $field_helper->btTable;
	       $areaData = $data['data'];
	       $blockAction = $areaData['action'];
	       
	       $btID = $areaData['btID'];
	       $arID = $areaData['arID'];
	       unset($areaData['action']);
	       unset($areaData['arID']);
	       unset($areaData['btID']);
	       unset($areaData['bID']);
	       unset($areaData['block_action']);
	       
	       if($data['data']['block_action'] == 'add'){
		       
		       $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			   $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			   $connection = $resource->getConnection();
			   $tableName = $resource->getTableName($table);
			   $data = $connection->insert($tableName, $areaData);
			   $bID = $connection->lastInsertId();
			   
			   //Now Insert data to mage25_area_blocks table
			   $tableName = $resource->getTableName('mage25_area_blocks');
			   $sortOrder = self::getAreaBlockLastSortOrder($connection, $arID);
			   $sortOrder = $sortOrder+1;
			   $data = ['bID'=>$bID,'btID'=>$btID,'arID'=>$arID,'fileName'=>'','trash'=>0,'sort_order'=>$sortOrder];
			   $data = $connection->insert($tableName, $data);
			   
	       }else{
		       
		       $areaData = $data['data'];
		       $bID = $areaData['bID'];
			   unset($areaData['action']);
		       unset($areaData['arID']);
		       unset($areaData['btID']);
		       unset($areaData['bID']);
		       unset($areaData['block_action']);
		       $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			   $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			   $adapter = $resource->getConnection();
		       $data = $adapter->update($table, $areaData, ['bID = ?'=>$bID]);
	    	}
	      
			return $bID;
	      
	      	       
	      // $this->_mBlock->saveAreaData();
       }
       
       public  function getAreaBlockLastSortOrder($connection, $arID){
	       	
		   	$tableName = $connection->getTableName('mage25_area_blocks');
	       
	        $select  = $connection->select()
	            ->from($tableName, '*')
	            ->where('arID = :arID');
	        $binds   = ['arID' => $arID];
			return sizeof($connection->fetchAll($select, $binds));
       }
       
       
       
       public  function edit($blockData){
	        
	        
	        $btHandle = $blockData['block_class'];
			$reflector = new \ReflectionClass($btHandle);
		    $classPath = $reflector->getFileName();
			$classPath = str_replace('/Controller.php', '', $classPath);
			$fileName = '/form.phtml';
			
			$dataToPass = array();
			
			 $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$field_helper = $objectManager->get($btHandle);
			$dataToPass['blockObject'] = $field_helper;
			
			
			if(isset($blockData['area_block_data']) and isset($blockData['area_block_data']['blockData'])){
				$dataToPass['blockData'] = $blockData['area_block_data']['blockData'];
				
			}
			
			extract($dataToPass);
			
			include($classPath.$fileName);
       }
       
       public  function add($blockData){
	       
	       self::edit($blockData);
       }
       
       public function view($blockData){
	        
	        
	       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		 
	        
	        $btHandle = $blockData['blockType']['btHandle'];
	        $fileName = $blockData['area_block']['fileName'];
	        $reflector = new \ReflectionClass($btHandle);
		   $classPath = $reflector->getFileName();
		   $classPath = str_replace('/Controller.php', '', $classPath);
		   
		 /* if($btHandle == 'Revolve\Mage25\Block\Autonav\Controller'){
			  echo $classPath;
		  }*/
			
			
			
			$templateUsed = false;
			if($fileName !=''){
				
				$CustomTemplatePath = explode('app/code/', $classPath);
				
				$fileName = $CustomTemplatePath[0].'app/code/'.$fileName;
				
				if(file_exists($fileName)){
					$templateUsed = true;
					//echo $fileName;
				}
				
			}
			
			if(!$templateUsed){
				$fileName = $classPath.'/view.phtml';
			}
			
			
			
			
			$field_helper = $objectManager->get($btHandle);
			
			$blockData1 = $blockData;
			
			
			if(isset($blockData1['blockData']) and is_array($blockData1['blockData'])){
				extract($blockData1['blockData']);
			}
			
			extract($blockData);
			
			$otherData = [];
			
			$otherData['blockObject'] = $field_helper;
			unset($blockData['blockTypeData']);
			$otherData['_block'] = $blockData1;
			extract($otherData);
			if(file_exists($fileName)){
				include($fileName);
			}elseif($this->isEditMode()){
				echo "<div class='mage25_error_div'>Block View Error".$fileName." Not Exists</div>";
			}
		  	
		  
		   
       }
       
       public function getAdminUrl(){
	       
	       
	      return $this->_helperBackend->getHomePageUrl();
       }
       
   
       
       
       
      
     
}
