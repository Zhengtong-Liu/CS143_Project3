<?php
// get the id parameter from the request
$id = intval($_GET['id']);

$db = new mysqli('localhost', 'cs143', '', 'class_db');
if ($db->connect_errno > 0) { 
    die('Unable to connect to database [' . $db->connect_error . ']'); 
}

$rs = $db -> query("
        with info as (select A.id as id, A.date as date, A.city as city, A.country as country, I.awardYear as awardYear, I.name as insName, I.city as insCity, I.country as insCountry, category, sortOrder
        from Awardee A, Institution I, Prize Pr
        where A.id = $id and I.id = A.id and Pr.id = A.id and I.awardYear = Pr.awardYear)
        select I.id as id, date, city, country, awardYear, insName, insCity, insCountry, category, sortOrder, givenName, familyName, gender, orgName
        from info I 
        left outer join People P on I.id = P.id
        left outer join Organization O on I.id = O.id
    ");

if (!$rs) {
    print "Query failed: $db -> error <br>";
    exit(1);
}

$info = array();
$prizes = array();

while ($row = $rs->fetch_assoc()) {
    array_push($info, array($row['id'], $row['date'], $row['city'], $row['country'], $row['givenName'], $row['familyName'], $row['gender'], $row['orgName']));
    $prize = array($row['awardYear'], $row['category'], $row['sortOrder']);
    if (array_key_exists($prize, $prizes)) {
        array_push($prizes[$prize], array($row['insName'], $row['insCity'], $row['insContry']));
    }
    else {
        $prizes[$prize] = array();
        array_push($prizes[$prize], array($row['insName'], $row['insCity'], $row['insContry']));
    }
}

$info = array_unique($info)[0];
// set the Content-Type header to JSON, 
// so that the client knows that we are returning JSON data
header('Content-Type: application/json');

/*
   Send the following fake JSON as the result
   {  "id": $id,
      "givenName": { "en": "A. Michael" },
      "familyName": { "en": "Spencer" },
      "affiliations": [ "UCLA", "White House" ]
   }
*/
if ( !empty($info[4]))
{
    $output = (object) [
        "id" => strval($id),
        "givenName" => (object) [
            "en" => $info[4]
        ],
        "familyName" => (object) [
            "en" => $info[5]
        ],
        "gender" => $info[6],
        "birth" => (object) [
            "date" => $info[1],
            "place" => (object) [
                "city" => (object) [
                    "en" => $info[2]
                ],
                "country" => (object) [
                    "en" => $info[3]
                ]
            ]
        ],
        "affliations" => array(
            "UCLA",
            "White House"
        )
    ];
} else {

}

echo json_encode($output);

$rs->free();

$db -> close();
?>
