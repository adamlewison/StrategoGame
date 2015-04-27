<?php

$session = $_POST['session'];

$color = $_POST['color'];
$oppColor = ($color == "R") ? "B" : "R" ;
$team = $_POST['team'];

// OPEN JSON FILE
$json = json_decode(file_get_contents("../gameData/game_$session.json"),true);

$k = $color . "_Start";
$l = $oppColor . "_Start";

$team = json_decode($team);

if (isset($json[$l])) {
	$json = [
		"StartingTeam" => "R",
		"moves" => [],
		$k => $team,
		$l => $json[$l]
	];
} else {
	$json = [
		"StartingTeam" => "R",
		"moves" => [],
		$k => $team
	];
}



if (isset($json[$l])) {
	echo 1;
} else {
	echo 0;
}




// SAVE JSON FILE
file_put_contents("../gameData/game_$session.json", json_encode($json));