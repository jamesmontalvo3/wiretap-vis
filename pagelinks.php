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


	// -- p_from.page_namespace AS ns_from,
	// -- p_to.page_namespace AS ns_to,
	$data = $conn->query('
		SELECT
			p_from.page_title AS title_from,
			p_to.page_title AS title_to
		FROM pagelinks
		LEFT JOIN page p_from ON pagelinks.pl_from = p_from.page_id
		LEFT JOIN page p_to ON pagelinks.pl_namespace = p_to.page_namespace AND pagelinks.pl_title = p_to.page_title
		WHERE
			p_to.page_title IS NOT NULL AND
			p_to.page_namespace = 0 AND
			p_from.page_namespace = 0
		LIMIT 20000
	');

	$pages = array();
	$toCount = array();
	$links = array();
	foreach($data as $row) {
		// $row <= array('title_from', 'title_to')
		if ( ! in_array( $row['title_from'], $pages) ) {
			$pages[] = $row['title_from'];
		}
		if ( ! in_array( $row['title_to'], $pages) ) {
			$pages[] = $row['title_to'];
		}
		// if ( isset( $toCount[ $row['title_to'] ] ) ) {
		// 	$toCount[ $row['title_to'] ]++;
		// }
		// else {
		// 	$toCount[ $row['title_to'] ] = 1;
		// }
		if ( isset( $fromCount[ $row['title_from'] ] ) ) {
			$fromCount[ $row['title_from'] ]++;
		}
		else {
			$fromCount[ $row['title_from'] ] = 1;
		}
		if ( $fromCount[ $row['title_from' ] ] < 5000 ) {
			$links[] = array(
				"source" => array_search( $row['title_from'] , $pages ),
				"target" => array_search( $row['title_to'],    $pages ),
				"value"  => 1
			);
		}
	}



	foreach($pages as $page) {
		$nodes[] = array(
			"name" => $page,
			"label" => $page,
			"group" => 1
		);

	}

	$json = array( "nodes" => $nodes, "links" => $links );
	$json = json_encode($json);
	header('Content-Type: application/json');
	echo $json;
}
catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
