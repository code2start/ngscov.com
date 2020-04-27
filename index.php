<?php
session_start();
//Database Configuration File
include('db/config.php');
error_reporting(0);

if (isset($_POST['login'])) {

    //Genrating random number for salt
    if (@$_SESSION['randnmbr'] == "") {

        $Alpha22 = range("A", "Z");
        $Alpha12 = range("A", "Z");
        $alpha22 = range("a", "z");
        $alpha12 = range("a", "z");
        $num22 = range(1000, 9999);
        $num12 = range(1000, 9999);
        $numU22 = range(99999, 10000);
        $numU12 = range(99999, 10000);
        $AlphaB22 = array_rand($Alpha22);
        $AlphaB12 = array_rand($Alpha12);
        $alphaS22 = array_rand($alpha22);
        $alphaS12 = array_rand($alpha12);
        $Num22 = array_rand($num22);
        $NumU22 = array_rand($numU22);
        $Num12 = array_rand($num12);
        $NumU12 = array_rand($numU12);
        $res22 = $Alpha22[$AlphaB22] . $num22[$Num22] . $Alpha12[$AlphaB12] . $numU22[$NumU22] . $alpha22[$alphaS22] . $num12[$Num12];
        $text22 = str_shuffle($res22);
        $_SESSION['randnum'] = $text22;
    }


    // Getting username/ email and password
    $uname = $_POST['username'];
    $password = hash('sha256', $_POST['password']);

    // Hashing with Random Number
    $saltedpasswrd = hash('sha256', $password . $_SESSION['randnum']);
    // Fetch stored password  from database on the basis of username/email 
    $sql = "SELECT username,email,password FROM userdata WHERE (username=:usname || email=:usname)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':usname', $uname, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
            $fetchpassword = $result->password;
            // hashing for stored password
            $storedpass = hash('sha256', $fetchpassword . $_SESSION['randnum']);
        }
        //You can configure your cost value according to your server configuration.By Default value is 10.
        $options = [
            'cost' => 12,
        ];
        // Hashing of the post password
        $hash = password_hash($saltedpasswrd, PASSWORD_DEFAULT, $options);
        // Verifying Post password againt stored password
        if (password_verify($storedpass, $hash)) {


            $_SESSION['userlogin'] = $_POST['username'];
            echo "<script type='text/javascript'> document.location = 'home.php'; </script>";
        } else {
            echo "<script>alert('Wrong password');</script>";
        }
    } else {
        echo "<script>alert('Invalid Details');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>NGSCOV | Login Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="js/jquery-3.5.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>

<body>


    <div class="container h-100">

        <div class="row h-100 justify-content-center align-items-center">

            <form id="loginForm" method="post">
                <h1 class="text-center">NGSCOV</h1>
                <br>
                <div class="form-group">
                    <label for="username" class="control-label">Username / Email</label>
                    <input type="text" class="form-control" id="username" name="username" required="" title="Please enter you username or Email-id" placeholder="email or username">
                    <span class="help-block"></span>
                </div>
                <div class="form-group">
                    <label for="password" class="control-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="" required="" title="Please enter your password">
                    <span class="help-block"></span>
                </div>

                <button type="submit" class="btn btn-success btn-block" name="login">Login</button>
            </form>

        </div>
    </div>
</body>

</html>