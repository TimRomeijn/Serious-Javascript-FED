<?php

$db_host = 'localhost';
$db_user = '0894594';
$db_password = '57da9b39';
$db_database = '0894594';

$db = mysqli_connect($db_host,$db_user,$db_password,$db_database)
or die(mysqli_connect_error());

// het connecten van de database voor variabele con
$con = mysqli_connect("localhost", "0894594", "57da9b39", "0894594");

// de functie waarmee selectquerys uitgevoerd kunnen worden
function doSelectQuery($db, $query){

    $arrayResults = false;

    if($result = mysqli_query($db, $query)){
        $arrayResults = array();
        while($row = mysqli_fetch_assoc($result)){
            array_push($arrayResults, $row);
        }
    }
    else{
        echo mysqli_error($db) . 'QUERY' . $query;
    }
    return $arrayResults;
}
// de functie waarmee de data in de database gestopt kan worden
function doQuery($query,$db){
    mysqli_query($db,$query);
}