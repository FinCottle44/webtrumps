<?php
$playerName = $_GET["player"];
$playerID = $_GET["playerId"];
$players = $_GET["players"];
$data = $_GET["data"];
$round = $_GET["round"];
$gameId = $_GET["gameid"];
$newGame = $_GET["newGame"];
$gamefile;

if ($newGame == 1){
	//echo 'starting new game';
	
	//sorting existing files
	$files = array();
	$dir = opendir('games'); // open the cwd..also do an err check.
	while(false != ($file = readdir($dir))) {
			if(($file != ".") and ($file != "..") and ($file != "index.php")) {
					$files[] = $file; // put in array.
			}   
	}
	natsort($files); // sort.
	
	//getting final number
	$lastFile = end($files); //3.json
	$without_extension = pathinfo($lastFile, PATHINFO_FILENAME); 
	//getting new file name and creating file
	$fileName = $without_extension + 1;
	echo $fileName; //informing client of gameid (Filename) KEEP ECHO
	$gameFile = fopen('games/' . $fileName . '.json', "w");
	
	//initialising file
	//echo 'array of players count: ' . sizeof($players);
	$playersString = "";
	fwrite($gameFile, '[');
	if (is_array($players) || is_object($players))
	{
		foreach ($players as $value){
			$str = '{';
			$str = $str . initPlayer($value);
			$str = $str . '}';
			$str = $str . ',';
			$playersString = $playersString . $str;
		}
	}
	//echo 'PLAYASTRING: ' . $playersString;
	$playersString = rtrim($playersString, ',');
	fwrite($gameFile, $playersString);
	fwrite($gameFile, ']');
	fclose($gameFile);
}
else{
	//echo 'server says: player ' . $playerName . ' (id: ' . $playerID . ') sent ' . $data . ' in round ' . $round . " of game " . $gameId;
	updateFile($playerID, $data, $round, $gameId);
}

function initPlayer($name){
	$jsonString = '"name":"' . $name .'"';
	return $jsonString;
}

function updateFile($playerID, $data, $round, $gameId){
	$sploded = explode("-", $data);
	$type = $sploded[0];
	$attrName = 'R' . $round . $type;
	//round 7 prediction = r7p
	$value = $sploded[1];
	
	// Get the contents of the JSON file 
	$strJsonFileContents = file_get_contents('games/' . $gameId . '.json');
	// Convert to array 
	$jsonPlayers = json_decode($strJsonFileContents, true);
	$jsonPlayers[$playerID][$attrName] = $value;
	//var_dump($jsonPlayers); // print array
	$newContents = json_encode($jsonPlayers);
	
	if ($type == "S"){
		checkIfPredictionCorrect($round);
	}
	
	file_put_contents('games/' . $gameId . '.json', $newContents);
	
	//$input = array($attrName => $value);
	//$newjsonPlayers = array_push($jsonPlayers[0], $input);
	//$newjsonPlayers = json_decode($strJsonFileContents, true);
	//var_dump($newjsonPlayers);

}

function checkIfPredictionCorrect($round){
	//do l8r maybe return bool
	
}

//**********TODO**********
//- Check if player correct when score is entered, compare RxP and RxS
//
//
//
//help: http://www.kodecrash.com/javascript/read-write-json-file-using-php/

?>