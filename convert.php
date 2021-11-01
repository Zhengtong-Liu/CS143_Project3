<?php 

// read JSON data
$file_content = file_get_contents("/home/cs143/data/nobel-laureates.json");
$data = json_decode($file_content, true);

// get the id, givenName, and familyName of the first laureate
$laureate = $data["laureates"][0];
$id = $laureate["id"];
$givenName = $laureate["givenName"]["en"];
$familyName = $laureate["familyName"]["en"];

// print the extracted information
echo $id . "\t" . $givenName . "\t" . $familyName . PHP_EOL

?>
