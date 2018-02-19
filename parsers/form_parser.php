<?php
if( isset($_POST["fname"], $_POST["lname"], $_POST["email"], $_POST["message"]) &&
    !isEmpty($_POST["fname"], $_POST["lname"], $_POST["email"], $_POST["message"])) {

    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        die("That is not a valid email address.");
    }

    $sqlFields = [
        "fname" => $_POST["fname"],
        "lname" => $_POST["lname"],
        "email" => $_POST["email"],
        "message" => $_POST["message"],
        "ip" => getRealIpAddr()
    ];

    $sql = "INSERT INTO guestbook VALUES(default, :fname , :lname, :email, NULL , :message, :ip )";

    if(isset($_POST["website"]) && !empty($_POST["website"])) {
        $sqlFields += [ "website" => $_POST["website"] ];
        $sql = "INSERT INTO guestbook VALUES(default, :fname , :lname , :email , :website , :message, :ip )";
    }

    try {
        $db = new PDO("mysql:hostname=localhost;dbname=guestbook", "root", "");

        $statement = $db->prepare($sql);

        if ($statement->execute($sqlFields)) {
            die("success");
        } else {
            die("Entry could not be saved in the database");
        }
    }
    catch (PDOException $e) {
        die($e->getMessage());
    }


} else {
    die("Some required fields where not set or where empty.");
}

/**
 * @param array ...$items the items to check
 * @return bool true if all the items are empty
 */
function isEmpty(...$items) {

    foreach ($items as $item) {
        if(empty($item)) {
            return true;
        }
    }

    return false;
}

/**
 * http://itman.in/en/how-to-get-client-ip-address-in-php/
 */
function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
        $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
        $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}