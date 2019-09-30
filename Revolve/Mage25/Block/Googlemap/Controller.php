<?php
namespace Revolve\Mage25\Block\Googlemap;


class Controller extends \Revolve\Mage25\Block\Bootstrap
{
		public  $btTable = 'mage25_btgooglemap';
		public  $btInterfaceWidth = "600px";
		public  $btInterfaceHeight = "auto";
		
		
		
		public  function getBlockTypeDescription() {
			return __("Google Map");
		}
		
		public  function getBlockTypeName() {
			return __("Google Map");
		}
		
		public  function installDB(){
			return 'CREATE TABLE IF NOT EXISTS `mage25_btgooglemap` (
  `bID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext,
  `location` varchar(200),
  `latitude` varchar(200),
  `longitude` varchar(200),
  `width` varchar(200),
  `height` varchar(200),
  `zoom` varchar(200),
  `mapkey` varchar(200),
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
			return parent::add($data);
		}
		
		public  function save($data){
	      
		  $data['data'] = $data['data'] + array(
            'latitude' => 0,
            'longitude' => 0,
        );
		  
        $data['data']['title'] = ($data['data']['title'] != '') ? $data['data']['title'] : '';
        $data['data']['location'] = ($data['data']['location'] != '') ? $data['data']['location'] : '';
       $data['data']['zoom'] = (intval($data['data']['zoom']) >= 0) && (intval($data['data']['zoom'])<=21) ? intval($data['data']['zoom']) : 14;
        $data['data']['width'] = intval($data['data']['width']);
		 $data['data']['height'] = intval($data['data']['height']);
		 
        if (strlen( $data['data']['location'])>0 ){
			$coords = $this->lookupLatLong($data['data']['location']);
			$data['data']['latitude']=floatval($coords['lat']);
				$data['data']['longitude']=floatval($coords['lng']);
		} else {
			$data['data']['latitude']=0;
			$data['data']['longitude']=0;
			}
		  
	    	return parent::save($data);
		}
		
		public function view($blockData){
			
			
			return parent::view($blockData);
			
			
		}
		
	public function lookupLatLong($address) {
			if(preg_match('/^\\s*([+\\-]?\\d+(\\.\\d*)?)[\\s,;]+([+\\-]?\\d+(\\.\\d*)?)\\s*$/', $address, $matches)) {
				return array('lat' => floatval($matches[1]), 'lng' => floatval($matches[3]));
			}
		
			$base_url = "http://maps.google.com/maps/api/geocode/json?sensor=false";
			$request_url = $base_url . "&address=".urlencode($address);
			
			$res = file_get_contents($request_url);
			$res = json_decode($res);
			if(!is_object($res)) { 
				return false;
			}
			switch($res->status) {
				case 'OK':
					$lat = $res->results[0]->geometry->location->lat;
					$lng = $res->results[0]->geometry->location->lng;
					return array('lat'=>$lat,'lng'=>$lng);
					break;
				case 'ZERO_RESULTS':
				case 'OVER_QUERY_LIMIT':
				case 'REQUEST_DENIED':
				case 'INVALID_REQUEST':
					return false;
					break;
			}
		}	
		
		

}