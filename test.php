<?php
	require 'vendor/autoload.php';

	$apiKey = "YOUR-API-KEY";
	$db = "test";

	$flybase = new \Flybase\Client( $apiKey,$db );

	$users = $flybase->Users; // where Tasks is your collection

	$lead = array();
	$lead['Name'] = 'Test User';
	$lead['PhoneNumber'] = '123-456-1234';
	$lead['DateCreated']  = date('Y-m-d H:i:s');			

//	Ok, how about inserting a new record:
	$inserted_id = $users->insert( $lead );

//	Now, let’s get all records:
	echo '<h4>Get All</h4>';
	$ret = $users->get();
	echo '<pre>'.print_r($ret,true).'</pre>';

//	We can also get a record based on a single id:
	echo '<hr />';
	echo '<h4>Get By _id</h4>';
	$ret = $users->get('544d8df66865edoc371206478');
	echo '<pre>'.print_r($ret,true).'</pre>';

//	Or, you can query based on other fields:
	echo '<hr />';
	echo '<h4>Get By "PhoneNumber"</h4>';
	$ret = $users->find( array('PhoneNumber'=>'6486490392721') );
	echo '<pre>'.print_r($ret,true).'</pre>';

//	Now, let’s update a record:
	echo '<hr />';
	echo '<h4>Update By "PhoneNumber"</h4>';
	$ret = $users->find( array('PhoneNumber'=>'6486490392721') );
	echo '<pre>'.print_r($ret,true).'</pre>';
	$row = $ret[0];

	$row['Role'] = 'Nobody';
	$ret = $users->updatebyid($row,$row['_id']);

	$ret = $users->find( array('PhoneNumber'=>'6486490392721') );
	echo '<pre>'.print_r($ret,true).'</pre>';

//	Or, you can update this way:
	echo '<hr />';
	echo '<h4>Alternate Update</h4>';
	$ret = $users->find( array("Name"=>"Lorenza Huels") );
	echo '<pre>'.print_r($ret,true).'</pre>';

	$row = array(
		"Role"=>"Teacher",
	);
	$ret = $users->update(array("Name"=>"Lorenza Huels"),$row);

	$ret = $users->find( array("Name"=>"Lorenza Huels") );
	echo '<pre>'.print_r($ret,true).'</pre>';


//	And finally, let’s delete the record:

#	$users->delete('544d8df63cffddoc853542530');
