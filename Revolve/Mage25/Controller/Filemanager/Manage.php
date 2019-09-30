<?php
namespace Revolve\Mage25\Controller\Filemanager;
use Magento\Framework\App\Action\Context;
use \Revolve\Mage25\Helper\Data as HelperData;
use \Magento\Framework\View\Result\PageFactory as PageFactory;
use \Revolve\Mage25\Model\File as RevolveFile;
use \Magento\Framework\Json\Helper\Data as JsonHelper;
use \Magento\Framework\Registry as Registry;
use \Magento\Framework\App\Filesystem\DirectoryList as DirectoryList;
use Revolve\Mage25\Block\Bootstrap as BlockBootstrap;

class Manage extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    protected $_helperData;
    protected $_mFile;
    protected $_jsonHelper;
    protected $_registry;
    protected $_directory_list;
    protected $_assetRepo;
    protected $_storeManager;
     protected $_blockClass;
    public function __construct(
    Context $context,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    PageFactory $resultPageFactory,
    HelperData $helperData,
    RevolveFile $revolveFile,
    Registry $registry,
    \Magento\Framework\View\Asset\Repository $assetRepo,
    JsonHelper $jsonHelper,
    DirectoryList $directory_list,
    BlockBootstrap $blockBootstrap)
    {
	    parent::__construct($context);
	    $this->_storeManager = $storeManager;
        $this->_resultPageFactory = $resultPageFactory;
		$this->_helperData = $helperData;
        $this->_mFile = $revolveFile;
        $this->_jsonHelper = $jsonHelper;
        $this->_registry = $registry;
        $this->_directory_list = $directory_list;
        $this->_assetRepo = $assetRepo;
        $this->_blockClass = $blockBootstrap;
    }

	public function execute()
    {

   if(!$this->_blockClass->isAdminLoggedIn()) die("Access Denied");
		if($blockAction = $this->_request->getParam('action')){
		      echo $this->$blockAction();
		      die();
	    }

		/*$this->_registry->register('installed_block_types', $this->getInstalledBlocks());
        echo $this->_view->getLayout()->createBlock("Revolve\Mage25\Block\Bootstrap")->setTemplate("Revolve_Mage25::blocks_add.phtml")->toHtml();
        */

    }

    protected function _prepareLayout()
   {

       parent::_prepareLayout();

    }
    
    

    public function file_manager(){

		 $elementid = $this->_request->getParam('elementid');
		 $fieldtype = $this->_request->getParam('fieldtype');
		 $this->_registry->register('elementid', $elementid);
	     $this->_registry->register('fieldtype', $fieldtype);
	     $this->_registry->register('file_sets', $this->_mFile->getFileSets());

	     echo $this->_view->getLayout()->createBlock("Revolve\Mage25\Block\Bootstrap")->setTemplate("Revolve_Mage25::file_manager.phtml")->toHtml();



    }

    public function add_update_file_set(){
	    $fsName = $this->_request->getParam('fsName');
	    if(trim($fsName) ==''){
		    $message = ['success'=>0,'message'=>'Fileset name cannot be empty, please try again'];
		    return $this->_jsonHelper->jsonEncode($message);
	    }
	    $fsID = $this->_request->getParam('fsID');
	    if($this->_mFile->addUpdateFileset($fsName, $fsID)){
		    $fileSets = $this->_mFile->getFileSets();
		    $message = ['success'=>1,'message'=>'Fileset successfully updated', 'file_sets'=>$fileSets];
	    }else{
		    $message = ['success'=>0,'message'=>'Something went wrong, please try again'];
	    }
	    return $this->_jsonHelper->jsonEncode($message);
    }

    public function delete_file_set(){
	    $fsID = $this->_request->getParam('fsID');
	    $this->_mFile->deleteFileSet($fsID);
	    $fileSets = $this->_mFile->getFileSets();
		$message = ['success'=>1,'message'=>'File set successfully deleted', 'file_sets'=>$fileSets];
		return $this->_jsonHelper->jsonEncode($message);
    }
    
    public function delete_files(){
	    $fIDs = $this->_request->getParam('fIDs');
	    $fIDs = explode(',', $fIDs);
	    
	    $i=0;
	    foreach($fIDs as $k=>$fID){
		    
		    
		   
		    
	    	$fileObject = $this->_mFile->getFileByID($fID);
	    	
	    	$filePath = $fileObject['fPath'];
	    	
	    	$filePath = $this->_directory_list->getPath('media').'/'.$filePath;
	    	
	    	
	    	$this->_mFile->removeFromFileSets($fID);
	    	$this->_mFile->deleteFile($fID);
			//now Delete the actual file from Disc
			if(!is_dir($filePath)){
		    	@unlink($filePath);
	    	}
			$i++;
	    
	    }
	    
	    $message = ['success'=>1,'message'=>$i.' File(s) deleted Successfully'];
			return $this->_jsonHelper->jsonEncode($message);

	    
    }
    
   public function arrange_sortorder_fileset_files(){
	   $fID = $this->_request->getParam('fID');
	   $fsID = $this->_request->getParam('fsID');
	    $fID = $this->_mFile->updateFileSetFileSortorder($fsID, $fID);
	   $message = ['success'=>1,'message'=>' Sort order updated Successfully'];
	   return $this->_jsonHelper->jsonEncode($message);
   }
    
    public function list_files_from_fileset(){
	    $fsID = $this->_request->getParam('fsID');
	    $this->_registry->register('object', $this);
		
		$fIDs = $this->_mFile->getFidsFromFsID($fsID);
	   
	    $files  = array();
	    foreach($fIDs as $fID){
		    $file = $this->_mFile->getFileByID($fID['fID']);
		    $file['sort_order'] = $fID['sort_order'];
		    array_push($files, $file);
	    }
	    
	   
		
		
		
		$this->_registry->register('files', $files);
		$this->_registry->register('fsID', $fsID);
		
		 echo $this->_view->getLayout()
        ->createBlock("Revolve\Mage25\Block\Bootstrap")
        ->setTemplate("Revolve_Mage25::list_files_from_fileset.phtml")->toHtml();
        die();
    }

	
	public function replace_file(){
		$fID = $this->_request->getParam('fID');
		$filesets = $this->_request->getParam('filesets');
		$fName = $this->_request->getParam('fName');
		$fDescription = $this->_request->getParam('fDescription');
		$fPath = $this->_request->getParam('fPath');
		
		$data = array();
		if(isset($_FILES) and isset($_FILES['mage25_file_upload']['tmp_name']) and $_FILES['mage25_file_upload']['tmp_name'] !=''){
			$filePrefixPath = $this->getDirectoryToUpload();
			$upload_file_dir = $this->_directory_list->getPath('media').'/mage25/'.$filePrefixPath.'/';
			$path = $upload_file_dir;
			$fname = $_FILES['mage25_file_upload']['name']; //file name
			$destPath = $path.$fname;
			move_uploaded_file($_FILES['mage25_file_upload']['tmp_name'], $destPath);
			$data['fPath'] = 'mage25/'.$filePrefixPath.'/'.$fname;
			
			
			$filePath = $fPath;
	    	
	    	$filePath = $this->_directory_list->getPath('media').'/'.$filePath;
	    	if(!is_dir($filePath)){
		    	@unlink($filePath);
	    	}
			
			
		}
		
		
		 $data['fName'] = $fName?$fName:$fname;
		 $data['fDescription'] = $fDescription;
		
		 $data['dateAdded'] = time();
		 $data['uID'] = 0;
		 $fID = $this->_mFile->updateFile($data, $filesets, $fID);
		 $message = ['success'=>1,'message'=>'File updated Successfully'];
		return $this->_jsonHelper->jsonEncode($message);
		
	}


    public function upload_files(){

		
		$filePrefixPath = $this->getDirectoryToUpload();
		$upload_file_dir = $this->_directory_list->getPath('media').'/mage25/'.$filePrefixPath.'/';
		
		
		if(isset($_FILES['mage25_file_upload']) and $_FILES['mage25_file_upload']['name'][0] !=''){
			$incr = 0;
			$path = $upload_file_dir;

			$filesets = $this->_request->getParam('filesets');

			
			
			foreach($_FILES['mage25_file_upload']['name'] as $file){

				 $fname = $_FILES['mage25_file_upload']['name'][$incr]; //file name
				 $temp_name = $_FILES['mage25_file_upload']['tmp_name'][$incr];
				 $size = $_FILES['mage25_file_upload']['size'][$incr];
				 $destPath = $path.$fname;
				 try{
				 	move_uploaded_file($temp_name, $destPath);
				 }catch(Exception $e){
					 $message = ['success'=>0,'message'=>$e->getMessage()];
					 return $this->_jsonHelper->jsonEncode($message);
				 }
				 $data = array();
				 $data['fName'] = $_POST['fName']?$_POST['fName']:$fname;
				 $data['fDescription'] = $_POST['fDescription'];
				 $data['fPath'] = 'mage25/'.$filePrefixPath.'/'.$fname;
				 $data['dateAdded'] = time();
				 $data['uID'] = 0;
				 $fID = $this->_mFile->saveFile($data, $filesets);
				 $incr++;
			}

			$message = ['success'=>1,'message'=>'File uploaded Successfully'];
			return $this->_jsonHelper->jsonEncode($message);

		}else{
			$message = ['success'=>0,'message'=>'No files uploaded'];
			return $this->_jsonHelper->jsonEncode($message);
		}


	}

	public function import_remote_files(){

		if (!function_exists('iconv_get_encoding')) {
			$message = array('error'=>0,'message'=>__("Remote URL import requires the iconv extension enabled on your server."));
		   return $this->_jsonHelper->jsonEncode($message);


		}


		$files = $this->_request->getParam('url_upload');
		$filesets = $this->_request->getParam('filesets');

		$errors = array();
		$incoming_urls = array();
		if(sizeof($files) > 0 ){


			foreach($files as $k){
				if(trim($k) !=''){
					if(filter_var($k, FILTER_VALIDATE_URL) === FALSE){
						    $errors[] = $k.' is not a valid url';
						}
				}
			}

			if(sizeof($errors) > 0){
				$message = array('error'=>1,'message'=>implode('<br />', $errors));
			    echo json_encode($message);
			    die();
			}else{

				$filePrefixPath = $this->getDirectoryToUpload();
				$upload_file_dir = $this->_directory_list->getPath('media').'/mage25/'.$filePrefixPath.'/';

				foreach($files as $this_url) {
					if ($this_url !='') {

						$fname = '';
						//$fpath = $temp_upload_dir;

						$fname = '';
						if (preg_match('/^.+?[\\/]([-\w%]+\.[-\w%]+)$/', $this_url, $matches)) {

							$fname = $matches[1];
						}
						if (strlen($fname)) {
							$handle = fopen($upload_file_dir.'/'.$fname, "w");
							fwrite($handle, file_get_contents($this_url));
							fclose($handle);
						}
						 $data = array();
						 $data['fName'] = $fname;
						 $data['fDescription'] = $fname;
						 $data['fPath'] = 'mage25/'.$filePrefixPath.'/'.$fname;
						 $data['dateAdded'] = time();
						 $data['uID'] = 0;

						 $fID = $this->_mFile->saveFile($data, $filesets);
						 //$incr++;
					}
				}

				$message = array('error'=>0,'message'=>"Successfully Uploaded");



			}



		}else{
			$message = array('error'=>0,'message'=>"Please provide a file url");

		}

		return $this->_jsonHelper->jsonEncode($message);
	}
	
	


  public function import_directory_files(){
    $incoming_dir = $this->_request->getParam('incoming_dir');
    $incoming_dir = preg_replace('/^\//', '', $incoming_dir);
    $filesets = $this->_request->getParam('filesets');
    

    $mediaDirectory = $this->_directory_list->getPath('media');




    if(isset($incoming_dir) and trim($incoming_dir) !='' and $incoming_dir !='mage25' and is_dir($mediaDirectory.'/'.$incoming_dir)){
		$incoming_dir = $mediaDirectory.'/'.$incoming_dir;
        $di = new \RecursiveDirectoryIterator($incoming_dir, \FilesystemIterator::SKIP_DOTS);
        $incr = 0;
        
        
        $filePrefixPath = $this->getDirectoryToUpload();
		$upload_file_dir = $this->_directory_list->getPath('media').'/mage25/'.$filePrefixPath.'/';
        
        
        
        foreach (new \RecursiveIteratorIterator($di) as $filename => $file) {
			
		
		rename($filename, $upload_file_dir.basename($filename));
		
		
         $data = array();
         $data['fName'] = basename($filename);
         $data['fDescription'] = basename($filename);
         $data['fPath'] = 'mage25/'.$filePrefixPath.'/'.basename($filename);

         $data['dateAdded'] = time();
         $data['uID'] = 0;
         $fID = $this->_mFile->saveFile($data, $filesets);
         
         $incr++;
        }
        
       
        $message = array('success'=>1,'message'=>$incr." file(s) imported");


    }else{
      $message = array('success'=>0,'message'=>"Please specify a valid folder name");

    }

    return $this->_jsonHelper->jsonEncode($message);


  }

  
  public function getDirectoryToUpload(){
		
		
		$time =  rand(10, 99) . time();
		$arr2 = str_split($time, 4);
		
		$upload_file_dir = $this->_directory_list->getPath('media').'/mage25/';
		if(!file_exists($upload_file_dir)){
			mkdir($upload_file_dir, 0755);
		}
		$path = '';
		foreach($arr2 as $k){
			if($path !=''){
			$path = $path.'/'.$k;
			}else{
				$path = $path.$k;
			}
			
			if(!file_exists($upload_file_dir.$path)){
				mkdir($upload_file_dir.$path, 0755);
			}
		}
		
		return implode('/', $arr2);
		
		
		
		
	}

