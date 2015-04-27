<?php

$session = $_POST['gameSession'];

// OPEN JSON FILE
$json = json_decode(file_get_contents("../gameData/game_$session.json"));

echo "{";
echo "StartingTeam: '" . $json->StartingTeam . "',";
echo "B_Start: ";

echo "[";
foreach ($json->B_Start as $v) {
	echo "[";
	foreach ($v as $h) {
		echo "{'color': '" . $h->color . "', 'piece': '" . $h->piece . "'},";
	}
	echo "],";
}
echo "],";

echo "R_Start: ";

echo "[";
foreach ($json->R_Start as $v) {
	echo "[";
	foreach ($v as $h) {
		echo "{'color': '" . $h->color . "', 'piece': '" . $h->piece . "'},";
	}
	echo "],";
}
echo "]";

echo "}";
