<?php
namespace Revolve\Mage25\Helper;
use \Revolve\Mage25\Model\File as RevolveFile;
class Url extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_urlInterface;
	protected $_helperUrl;
	protected $categoryRepository;
	protected $_storeManager;
	protected $_mFile;
	protected $_assetRepo;
   	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
  
        \Magento\Backend\Helper\Data $helperBackend,
       
         \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
       
         RevolveFile $revolveFile,
         array $data = []
    ){
	    $this->_urlInterface = $context->getUrlBuilder();
	     $this->_helperUrl = $helperBackend;
	    $this->_storeManager = $context->getStoreManager();
	      $this->categoryRepository = $categoryRepository;
	      $this->_mFile = $revolveFile;
	      $this->_assetRepo = $context->getAssetRepository();
	       //$this->_storeManager = $storeManager;
    }
    
   public function translateToView($content){
	   
	   		$newContent = $content;
	   		$dom = new \DOMDocument();
			$dom->strictErrorChecking = false; 
			@$dom->loadHTML($content); 
			$links = $dom->getElementsByTagName('a'); 
			if(sizeof($links) > 0){
				foreach($links as $link) { 
				   if ($link->hasAttribute('href')) { 
				      $href = $link->getAttribute('href'); 
				      if(preg_match('/{{/', $href) and preg_match('/}}/', $href) ){
					      
					      $urlparts = str_replace('{{', '', $href);
						 $urlparts = str_replace('}}', '', $urlparts);
						 $urlparts = explode('-', $urlparts);
						 switch($urlparts[0]){
							 case "product":
							 	$urlToReplace = $this->getProductUrl($href);
							 	$newContent = str_replace('{{product-'.$urlparts[1].'}}', $urlToReplace, $newContent);
							 break;
							 case "category":
							 	$urlToReplace = $this->getCategoryUrl($href);
							 	$newContent = str_replace('{{category-'.$urlparts[1].'}}', $urlToReplace, $newContent);
							 break;
							 case "page":
							 	$urlToReplace = $this->getCmsUrl($href);
							 	$newContent = str_replace('{{page-'.$urlparts[1].'}}', $urlToReplace, $newContent);
							 break;
							 
							 case "file":
							 	$urlToReplace = $this->getFileUrl($href);
							 	$newContent = str_replace('{{file-'.$urlparts[1].'}}', $urlToReplace, $newContent);
							 break;
							 
							 
							 
						 }
						 
						 
				      }
				    } 
				} 
			}

			return $newContent;
	   
	   
   }
   public function getID($href){
	   
	 $urlparts = str_replace('{{', '', $href);
	 $urlparts = str_replace('}}', '', $urlparts);
	 $urlparts = explode('-', $urlparts);
	 if(sizeof($urlparts)>1){
	 	return $urlparts[1];
	 }else{
		 return $urlparts[0];
	 }
	   
   }
   
   public function getCategoryObject($categoryID){
	   return $this->categoryRepository->get($categoryID, $this->_storeManager->getStore()->getId());
   }
   
   public function getProductObject($productID){
	   $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	   return $objectManager->get('\Magento\Catalog\Model\Product')->load($productID);
   }
   
   public function getCategoryUrl($categoryID){
	   $categoryID=$this->getID($categoryID);
	   return $this->getCategoryObject($categoryID)->getUrl();
  }
  
  public function getPageObject(){
	  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	    return $objectManager->get('\Magento\Cms\Helper\Page');
  }
   
    public function getProductUrl($productID){
	  
	  	$productID=$this->getID($productID);
		return $this->getProductObject($productID)->getProductUrl();
   }
   
    public function getCmsUrl($pageID){
	     $pageID=$this->getID($pageID);
	     return $this->getPageObject()->getPageUrl($pageID);
	   
   }
   
   public function getUrlFromString($idString){
	  
	     $urlparts = str_replace('{{', '', $idString);
		 $urlparts = str_replace('}}', '', $urlparts);
		 $urlparts = explode('-', $urlparts);
		 switch($urlparts[0]){
			 case "product":
			 	return $this->getProductUrl($idString);
			 	
			 break;
			 case "category":
			 	return $this->getCategoryUrl($idString);
			 	
			 break;
			 case "page":
			 	
			 	return $this->getCmsUrl($idString);
			 	
			 break;
			 
			 case "file":
			 	return $this->getFileUrl($idString);
			 	
			 break;
			 
			 
			 
		 }
   }
   
   public function getFileUrl($fID, $storeUrl = true){
	    $fID=$this->getID($fID);
	    $fileData = $this->_mFile->getFileByID($fID);
	    if($storeUrl){
	    return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$fileData['fPath'];
	    }else{
		    return $fileData['fPath'];
	    }
   }
   
   public function getMediaFile($filePath='', $width = 75, $height = 75, $resize=false){
	   if($filePath == ''){ $filePath = $this->_assetRepo->getUrl('Revolve_Mage25::images/missing.png'); }
		
		$fileExt = pathinfo($filePath, PATHINFO_EXTENSION);
		
		$widthS = '';
		if($width > 0){
			$widthS = 'width:'.$width.'px;';
		}
		
		$heightS = '';
		if($height > 0){
			$heightS = 'height:'.$height.'px;';
		}
		
		$attr = 'style="'.$widthS.' '.$heightS.'"';
		
		
		switch($fileExt){
		case 'png':
		case 'PNG':
		case 'jpeg':
		case 'JPEG':
		case 'jpg':
		case 'jpg':
		case 'gif':
		
			return '<img src="'.$this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$filePath.'" '.$attr.'>';
		break;
		
		case 'pdf':
			return '<img src="'.$this->_assetRepo->getUrl('Revolve_Mage25::images/pdf.png').'" '.$attr.'>';
		break;
		
		case 'zip':
			return '<img src="'.$this->_assetRepo->getUrl('Revolve_Mage25::images/zip.png').'" '.$attr.'>';
		break;
		case 'doc':
		case 'docx':
			return '<img src="'.$this->_assetRepo->getUrl('Revolve_Mage25::images/word.png').'" '.$attr.'>';
		break;
		case 'txt':
			return '<img src="'.$this->_assetRepo->getUrl('Revolve_Mage25::images/text.png').'" '.$attr.'>';
		break;
		case 'mp3':
		case 'mov':
		case 'swf':
		case 'flv':
		case 'mp4':
		case 'ogg':
		case 'webm':
			return '<img src="'.$this->_assetRepo->getUrl('Revolve_Mage25::images/video.png').'" '.$attr.'>';
		break;
		default:
		return '<img src="'.$this->_assetRepo->getUrl('Revolve_Mage25::images/text.png').'" '.$attr.'>';
		break;
		
		
		}
   }
   
   
   
   
   
}
?>