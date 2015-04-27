<?php

$session = $_POST['gameSession'];
$color = $_POST['myColor'];

// OPEN JSON FILE
$json = json_decode(file_get_contents("../gameData/game_$session.json"));

$lastMove = end($json->moves);



if ($lastMove->color != $color && isset($lastMove->color) || isset($_POST['confirmMove']) && $lastMove->color == $color) {
	echo "{";
		echo "'oldpos': '" . $lastMove->oldpos . "', 'newpos': '" . $lastMove->newpos ."', 'color': '" . $lastMove->color . "'";
	echo "}";
} else {
	echo 0;
}