<?php

require_once 'database.php';

$method = $_SERVER["REQUEST_METHOD"];

$accept = $_SERVER["HTTP_ACCEPT"];

$parsedUrl = 'https://stud.hosted.hr.nl/0894594/restful/restfulMusicOpdracht/music';

$data = array();

$startstring = '';
$limitstring = '';
$limit = 0;
$start = 0;

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

if(isset($_GET['limit'])){
    $limit = $_GET['limit'];
    $limitstring = "LIMIT " . $_GET['limit'];
}

if(isset($_GET['start'])){
    $start = $_GET['start'];
    $startstring = "OFFSET " . $_GET['start'];
}

switch($method)
{
    case "OPTIONS":
        header("Allow: GET,POST,OPTIONS");

        break;


    case "GET":
        $items = [];
        if ($accept == "application/json") {
            header("Content-Type: application/json");
                           $sql = "SELECT * FROM overwatch $limitstring $startstring";
                $result = mysqli_query($db, $sql);


                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $item = [
                            "id" => $row["id"],
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
                        array_push($items, $item);

                    }
                    $data["items"] = $items;
                    $links = [
                        [
                            "rel" => "self",
                            "href" => $parsedUrl
                        ]
                    ];
                    $data["links"] = $links;

                    $totalItems = mysqli_num_rows($con->query("SELECT * FROM overwatch "));
                    $currentItems = $result->num_rows;

                    if ($limit > 0) {
                        $totalPages = ceil($totalItems / $limit);
                    } else
                        $totalPages = 1;

                    if ($limit > 0)
                        $currentPage = ceil(($start + 1) / ($limit <= 0 ? 1 : $limit));
                    else
                        $currentPage = 1;

                    $pagination = [
                        "currentPage" => $currentPage,
                        "currentItems" => $currentItems,
                        "totalPages" => $totalPages,
                        "totalItems" => $totalItems,
                        "links" => [
                            [
                                "rel" => "first",
                                "page" => "1",
                                "href" => $parsedUrl . "?limit=$limit" . "&start=1"
                            ],
                            [
                                "rel" => "last",
                                "page" => $totalPages,
                                "href" => $parsedUrl . "?limit=$limit" . "&start=" . ($totalItems - ($totalItems % ($limit <= 0 ? 1 : $limit)))
                            ],
                            [
                                "rel" => "previous",
                                "page" => $totalPages <= 1 ? 1 : $currentPage - 1,
                                "href" => $parsedUrl . "?limit=$limit" . "&start=" . (($start - $limit) <= 0 ? 0 : ($start - $limit))
                            ],
                            [
                                "rel" => "next",
                                "page" => $currentPage = $totalPages ? $currentPage + 1 : $totalPages,
                                "href" => $parsedUrl . "?limit=$limit" . "&start=" . (($start + $limit) >= $totalItems ? $totalItems : ($start + $limit))
                            ],
                        ]
                    ];
                    $data["pagination"] = $pagination;


                }

            echo json_encode($data);
        }
        else if ($accept == "application/xml")
        {
            header("Content-Type: application/xml");
            $sql = "SELECT * FROM overwatch $limitstring $startstring";
            $result = mysqli_query($db, $sql);


            if ($result->num_rows > 0)
            {
                while ($row = $result->fetch_assoc()) {
                    $item =
                        [
                            "id" => $row["id"],
                            "hero" => $row["hero"],
                            "ultimateAbility" => $row["ultimateAbility"],
                            "role" => $row["role"],
                            "links" =>
                                [
                                    [
                                        "rel" => "self", "href" => $parsedUrl . '/' . $row["id"]
                                    ],
                                    [
                                        "rel" => "collection", "href" => $parsedUrl
                                    ]
                                ]
                        ];
                    array_push($items, $item);

                }
                $data["items"] = $items;
                $links = [
                    [
                        "rel" => "self",
                        "href" => $parsedUrl
                    ]
                ];
                $data["links"] = $links;

                $totalItems = mysqli_num_rows($con->query("SELECT * FROM overwatch "));
                $currentItems = $result->num_rows;

                if ($limit > 0) {
                    $totalPages = ceil($totalItems / $limit);
                } else
                    $totalPages = 1;

                if ($limit > 0)
                    $currentPage = ceil(($start + 1) / ($limit <= 0 ? 1 : $limit));
                else
                    $currentPage = 1;

                $pagination = [
                    "currentPage" => $currentPage,
                    "currentItems" => $currentItems,
                    "totalPages" => $totalPages,
                    "totalItems" => $totalItems,
                    "links" => [
                        [
                            "rel" => "first",
                            "page" => "1",
                            "href" => $parsedUrl . "?limit=$limit" . "&start=1"
                        ],
                        [
                            "rel" => "last",
                            "page" => $totalPages,
                            "href" => $parsedUrl . "?limit=$limit" . "&start=" . ($totalItems - ($totalItems % ($limit <= 0 ? 1 : $limit)))
                        ],
                        [
                            "rel" => "previous",
                            "page" => $totalPages <= 1 ? 1 : $currentPage - 1,
                            "href" => $parsedUrl . "?limit=$limit" . "&start=" . (($start - $limit) <= 0 ? 0 : ($start - $limit))
                        ],
                        [
                            "rel" => "next",
                            "page" => $currentPage = $totalPages ? $currentPage + 1 : $totalPages,
                            "href" => $parsedUrl . "?limit=$limit" . "&start=" . (($start + $limit) >= $totalItems ? $totalItems : ($start + $limit))
                        ],
                    ]
                ];
                $data["pagination"] = $pagination;

                // create new instance of simplexml
                $xml = new SimpleXMLElement('<root/>');

                // function callback
                array2XML($xml, $data);

                // save as xml file
                echo $xml->asXML();
            }
        }
        else {
            http_response_code(400);
        }

        break;

    case "POST":

        $contentType = $_SERVER["CONTENT_TYPE"];
        if ($contentType == "application/json") {
            $body = file_get_contents("php://input");
            $json = json_decode($body);

            if (isset($json->ultimate) && isset($json->artiest) && isset($json->album) ) {
                $ultimate = $json->ultimate;
                $hero = $json->hero;
                $role = $json->role;
                $sql = "INSERT INTO `0894594`.`overwatch` (`id`, `hero`, `ultimateAbility`, `role`) VALUES (NULL, '$ultimate', '$hero', '$role');";
                if ($con->query($sql) === TRUE) {
                    header("HTTP/1.1 201 Created");
                    echo "Hoera";
                }
                else {
                    header("HTTP/1.1 500 Internal Server Error");
                    echo "Error: " . $sql . "<br>" . $con->error;
                }
            }
            else {
                header("HTTP/1.1 400 Bad Request");
                echo "Please post a number, artist, album ";
            }
        }
        else if ($contentType == "application/x-www-form-urlencoded") {
            if (isset($_POST["ultimate"]) && isset($_POST["hero"]) && isset($_POST["role"])) {
                $ultimate = $json->ultimate;
                $hero = $json->hero;
                $role = $json->role;

                $sql = "INSERT INTO `0894594`.`overwatch` (`id`, `hero`, `ultimateAbility`, `role`) VALUES (NULL, '$ultimate', '$hero', '$role');";
                if ($con->query($sql) === TRUE) {
                    header("HTTP/1.1 201 Created");
                    echo "Hoera";
                }
                else {
                    header("HTTP/1.1 500 Internal Server Error");
                    echo "Error: " . $sql . "<br>" . $con->error;
                }
            }
            else {
                header("HTTP/1.1 400 Bad Request");
                echo "Please post a name, artist, album and albumArt";
            }
        }
        else {
            header("HTTP/1.1 400 Bad Request");
            echo "Content type not allowed. Send json or x-www-form-urlencoded data.";
        }

        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");

        break;


}



