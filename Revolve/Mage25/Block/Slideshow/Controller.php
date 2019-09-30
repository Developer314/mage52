<?php
namespace Revolve\Mage25\Block\Slideshow;


class Controller extends \Revolve\Mage25\Block\Bootstrap
{
		public  $btTable = 'mage25_btslideshow';
		public  $btInterfaceWidth = "800px";
		public  $btInterfaceHeight = "auto";
		public  $associatedTables = array('mage25_btslideshowimg');
		protected $_mFile;
		
		
		
		public  function getBlockTypeDescription() {
			return __("Display a running loop of images.");
		}
		
		public  function getBlockTypeName() {
			return __("Slideshow");
		}
		
		public  function installDB(){
			
			$query1= 'CREATE TABLE IF NOT EXISTS `mage25_btslideshow` (
				  `bID` int(10) unsigned NOT NULL AUTO_INCREMENT,
				   `slideshow_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `fsID` int(10) unsigned DEFAULT NULL,
				  PRIMARY KEY (`bID`)
				);';
				
				$query2 = 'CREATE TABLE IF NOT EXISTS `mage25_btslideshowimg` (
				  `slideshowImgId` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `bID` int(10) unsigned DEFAULT NULL,
				  `fID` int(10) unsigned DEFAULT NULL,
				  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `link_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `description` text COLLATE utf8_unicode_ci,
				  `page_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `external_link` text COLLATE utf8_unicode_ci,
				  `link_text` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `target` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  PRIMARY KEY (`slideshowImgId`)
				);';
				
				return  array($query1, $query2);
			
		}
		
		public  function updateDB(){
			return '';
		}
		
		public  function upgrade(){
			$this->updateDB();
		}
		
		
		
		public  function save($data){
			
			
			$slidedata=array();
			foreach($data['data'] as $k=>$v){
				if(preg_match('/fID_/', $k)){
					$id = explode('_', $k);
					$id = $id[1];
					$slideItem = array($v, $data['data']['title'][$id], $data['data']['description'][$id], $data['data']['link_type'][$id], $data['data']['external_link'][$id],$data['data']['cID_'.$id],$data['data']['link_text'][$id], $data['data']['target'][$id]);
					array_push($slidedata, $slideItem);
					unset($data['data'][$k]);
					unset($data['data']['cID_'.$id]);
				}
			}
			
			unset($data['data']['title']);
			unset($data['data']['description']);
			unset($data['data']['link_type']);
			unset($data['data']['external_link']);
			unset($data['data']['link_text']);
			unset($data['data']['target']);
			
			/*echo "<pre>";
			print_r($data);
			print_r($slidedata);
			die();
			*/
			
			$bID =  parent::save($data);
			
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection();
			$connection->query("DELETE from mage25_btslideshowimg where bID=".$bID);
			
			
			
			if($data['data']['slideshow_type'] == 'CUSTOM'){
				foreach($slidedata as $slide){
					$connection->query("INSERT INTO mage25_btslideshowimg (`bID`, `fID`, `title`, `link_type`, `description`, `page_id`, `external_link`, `link_text`, `target`) VALUES (".$bID.", ".$slide[0].", '".$slide[1]."', '".$slide[3]."', '".$slide[2]."', '".$slide[5]."', '".$slide[4]."', '".$slide[6]."', '".$slide[7]."')");
				}
			}
			
			return $bID;
			
			
	    	
		}
		
		public function view($blockData){
			
			
			return parent::view($blockData);
			
			
		}
		
		public function getFileModel(){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			
			$field_helper = $objectManager->get('\Revolve\Mage25\Model\File');
			return $field_helper;
		}
		
		public function getFilesByfsID($fsID){
			$mFile = $this->getFileModel();
			return $mFile->getFidsFromFsID($fsID);
		}
		
		public function getFileSets(){
			$mFile = $this->getFileModel();
			return $mFile->getFileSets();
		}
		
		public function getFileByID($fID){
			$mFile = $this->getFileModel();
			return $mFile->getFileByID($fID);
		}
		
		public function getSlides($bID){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection();
			
			$slides = $connection->fetchAll("SELECT * from mage25_btslideshowimg WHERE bID=".$bID);
			return $slides;
		}
		
	
		
		
		public  function displayFilePicker($field, $fID=0){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			
			$field_helper = $objectManager->get('\Revolve\Mage25\Helper\Field');
			return $field_helper->displayFilePicker($field, $fID);
		}
		
		public  function displayPagePicker($field, $cID=0){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$field_helper = $objectManager->get('\Revolve\Mage25\Helper\Field');
			return $field_helper->displayPagePicker($field, $cID);
		}
		
		public function getImageForView($fID, $resize = false, $width="0", $height="0"){
			
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$field_helper = $objectManager->get('\Revolve\Mage25\Helper\Url');
				
				$imagePath = $field_helper->getFileUrl($fID);
				
				if($this->isImage($imagePath)){
					if($resize){
						$imagePath = $field_helper->getFileUrl($fID, false);
						$imageHelper = $objectManager->get('\Revolve\Mage25\Helper\Image');
						$imagePath = $imageHelper->resize($imagePath, $width, $height);
					}
					return $imagePath;
				}
				
			
			
			return false;
			
			
		}
		
		public function isImage($fPath){
			
			$ext = substr($fPath, strrpos($fPath, '.')+1);
			$allowedExtensions = array('gif','png','jpg','jpeg','JPG','JPEG');
			if(in_array($ext, $allowedExtensions)){
				return true;
			}else{
				return false;
			}
		}
		
		public function getLink($cID){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$field_helper = $objectManager->get('\Revolve\Mage25\Helper\Url');
			return $field_helper->getUrlFromString($cID);
		}
		
		

}