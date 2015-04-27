<?php

$session = $_POST['session'];

$color = $_POST['color'];
$oppColor = ($color == "R") ? "B" : "R" ;

// OPEN JSON FILE
$json = json_decode(file_get_contents("../gameData/game_$session.json"),true);

$l = $oppColor . "_Start";

if (isset($json[$l])) {
	echo 1;
} else {
	echo 0;
}