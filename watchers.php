<?php

if ( true ) {

	// turn error logging on
	error_reporting( -1 );
	ini_set( 'display_errors', 1 );
	ini_set( 'log_errors', 1 );

	// Output errors to log file
	// ini_set( 'error_log', __DIR__ . '/php.log' );

}

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
	// $conn = new PDO('mysql:host=localhost;dbname=wiretap', $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


	// -- p_from.page_namespace AS ns_from,
	// -- p_to.page_namespace AS ns_to,
	// LEFT JOIN page p_from ON pagelinks.pl_from = p_from.page_id
	// LEFT JOIN page p_to ON pagelinks.pl_namespace = p_to.page_namespace AND pagelinks.pl_title = p_to.page_title
	$data = $conn->query('
		SELECT
			watchlist.wl_title AS title,
			watchlist.wl_notificationtimestamp AS notification,
			user.user_name AS user_name,
			user.user_real_name AS real_name
		FROM watchlist
		LEFT JOIN user ON user.user_id = watchlist.wl_user
		WHERE
			wl_namespace = 0
		LIMIT 20000
	');
//			AND user.user_name NOT IN (\'Lwelsh\',\'Swray\',\'Balpert\',\'Ejmontal\',\'Cmavridi\', \'Sgeffert\', \'Smulhern\', \'Kgjohns1\', \'Bscheib\', \'Ssjohns5\')

	$nodes = array();
	$pages = array();
	$users = array();
	$links = array();
	foreach($data as $row) {

		// if the page isn't in $pages, then it's also not in $nodes
		// add to both
		if ( ! isset( $pages[ $row['title'] ] ) ) {
			$nextNode = count($nodes);

			$pages[ $row['title'] ] = $nextNode;

			// $nodes[ $nextNode ] = $row['title'];
			$nodes[ $nextNode ] = array(
				"name" => $row['title'],
				"label" => $row['title'],
				"group" => 1
			);
		}

		// same for users...add to $users and $nodes accordingly
		if ( ! isset( $users[ $row['user_name'] ] ) ) {
			$nextNode = count($nodes);

			$users[ $row['user_name'] ] = $nextNode;

			$nodes[ $nextNode ] = $row['user_name'];
			if ( $row['real_name'] !== NULL && trim($row['real_name']) !== '' ) {
				$displayName = $row['real_name'];
			}
			else {
				$displayName = $row['user_name'];
			}

			$nodes[ $nextNode ] = array(
				"name" => $displayName,
				"label" => $displayName,
				"group" => 2,
				"weight" => 1
			);

		}
		else {
			$userNodeIndex = $users[ $row['user_name'] ];
			$nodes[ $userNodeIndex ]['weight']++;
		}

		if ( $row['notification'] == NULL ) {
			$linkClass = "link";
		}
		else {
			$linkClass = "unreviewed";
		}

		// if ( $linkClass !== "unreviewed" ) {
			$links[] = array(
				"source" => $users[ $row['user_name'] ],
				"target" => $pages[ $row['title']     ],
				"value"  => 1,
				"linkclass" => $linkClass
			);
		// }
	}




	$json = array( "nodes" => $nodes, "links" => $links );
	$json = json_encode($json);
	header('Content-Type: application/json');
	echo $json;
}
catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
