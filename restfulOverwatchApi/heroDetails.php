<?php

require_once 'database.php';

$method = $_SERVER["REQUEST_METHOD"];

$accept = $_SERVER["HTTP_ACCEPT"];

$parsedUrl = 'https://stud.hosted.hr.nl/0894594/restful/restfulMusicOpdracht/music';

$data = array();

function array2XML($obj, $array)
{
    foreach ($array as $key => $value)
    {
        if(is_numeric($key))
            $key = 'item' . $key;

        if (is_array($value))
        {
            $node = $obj->addChild($key);
            array2XML($node, $value);
        }
        else
        {
            $obj->addChild($key, htmlspecialchars($value));
        }
    }
}

switch($method)
{
    case "OPTIONS":
        header("Allow: GET,OPTIONS,PUT,DELETE");

        break;


    case "GET":
        $x = $_GET['id'];
        $sql = "SELECT * FROM overwatch WHERE id = $x ";
        $result = mysqli_query($db, $sql);
        $object = null;

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $item = [
                    "hero" => $row["hero"],
                    "ultimateAbility" => $row["ultimateAbility"],
                    "role" => $row["role"],
                    "links" =>
                        [
                            [
                                "rel" => "self", "href" => $parsedUrl.'/' .$row["id"]
                            ],
                            [
                                "rel" => "collection", "href" => $parsedUrl
                            ]
                        ]

                ];
                $object = $item;
            }

        }

        if ($accept == "application/json") {
            header("Content-Type: application/json");
            $json = json_encode($object);

            if($json === "null")
            {
                header("HTTP/1.1 404 Not Found.");
            }
            else
                echo $json;

       }
        else if ($accept == "application/xml") {
            // create new instance of simplexml
            $xml = new SimpleXMLElement('<song/>');

            // function callback
            array2XML($xml, $object);

            // save as xml file
            echo $xml->asXML();


            header("Content-Type: application/xml");

        }


        break;
    case "PUT":
        $x = $_GET['id'];
        $contentType = $_SERVER["CONTENT_TYPE"];
        if ($contentType == "application/json") {
            $body = file_get_contents("php://input");
            $json = json_decode($body);

            if (!isset($json->ultimate) || $json->ultimate == "" || !isset($json->hero) || $json->hero == "" || $json->role == "" ||  !isset($json->role))
            {
                header("HTTP/1.1 400 Bad Request");
            }
            else
            {
                $ultimate = $json->ultimate;
                $hero = $json->hero;
                $role = $json->role;

                $sql = "UPDATE overwatch SET ultimateAbility='$ultimate', hero='$hero', role='$role' WHERE id = $x";

                if ($con->query($sql) === TRUE)
                {
                    http_response_code(201);
                    header("HTTP/1.1 201 Created");
                }
                else
                {
                    echo $con->error;
                    header("HTTP/1.1 400 Bad Request");
                }
            }
        }
        else {

            header("HTTP/1.1 415 Unsupported Media Type");
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        break;
    case "DELETE":
        $x = $_GET['id'];
        $sql = "DELETE FROM overwatch WHERE id = $x";

        if($con->query($sql)=== true){
            header("HTTP/1.1 204 No Content");
        }
        else{
            header("HTTPS/1.1 400 Bad Request");
        }


        break;

}

