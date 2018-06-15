<?php

namespace Flybase;

/*	private functions for calling the API.. Change these at your own risk	*/

class Request{
	private $apikey;
	public function __construct( $apikey ){
		$this->apikey = $apikey;
		return true;	
	}

	public function get($url){
		return $this->_curl($url,'','GET');
	}

	public function delete($url){
		return $this->_curl($url,'DELETE');
	}
	
	public function put($url,$args){
		return $this->_curl($url,$args,'PUT');
	}

	public function post($url,$args){
		return $this->_curl($url,$args,'POST');
	}

/*
	_curl is a private function which handles all API requests.
	
		-	$url:	URL to the API
		-	$args:	Array of fields to pass
		-	$type:	either GET, PUT, POST or DELETE
*/
	private function _curl($url,$args,$type=''){

		$timeout=5;

		$timestamp = time();
		$signature = sha1($this->apikey . ' ' . $args . ' ' . $timestamp);
	
		$headers = array(
			'X-Flybase-API-Key: '.$this->apikey,
			'X-Flybase-API-Signature: '. $signature,
			'X-Flybase-API-Timestamp: '. $timestamp

		);

		$ch = curl_init($url);
		if( $type == 'GET' ){
			curl_setopt($ch,CURLOPT_HEADER,false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			$data = curl_exec($ch);
			curl_close($ch);
		}else{
			$headers[] = 'Content-Type: application/json; charset=utf-8';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			if( $type != '' || $type != 'POST'){
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
			}else{
				curl_setopt($ch, CURLOPT_POST, true);
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
			$data = curl_exec($ch);
			curl_close($ch);
		}
		return $data;		
	}
}