public function edit_file_property(){
	$fID = $this->getRequest()->getParam('fID');
	$fileObject = $this->_mFile->getFileByID($fID);
	$this->_registry->register('file_object', $fileObject);
	$this->_registry->register('object', $this);
	
	$filesets = $this->_mFile->getFileSetsOfFile($fID);
	$this->_registry->register('file_sets', $filesets);
	
	$filesets_all = $this->_mFile->getFileSets();
	$this->_registry->register('file_sets_all', $filesets_all);
	
	 echo $this->_view->getLayout()
        ->createBlock("Revolve\Mage25\Block\Bootstrap")
        ->setTemplate("Revolve_Mage25::edit_file_property.phtml")->toHtml();
        die();
}

  public function list_files(){

  		$this->_registry->register('file_sets', $this->_mFile->getFileSets());
    
    	$fsID = $this->getRequest()->getParam('fsID');
    	$keyword = $this->getRequest()->getParam('keyword');
    	
    	$elementid = $this->_request->getParam('elementid');
		 $fieldtype = $this->_request->getParam('fieldtype');
		 $this->_registry->register('elementid', $elementid);
	     $this->_registry->register('fieldtype', $fieldtype);
    	
    	$this->_registry->register('fsID', $fsID);
    	$this->_registry->register('keyword', $keyword);
    	
    	
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        //get values of current limit
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest
        ()->getParam('limit') : 15;


        $collection = $this->_mFile->getCollection();
        
        
        if($keyword !=''){
	        
	        
	        $collection->addFieldToFilter(
						    array('fName', 'fDescription', 'fPath'),
						    array(
						        array('like'=>'%'.$keyword.'%'), 
						        array('like'=>'%'.$keyword.'%'),
						        array('like'=>'%'.$keyword.'%')
						    )
						);
	        
        }
        
       
         if($fsID >0){
	         
	         
	         
	         
	         $collection->getSelect()->where("fID IN (SELECT fID from mage25_file_set_files where fsID = ".$fsID.")");
	         
	        
	         
	         
         }
        
        
        $collection->setOrder('dateAdded','DESC');
        //$collection->setPageSize($pageSize);
        $collection->setCurPage($page);
                
        
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
       
       
		$pager = $this->_view->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'mage25.file.pager'
        )->setAvailableLimit(array(5=>5,10=>10,15=>15))->setShowPerPage(true)->setCollection($collection)->toHtml();
        //$this->_view->setChild('pager', $pager);
        $this->_registry->register('file_pager', $pager);
       
       
       
       

        $this->_registry->register('file_collection', $collection->getData());
        $this->_registry->register('file_model', $this->_mFile);
        $this->_registry->register('object', $this);

        echo $this->_view->getLayout()
        ->createBlock("Revolve\Mage25\Block\Bootstrap")
        ->setTemplate("Revolve_Mage25::list_files.phtml")->toHtml();
        die();
      //  return $collection;




  }
  
  public function getMediaFile($filePath='', $width = 75, $height = 75, $resize=false)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		 $urlHelper = $objectManager->get('\Revolve\Mage25\Helper\Url');
		return $urlHelper->getMediaFile($filePath, $width, $height, $resize);
		
		
		
	}




}
