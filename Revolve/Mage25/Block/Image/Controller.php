<?php
namespace Revolve\Mage25\Block\Image;


class Controller extends \Revolve\Mage25\Block\Bootstrap
{
		public  $btTable = 'mage25_btimage';
		public  $btInterfaceWidth = "700px";
		public  $btInterfaceHeight = "auto";
		public $associatedTables = array();
		
		
		public  function getBlockTypeDescription() {
			return "Shows Image Block";
		}
		
		public  function getBlockTypeName() {
			return "Image";
		}
		
		public  function installDB(){
			return 'CREATE TABLE IF NOT EXISTS `mage25_btimage` (
				  `bID` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `fID` int(255),
				  `fOnstateID` int(10),
				  `maxWidth` int(10),
				  `maxHeight` int(10),
				  `constrainImage` int(1),
				  `imageLinkType` VARCHAR(255),
				  `externalLink` VARCHAR(255),
				  `internalLinkCID` VARCHAR(100),
				  `altText` VARCHAR(255),
				  `title` VARCHAR(255),
				  PRIMARY KEY (`bID`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;';
		}
		
		public  function updateDB(){
			return '';
		}
		
		public  function upgrade(){
			$this->updateDB();
		}
		
		public  function add($data){
			parent::add($data);
		}
		
		public  function save($data){
			return parent::save($data);
		}
		
		public function view($blockData){
			
			
			parent::view($blockData);
			
			
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
		
