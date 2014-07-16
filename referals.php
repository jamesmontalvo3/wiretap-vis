<?php
error_reporting( -1 );
ini_set('error_reporting', E_ALL);
ini_set( 'display_errors', 1 );
ini_set( 'log_errors', 1 );

function linkMade ($page1, $page2) {
	global $linksMade;
	$oneToTwo = isset( $linksMade[$page1] ) && in_array($page2, $linksMade[$page1]);;
	$twoToOne = isset( $linksMade[$page2] ) && in_array($page1, $linksMade[$page2]);;
	return $oneToTwo || $twoToOne; // link is made if either is source
}

$linksMade = array();

$config = json_decode( file_get_contents(__DIR__ . '/config.json') );
$host = $config->host;
$dbname = $config->dbname;
$username = $config->username;
$password = $config->password;

$d = array();
$users = array();
$pages = array();
$count = 0;
try {
	$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// AND wiretap.referer_title != "Main_Page"
	$data = $conn->query('
		SELECT
			wiretap.user_name AS user_name,
			page.page_title AS target,
			wiretap.referer_title AS referer
		FROM wiretap 
		LEFT JOIN page ON page.page_id = wiretap.page_id
		WHERE
			page.page_namespace = 0 
			AND (wiretap.page_action IS NULL OR wiretap.page_action = "view")
			AND wiretap.referer_title IS NOT NULL
			AND wiretap.referer_title != ""
			AND wiretap.user_name NOT IN ("Ejmontal", "Lwelsh", "Swray", "Balpert")
		ORDER BY wiretap.hit_timestamp ASC
		LIMIT 100000
	');

	$nodes = array();
	$pages = array();
	$links = array();
	foreach($data as $row) {

		$target = $row['target'];
		$referer = $row['referer'];

		if ( ! isset( $pages[$target] ) ) {
			$nextNode = count($nodes);
			$pages[$target] = $nextNode;

			$nodes[ $nextNode ] = array(
				"name" => $target,
				"label" => $target,
				"group" => 1
			);
		}

		if ( ! isset( $pages[$referer] ) ) {
			$nextNode = count($nodes);
			$pages[$referer] = $nextNode;

			$nodes[ $nextNode ] = array(
				"name" => $referer,
				"label" => $referer,
				"group" => 1
			);
		}


		if ( ! linkMade($target, $referer) ) {
			// 	if ($count == 22) {
			// 		echo "Target: $target; Referer: $referer";
			// 		echo "<pre>";
			// 		print_r($linksMade);
			// 		echo "STUFF AND THINGS\n\n\n\n\n";
			// 		print_r($links);
			// 		echo "</pre>";
			// 		die();
			// 	}
			// 	else {$count++;}
			// }
			// else {
			$links[] = array(
				"source" => $pages[$referer],
				"target" => $pages[$target],
				"value"  => 1
			);
		}


		if ( ! isset( $linksMade[$referer] ) ) {
			$linksMade[$referer] = array($target);
		}
		else if ( ! in_array( $target, $linksMade[$referer] ) ) {
			array_push($linksMade[$referer], $target);
		}

		if ( ! isset( $linksMade[$target] ) ) {
			$linksMade[$target] = array($referer);
		}
		else if ( ! in_array( $referer, $linksMade[$target] ) ) {
			array_push($linksMade[$target], $referer);
		}

	}


	$json = array( "nodes" => $nodes, "links" => $links );
	$json = json_encode($json);
	header('Content-Type: application/json');
	echo $json;
}
catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
