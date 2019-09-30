<?php
namespace Revolve\Mage25\Block\Youtube;


class Controller extends \Revolve\Mage25\Block\Bootstrap
{
		public  $btTable = 'mage25_btyoutube';
		public  $btInterfaceWidth = "600";
		public  $btInterfaceHeight = "465";
		
		
		
		public  function getBlockTypeDescription() {
			return __("Embeds a YouTube Video in your web page.");
		}
		
		public  function getBlockTypeName() {
			return __("Youtube Video");
		}
		
		public  function installDB(){
			return 'CREATE TABLE IF NOT EXISTS `mage25_btyoutube` (
  `bID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `YouTubeVideoURL` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vWidth` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vHeight` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vPlayer` int(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`bID`)
); ';
		}
		
		public  function updateDB(){
			return '';
		}
		
		public  function upgrade(){
			$this->updateDB();
		}
		
		public  function add($data){
			return parent::add($data);
		}
		
		public  function save($data){
			return parent::save($data);
		}
		
		public function view($blockData){
			return parent::view($blockData);
		}
}