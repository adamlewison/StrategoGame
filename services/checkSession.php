<?php

$session = $_POST['session'];

if (file_exists("../gameData/game_$session.json")) {
	echo 1;
} else {
	echo 0;
	$f = fopen("../gameData/game_$session.json", "w");
	fwrite($f, "{'StartingTeam': 'R', 'moves': []}");
}