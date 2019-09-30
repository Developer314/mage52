<?php
namespace Revolve\Mage25\Controller\Blocks;
use Magento\Framework\App\Action\Context;
use \Revolve\Mage25\Helper\Data as HelperData;
use \Magento\Framework\View\Result\PageFactory as PageFactory;
use \Revolve\Mage25\Model\Block as RevolveBlock;
use \Magento\Framework\Json\Helper\Data as JsonHelper;
use \Magento\Framework\Registry as Registry;
use Revolve\Mage25\Block\Bootstrap as BlockBootstrap;
//https://magento.stackexchange.com/questions/117098/magento-2-to-use-or-not-to-use-the-objectmanager-directly
class Add extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    protected $_helperData;
    protected $_mBlock;
    protected $_jsonHelper;
    protected $_registry;
    protected $_blockClass;

    public function __construct(
    Context $context,
    PageFactory $resultPageFactory,
    HelperData $helperData,
    RevolveBlock $blockFactory,
    Registry $registry,
    JsonHelper $jsonHelper,
    BlockBootstrap $blockBootstrap)
    {
	    parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
		$this->_helperData = $helperData;
        $this->_mBlock = $blockFactory;
        $this->_jsonHelper = $jsonHelper;
        $this->_registry = $registry;
        $this->_blockClass = $blockBootstrap;
    }



    public function execute()
    {
    
    	if(!$this->_blockClass->isAdminLoggedIn()) die("Access Denied");

		if($blockAction = $this->_request->getParam('action')){
		      echo $this->$blockAction();
		      die();
	    }

		$this->_registry->register('installed_block_types', $this->getInstalledBlocks());
		
        echo $this->_view->getLayout()->createBlock("Revolve\Mage25\Block\Bootstrap")->setTemplate("Revolve_Mage25::blocks_add.phtml")->toHtml();
		die();
    }

    public function block_add(){

	    $blockHandle = $this->_request->getParam('block_class');
	    $blockHandleTemp = $this->_request->getParam('block_class');
	    $blockHandle = $blockHandle.'\Controller';
	    
	    $errors = array();

	    if(!$this->validateFieldValue($blockHandle)){
		    array_push($errors, __('Class <b>'.$blockHandleTemp.'</b> is not a valid class name '));
	    }



	    if(!$this->_helperData->isClassExists($blockHandle)){

		     array_push($errors, __('Class <b>'.$blockHandleTemp.'</b> not exists'));


	    }

	    //let this funciton install the table too
	    if($this->_mBlock->isBlockTypeExists($blockHandle)){
		   
		    array_push($errors, __('Block type <b>'.$blockHandle.'</b> already exists'));
	    }

		if(empty($errors)){
		    $message = $this->_mBlock->installBlockType($blockHandle);
	    }else{
		    $message = ['success'=>0,'message'=>implode(',', $errors),'installed_blocks'=>$this->_mBlock->getInstalledBlockTypes(1)];
	    }
		
	   return $this->_jsonHelper->jsonEncode($message);
    }
    
    

    public function validateFieldValue($handle){

	    if($handle != '' or $handle !=null){
		    return true;
	    }

    }

     public function getInstalledBlocks($btActive = 1){

	    return $this->_mBlock->getInstalledBlockTypes($btActive);
	    
    }

    public function block_refresh(){
      $btID = $this->_request->getParam('block_id');
      $message = $this->_mBlock->refreshBlockType($btID);      
     
      return $this->_jsonHelper->jsonEncode($message);
    }
    
    public function remove_block(){
      $btID = $this->_request->getParam('block_id');
      $message =  $this->_mBlock->removeBlockType($btID);
      
      return $this->_jsonHelper->jsonEncode($message);
    }
    
    public function block_list_popup(){
	    $arID = $this->_request->getParam('arID');
	    $this->_registry->register('arID', $arID);
	    $this->_registry->register('installed_block_types', $this->getInstalledBlocks());
	    echo $this->_view->getLayout()->createBlock("Revolve\Mage25\Block\Bootstrap")->setTemplate("Revolve_Mage25::block_list_popup.phtml")->toHtml();
	     
	   return '';
    }
    
    public function block_custom_templates_other_vendor(){
	    $btID = $this->_request->getParam('btID');
	    $this->_registry->register('btID', $btID);
	    
	    $InstalledTemplates = $this->_mBlock->getVendorTemplates($btID);
	    $this->_registry->register('installed_templates', $InstalledTemplates);
	    echo $this->_view->getLayout()->createBlock("Revolve\Mage25\Block\Bootstrap")->setTemplate("Revolve_Mage25::block_custom_templates_other_vendor.phtml")->toHtml();
	     
	   return '';
    }
    
    public function block_custom_templates_other_vendor_add(){
	    $template_path = trim($this->_request->getParam('template_path'));
	    $btID = $this->_request->getParam('btID');
	    
	    $errors = '';
	    
	    //check if it is empty string
	    if($template_path == ''){
		    $errors= __("Template Name Cannot be Null");
	    }
	    
	    // Check this temlate is already installed
	    
	    if($this->_mBlock->isVendorTemplateExists($template_path)){
		    $errors= $template_path.__(" is already exists");
	    }
	    
	    // Check the template is exists or not
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	    $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
	    $rootPath  =  $directory->getRoot();
	    $templatePath = $rootPath."/app/code/".$template_path;
	    
	    if(!file_exists($templatePath)){
		    $errors = $templatePath." is not exists";
	    }
	    
	    if($errors =='' and $this->_mBlock->insertVendorTemplate($btID, $template_path)){
	    
	    		$message = ['success'=>1,'message'=>__(' Vendor Template Successfully Installed'),'installed_templates'=>$this->_mBlock->getVendorTemplates($btID)];
	    
	    }else{
		    
		    $message = ['success'=>0,'message'=>$errors!=''?$errors:__('Error processing your request'),'installed_templates'=>$this->_mBlock->getVendorTemplates($btID)];
		    
	    }
	    
	    return $this->_jsonHelper->jsonEncode($message);
	    
	}
	
	function block_custom_templates_other_vendor_delete(){
		 $filename = trim($this->_request->getParam('filename'));
		 $btID = $this->_request->getParam('btID');
		 if($this->_mBlock->deleteVendorTemplate($btID, $filename)){
		 	$message = ['success'=>1,'message'=>__(' Vendor Template Successfully Deleted'),'installed_templates'=>$this->_mBlock->getVendorTemplates($btID)];
	    	}else{
		    $message = ['success'=>0,'message'=>__('Error processing your request'),'installed_templates'=>$this->_mBlock->getVendorTemplates($btID)];
		}
	    
	    return $this->_jsonHelper->jsonEncode($message);
		 
		 
	}
    
    public function block_add_edit_form(){
	    $bID = $this->_request->getParam('bID');
	    $arID = $this->_request->getParam('arID');
		$btID = $this->_request->getParam('btID');
		$block_action = $this->_request->getParam('block_action');
		$blockData = $this->_mBlock->getBlockTypeByID($btID);
		$class = $blockData['btHandle'];
		
		$dataToPass = [	'btID' => $btID,
						'bID' => $bID, 
						'arID' => $arID,
						'block_action' =>$block_action,
						'block_class' => $class,
						'btName' => $blockData['btName'],
						//'mage' => $this
					  ];
		
		
		if($block_action == 'edit'){
			$areaBlockData = $this->_mBlock->getBlockDataBybIDAndBtID($bID, $btID);
			$dataToPass['area_block_data'] = $areaBlockData;
			
		}
		
		$this->_registry->register('global_block_data',$dataToPass);
		
		
		echo $this->_view->getLayout()->createBlock($class)->setTemplate("Revolve_Mage25::block_add_edit_form.phtml")->toHtml();
		
    }
    
    
    public function add_block_from_archive(){
	    $arID = $this->_request->getParam('arID');
	    $archives = $this->_mBlock->getBlocksfromArchive($arID);
	    
	   
	    $this->_registry->register('block_factory',$this->_mBlock);
	    
	    $this->_registry->register('archives',$archives);
 
	    echo $this->_view->getLayout()->createBlock("Revolve\Mage25\Block\Bootstrap")->setTemplate("Revolve_Mage25::add_block_from_archive.phtml")->toHtml();
	
	 }
	 
	 public function update_block_update_custom_template(){
		$bID = $this->_request->getParam('bID');
		$arID = $this->_request->getParam('arID');
		$btID = $this->_request->getParam('btID');
		$block_template = $this->_request->getParam('block_template');
		
		if($this->_mBlock->updateBlockCustomTemplate($btID, $bID, $arID, $block_template)){
			$message = ['success'=>1,'message'=>'Custom template successfully updated'];
		}else{
			$message = ['success'=>0,'message'=>'Error processing your request, Please try again'];
		}
		return $this->_jsonHelper->jsonEncode($message);
	 }
	 
	 public function block_choose_custom_template(){
		 
		$bID = $this->_request->getParam('bID');
		$arID = $this->_request->getParam('arID');
		$btID = $this->_request->getParam('btID');
		$block_action = $this->_request->getParam('block_action');
		$blockData = $this->_mBlock->getBlockTypeByID($btID);
		
		$areaBlockData = $this->_mBlock->getAreaBlockData($bID,$btID, $arID);
		$class = $blockData['btHandle'];
		
		$reflector = new \ReflectionClass($class);
		$classPath = $reflector->getFileName();
		$classPath = str_replace('/Controller.php', '', $classPath);
		$templatePath = $classPath.'/templates';
		
		$templates = array();
		
		$CustomTemplatePath = explode('app/code/', $classPath);
		
		if (file_exists($templatePath) and $handle = opendir($templatePath)) {
		    while (false !== ($entry = readdir($handle))) {
		        if ($entry != "." && $entry != "..") {
		            //array_push($templates, array($classPath.'/'.$entry=>$entry));
		            if(!preg_match('/.phtml/', $entry)){
			            $entry = $entry.'/view.phtml';
		            }
		            $templates[$CustomTemplatePath[1].'/'.'templates/'.$entry] = $entry;
		        }
		    }
		    closedir($handle);
		}
		
		
		//Read Other Package Block type Custom templates
		
		
		$otherTemplates =  $this->_mBlock->getVendorTemplates($btID);
		
		foreach($otherTemplates as $_template){
			$baseName = basename($_template['filename']);
			
			$templates[$_template['filename']] = $baseName;
		}
		
		
		
		//$templates = array_merge($otherTemplates, $templates);
		
		$dataToPass = [	'btID' => $btID,
						'bID' => $bID, 
						'arID' => $arID,
						'block_action' =>$block_action,
						'block_class' => $class,
						'current_template'=>$areaBlockData['fileName'],
						'templates' =>$templates
					  ];
		
		$this->_registry->register('block_type_data',$dataToPass);
		
		
		
		  echo $this->_view->getLayout()->createBlock("Revolve\Mage25\Block\Bootstrap")->setTemplate("Revolve_Mage25::block_custom_templates.phtml")->toHtml();
		 
	 }
    
    public function restore_from_archieve(){
	   
	    $arID = $this->_request->getParam('arID');
		$btID = $this->_request->getParam('btID');
		$bID = $this->_request->getParam('bID');
		$this->_mBlock->restoreBlockFromTrash($arID, $btID, $bID);
		$message = ['success'=>1,'message'=>"Block successfully restored"];
		return $this->_jsonHelper->jsonEncode($message);
    }
    
    
    public function delete_from_archieve(){
	   
	    $arID = $this->_request->getParam('arID');
		$btID = $this->_request->getParam('btID');
		$bID = $this->_request->getParam('bID');
		$this->_mBlock->deleteBlockFromTrash($arID, $btID, $bID);
		$message = ['success'=>1,'message'=>"Block successfully deleted"];
		return $this->_jsonHelper->jsonEncode($message);
    }
    
    
    
    
    /*
	    called form view/frontend/templates/block_add_edit_form.phtml
		
	*/
    
    public function update_block_to_area(){
	    
		$btID = $this->_request->getParam('btID');
		$blockTypeData = $this->_mBlock->getBlockTypeByID($btID);
		$class = $blockTypeData['btHandle'];
		$data = [];
		$data['block_type_data'] = $blockTypeData;
		$data['data']  = $this->_request->getParams();
		
		try{
			
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$field_helper = $objectManager->get($class);
			$bID = $field_helper->save($data);
			
			
			$blockData = $this->_mBlock->getBlockDataBybIDAndBtID($bID, $btID);
			
			
			
			$b = array(
			'bID'=>$bID,
			'btID'=>$btID,
			'arID'=>$data['data']['arID'],
			'fileName'=>'',
			);
			
		
			
			
			$blockData['area_block'] = $b;
		$class = $blockData['blockType']['btHandle'];
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$field_helper = $objectManager->get($class);
					
				
					
			ob_start();
			$field_helper->view($blockData);
			$btContent = ob_get_contents();
			ob_end_clean();
		
			
			
			$message = ['success'=>1,'bID'=>$bID,'btContent'=>$btContent,'message'=>"Block successfully Updated/Added"];
			
		}catch(Exception $e){
			$message = ['success'=>0,'message'=>$e->getMessage()];
		}
		
		return $this->_jsonHelper->jsonEncode($message);
	   
    }
    
    
    public function archive_block_from_area(){
	    $bID = $this->_request->getParam('bID');
	    $arID = $this->_request->getParam('arID');
		$btID = $this->_request->getParam('btID');
		$blockData = $this->_mBlock->getBlockTypeByID($btID);
		$class = $blockData['btHandle'];
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$adapter = $resource->getConnection();
		$data = ['trash'=>1,'date_deleted'=>time()];
		$data = $adapter->update('mage25_area_blocks', $data, ['bID = ?'=>$bID, 'btID = ?'=>$btID, 'arID = ?'=>$arID]);
		$message = ['success'=>0,'message'=>'Block Successfully Archived'];
		return $this->_jsonHelper->jsonEncode($message);
    }
    
    public function arrange_block(){
	    
	    //$dts = $this->_request->getParams();
	    
	    
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$adapter = $resource->getConnection();
	    
	    
	    
	    
		$areas = $this->_request->getParam('area');
		
		foreach($areas as $ar=>$blocks){
			$sort_order = 0;
			foreach($blocks as $bl){
				
				$bl = explode("||", $bl);
				$blID = $bl[0];
				$btID = $bl[1];
				
				$data = ['arID'=>$ar, 'sort_order'=>$sort_order];
				
				$data = $adapter->update('mage25_area_blocks', $data, ['bID = ?'=>$blID, 'btID = ?'=>$btID]);
				$sort_order++;	
			}
		}
		
		$message = ['success'=>0,'message'=>'Block Successfully Archived'];
		return $this->_jsonHelper->jsonEncode($message);
	    
    }
    
    public function block_mass_install(){
	
	   $blocksToinstall = $this->getBlocksToInstall();
        foreach($blocksToinstall as $_block){
	        $this->_mBlock->installBlockType($_block);
        }
        
        $message = ['success'=>0,'message'=>'Blocks Successfully installed','installed_blocks'=>$this->_mBlock->getInstalledBlockTypes(1)];
	   
	   return $this->_jsonHelper->jsonEncode($message);
	
	}
    
    
    public function getBlocksToInstall(){
	    return array(
		    'Revolve\Mage25\Block\Autonav\Controller',
		    'Revolve\Mage25\Block\Content\Controller',
		    'Revolve\Mage25\Block\File\Controller',
		    'Revolve\Mage25\Block\Googlemap\Controller',
		    'Revolve\Mage25\Block\Html\Controller',
		    'Revolve\Mage25\Block\Image\Controller',
		    'Revolve\Mage25\Block\Productlist\Controller',
		    'Revolve\Mage25\Block\Slideshow\Controller',
		    'Revolve\Mage25\Block\Staticblock\Controller',
		    'Revolve\Mage25\Block\Youtube\Controller'
		    
	    );
    }
    



}
