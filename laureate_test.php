<?php
// get the id parameter from the request
// $id = 6;
// $id = 811;
$id = 811;
$db = new mysqli('localhost', 'cs143', '', 'class_db');
if ($db->connect_errno > 0) { 
    die('Unable to connect to database [' . $db->connect_error . ']'); 
}

$rs = $db -> query("

        with awardeeprize as (select A.id as id, A.date as date, A.city as city, A.country as country, awardYear,category, sortOrder
        from Awardee A, Prize Pr
        where A.id = $id and Pr.id = A.id),
        awardeeprizeinstitution as ( select A.id as id, A.date as date, A.city as city, A.country as country, A.awardYear as awardYear, I.name as insName, I.city as insCity, I.country as insCountry, category, sortOrder
        from awardeeprize A 
        left outer join Institution I on I.id = A.id and I.awardYear = A.awardYear)
        select I.id as id, givenName, familyName, gender, orgName, date, city, country, awardYear, insName, insCity, insCountry, category, sortOrder
        from awardeeprizeinstitution I 
        left outer join People P on I.id = P.id
        left outer join Organization O on I.id = O.id
    ");

if (!$rs) {
    print "Query failed: $db -> error <br>";
    exit(1);
}

$info = array();
$nobelPrizes = array();

while ($row = $rs->fetch_assoc()) {
    $info = array('id'=>$row['id'],'givenName'=>$row['givenName'], 'familyName'=>$row['familyName'],'gender'=>$row['gender'],'orgName'=>$row['orgName'],
    'date'=>$row['date'],'city'=>$row['city'],'country'=>$row['country']);

    // array_push($info, array($row['id'], $row['date'], $row['city'], $row['country'], $row['givenName'], $row['familyName'], $row['gender'], $row['orgName']));

    // $which_award = array($row['awardYear'], $row['category'], $row['sortOrder']);
    $which_award = $row['awardYear'] . "|" . $row['category'] . "|" . $row['sortOrder'];
    if (!array_key_exists($which_award, $nobelPrizes))
    {
        $totalAffli = array(array('awardYear'=>$row['awardYear'], 'category'=>$row['category'], 'sortOrder'=>$row['sortOrder'],'insName'=>$row['insName'], 
        'insCity'=>$row['insCity'], 'insCountry'=>$row['insCountry']));
        $nobelPrizes[$which_award] = $totalAffli;
    }
    else
    {
        array_push($nobelPrizes[$which_award], array('awardYear'=>$row['awardYear'], 'category'=>$row['category'], 'sortOrder'=>$row['sortOrder'],'insName'=>$row['insName'], 
        'insCity'=>$row['insCity'], 'insCountry'=>$row['insCountry']));
    }
}

// set the Content-Type header to JSON, 
// so that the client knows that we are returning JSON data
// print_r($nobelPrizes);
header('Content-Type: application/json');
$result_nobelPrizes = array();
foreach ($nobelPrizes as $eachprize) 
{
    // print_r($eachprize);
    $tmpeachprize = array();
    // $awardYear = $eachprize["awardYear"];
    // $category = $eachprize["category"];
    // $sortOrder = $eachprize["sortOrder"];
    $affiliations = array();
    foreach ($eachprize as $eachinstitution)
    {
        $tmpinstitution = array();
        if(!empty($eachinstitution["insName"]))
        {
            $tmpinsname = array("en"=>$eachinstitution['insName']);
            $tmpinstitution["name"] = $tmpinsname;
            // array_push($tmpinstitution,$eachinstitution['insName']);
            // array_push($affiliations,array($eachinstitution['insName'],$eachinstitution['insCity'],$eachinstitution['insCountry']));
        }
        if(!empty($eachinstitution['insCity']))
        {
            $tmpinscity = array("en"=>$eachinstitution['insCity']);
            $tmpinstitution["city"] = $tmpinscity;
            // array_push($tmpinstitution,$eachinstitution['insCity']);
        }
        if(!empty($eachinstitution['insCountry']))
        {
            $tmpinscountry = array("en"=>$eachinstitution['insCountry']);
            $tmpinstitution["country"] = $tmpinscountry;
            // array_push($tmpinstitution,$eachinstitution['insCountry']);
        }
        if(!empty($tmpinstitution))
        {
            array_push($affiliations,$tmpinstitution);
        }
    }
    if(!empty($eachprize[0]["awardYear"]))
    {
        $tmpeachprize['awardYear'] = $eachprize[0]["awardYear"];
    }
    if(!empty($eachprize[0]["category"]))
    {
        $tmpeachprize['category'] = $eachprize[0]["category"];
    }
    if(!empty($eachprize[0]["sortOrder"]))
    {
        $tmpeachprize['sortOrder'] = $eachprize[0]["sortOrder"];
    }
    if(!empty($affiliations))
    {
        $tmpeachprize['affiliations'] = $affiliations;
    }
    if(!empty($tmpeachprize))
    {
        array_push($result_nobelPrizes, $tmpeachprize);
    }
}

$totalresult = array('id' => $info['id']);
if(empty($info['orgName']))
{
    if(!empty($info['givenName']))
    {
        $tmpgivenName = array("en"=>$info['givenName']);
        $totalresult['givenName'] =  $tmpgivenName;
    }
    if(!empty($info['familyName']))
    {
        $tmpfamilyName = array("en"=>$info['familyName']);
        $totalresult['familyName'] =  $tmpfamilyName;
    }
    if(!empty($info['gender']))
    {
        $totalresult['gender'] =  $info['gender'];
    }
    $tmpbirth = array();
    if(!empty($info['date']))
    {
        $tmpbirth['date'] =  $info["date"];
    }
    $tmpplace = array();
    if(!empty($info['city']))
    {
        $tmpcity = array("en"=>$info['city']);
        $tmpbirth['city'] =  $tmpcity;
    }
    if(!empty($info['country']))
    {
        $tmpcountry = array("en"=>$info['country']);
        $tmpbirth['country'] =  $tmpcountry;
    }
    if(!empty($tmpplace))
    {
        $tmpbirth['place'] = $tmpplace;
    }
    if(!empty($tmpbirth))
    {
        $totalresult["birth"] = $tmpbirth;
    }
    if(!empty($result_nobelPrizes))
    {
        $totalresult['nobelPrizes'] = $result_nobelPrizes;
    }
}
else
{
    if(!empty($info['orgName']))
    {
        $tmpgivenName = array("en"=>$info['orgName']);
        $totalresult['orgName'] =  $tmpgivenName;
    }
    $tmpbirth = array();
    if(!empty($info['date']))
    {
        $tmpbirth['date'] =  $info["date"];
    }
    $tmpplace = array();
    if(!empty($info['city']))
    {
        $tmpcity = array("en"=>$info['city']);
        $tmpbirth['city'] =  $tmpcity;
    }
    if(!empty($info['country']))
    {
        $tmpcountry = array("en"=>$info['country']);
        $tmpbirth['country'] =  $tmpcountry;
    }
    if(!empty($tmpplace))
    {
        $tmpbirth['place'] = $tmpplace;
    }
    if(!empty($tmpbirth))
    {
        $totalresult["founded"] = $tmpbirth;
    }
    if(!empty($result_nobelPrizes))
    {
        $totalresult['nobelPrizes'] = $result_nobelPrizes;
    }
}
echo json_encode($totalresult);
print_r($totalresult);
// print_r($nobelPrizes);
// // print_r($nobelPrizes['2007|Physiology or Medicine|1'][0][4]);
// // print(is_null($nobelPrizes['2007|Physiology or Medicine|1'][0][4]));
// var_dump(empty($nobelPrizes['2007|Physiology or Medicine|1'][0]['insCity']));
// // echo gettype($nobelPrizes['2007|Physiology or Medicine|1'][0][4]);
// $rs->free();

$db -> close();
?>
