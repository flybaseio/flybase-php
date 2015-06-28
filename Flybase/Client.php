<?php

namespace Flybase;

class Client{
	private $db;
	private $col;
	private $apiKey;
	private $request;
	private $uri = 'https://api.flybase.io/apps/';
	private $push_uri = 'http://push.flybase.io/emit/';
	private $q = array();

	public function __construct($apiKey,$db,$col=''){
		$this->db = $db;
		$this->col = $col;
		$this->apiKey = $apiKey;
		$this->request = new \Flybase\Request($apiKey);
	}

	public function __destruct(){
		
	}

	public function drop(){
		
	}

	public function collection($col){
		$this->col = $col;
		return $this;
	}
	
	public function __get($col){
		$col = strtolower( $col );
		return $this->collection($col);
	}

	public function get($key=''){
		if( !empty($key) ){
			$url = $this->uri.$this->db.'/collections/'.$this->col.'/'.$key.'?apiKey='.$this->apiKey;
		}else{
			$url = $this->uri.$this->db.'/collections/'.$this->col.'?apiKey='.$this->apiKey;
		}
		$ret = $this->request->get($url);
		return json_decode( $ret,true );
	}

	public function where( $where ){
		$this->q['q'] = $where;
		return $this;
	}
	public function fields( $field ){
		$this->q['f'] = $field;
		return $this;
	}
	
	public function skip( $n ){
		$this->q['sk'] = $n;
		return $this;
	}
	
	public function orderBy( $o ){
		$this->q['s'] = $o;
		return $this;
	}
	
	public function limit( $n ){
		$this->q['l'] = $n;
		return $this;
	}
	public function limitToFirst( $n ){
		$this->q['l'] = $n;
#		$this->q['s'] = array("_id" => 1);
		return $this;
	}
	public function limitToLast ( n ){
		$this->q['l'] = n;
		$this->q['s'] = array("_id" => -1);
		return $this;
	}


	public function on($event, $callback){
		if( $event == 'value' ){
			$query = $this->q['q'];
			return $this->find( $query, $this->q );
		}else{
			//	still to implement the other versions...
		}
	}

	public function find( $query, $meta = array() ){
		return $this->query( $query, $meta );
	}


	public function query( $query, $meta = array() ){
		$q = json_encode( $query );
		$q = urlencode( $q );
		$url = $this->uri.$this->db.'/collections/'.$this->col.'?apiKey='.$this->apiKey.'&q='.$q;
		if( count($meta) ){
			$mv = array();
			foreach($meta as $k=>$v){
				if( $k == 'q' ) continue;
				if( is_array($v) ){
					$v = json_encode( $v );
				}
				$mv[] = $k.'='.$v;
			}
			$mv = implode("&",$mv);
			$url = $url."&".$mv;
		}
		$ret = $this->request->get($url);
		return json_decode( $ret,true );
	}

	public function insert($vars){
		$row = $vars;
		$url = $this->uri.$this->db.'/collections/'.$this->col.'?apiKey='.$this->apiKey;
		$row = json_encode( $row );
		$ret = $this->request->post($url,$row);
		$ret = json_decode( $ret,true );
		$id = $ret['_id']['$oid'];
		return $id;
	}

	public function update($where,$vars){
		$res = $this->find( $where );
		$ret = false;
		if( count($res) ){
			foreach($res as $row){
				$key = $row['_id'];
				$ret = $this->updatebyid($vars,$key);
			}
		}
		return $ret;
	}

	public function updatebyid($vars,$key){
		$row = $vars;
		$url = $this->uri.$this->db.'/collections/'.$this->col.'/'.$key.'?apiKey='.$this->apiKey;
		$row = json_encode( $row );
		$ret = $this->request->put($url,$row);
		$ret = json_decode( $ret,true );
		return $ret;
	}

	public function delete($key){
		$url = $this->uri.$this->db.'/collections/'.$this->col.'/'.$key.'?apiKey='.$this->apiKey;
		$ret = $this->request->delete($url);
		return json_decode( $ret,true );
	}

/*	
	Real time notifications	
	
	push.flybase.io is our push server, you can use trigger to push a message to clients listening to the same channel.
	
	Channels are an md5 hash of db name and collection.
*/
	public function emit($event,$message){
		$this->trigger($event,$message);
	}
	public function trigger($event,$message){
		//	we create a channel, channels are an md5 hash of db name and collection...
		$channel = md5( $this->db.'/'.$this->collection );
		$url = $this->push_uri.$channel.'/'.$event.'/'.$message;
		$ret = $this->request->get($url);
	}
}