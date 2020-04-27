<?php
require_once("db/config.php");
// Code for checking username availabilty
if (!empty($_POST["username"])) {
    $uname = $_POST["username"];
    $sql = "SELECT username FROM  userdata WHERE username=:uname";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uname', $uname, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    if ($query->rowCount() > 0) {
        echo "<span style='color:red'> Username already exists.</span>";
    } else {
        echo "<span style='color:green'> Username available for Registration.</span>";
    }
}

// Code for checking email availabilty
if (!empty($_POST["email"])) {
    $email = $_POST["email"];
    $sql = "SELECT email FROM  userdata WHERE email=:email";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    if ($query->rowCount() > 0) {
        echo "<span style='color:red'>Email-id already exists.</span>";
    } else {
        echo "<span style='color:green'>Email-id available for Registration.</span>";
    }
}
