<?php

$session = $_POST['gameSession'];

// OPEN JSON FILE
$json = json_decode(file_get_contents("../gameData/game_$session.json"));


$json->moves[] = [
	"oldpos" 	=> $_POST['oldpos'],
	"newpos" 	=> $_POST['newpos'],
	"color"		=> $_POST['myColor']
];

//print_r(end($json->moves));

echo "{'oldpos': '" . end($json->moves)['oldpos'] . "', 'newpos': '" . end($json->moves)['newpos'] . "', 'color': '" . end($json->moves)['color'] . "'}";


// SAVE JSON FILE
file_put_contents("../gameData/game_$session.json", json_encode($json));