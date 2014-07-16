<?php

$config = json_decode( file_get_contents(__DIR__ . '/config.json') );
$host = $config->host;
$dbname = $config->dbname;
$username = $config->username;
$password = $config->password;

$d = array();
$users = array();
$pages = array();

try {
	$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


	if ( $_GET['action'] == 'allwiretap' ) {
		$data = $conn->query('SELECT user_name, page_name FROM wiretap ORDER BY hit_timestamp DESC LIMIT 20000');
	}
	else if ( $_GET['action'] == 'editwiretap' ) {
		$data = $conn->query('
			SELECT wiretap.user_name, page.page_title
			FROM wiretap 
			LEFT JOIN page ON page.page_id = wiretap.page_id
			WHERE page.page_namespace = 0 AND page_action IN ("edit", "edit.", "formcreate", "formedit")
			ORDER BY wiretap.hit_timestamp DESC
			LIMIT 200000
		');
	}
	else if ( $_GET['action'] == 'mainwiretap' ) {
		$data = $conn->query('
			SELECT wiretap.user_name, page.page_title
			FROM wiretap 
			LEFT JOIN page ON page.page_id = wiretap.page_id
			WHERE page.page_namespace = 0
			ORDER BY wiretap.hit_timestamp DESC
			LIMIT 200000
		');
	}




	/*
	
	*/
	// echo "<pre>"; print_r($data->fetchAll()); echo "</pre>";
	// die();
	foreach($data as $row) {
		$user = $row['user_name'];
		$page = $row['page_title'];

		if ( ! in_array($user, $users) ) {
			$users[] = $user;
		}

		if ( ! in_array($page, $pages) ) {
			$pages[] = $page;
		}

		if ( ! isset( $d[$user] ) ) {
			$d[$user] = array();
		}

		if ( ! isset( $d[$user][$page] ) ) {
			$d[$user][$page] = 1;
		}
		else {
			$d[$user][$page]++;
		}

		if ($d[$user][$page] > $maxHits) {
			$maxHits = $d[$user][$page];
		}
	}
	// $out = array();
	// foreach($d as $user => $pageinfo) {
	// 	foreach($pageinfo as $page => $count) {
	// 		$out[] = array($user,$page,$count);
	// 	}
	// }

	$out = array(
		"hits" => $d,
		"users" => $users,
		"pages" => $pages,
		"maxHits" => $maxHits,
	);

	$json = json_encode($out);
	header('Content-Type: application/json');
	echo $json;
}
catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
