<?php
//Database Configuration File
include('db/config.php');
error_reporting(0);
if (isset($_POST['signup'])) {
    //Getting Post Values
    $fullname = $_POST['fname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $mobile = $_POST['mobilenumber'];
    $password = $_POST['password'];
    $hasedpassword = hash('sha256', $password);
    //$hasedpassword = md5($password);
    // Query for validation of username and email-id
    $ret = "SELECT * FROM userdata where (username=:uname ||  email=:uemail)";
    $queryt = $dbh->prepare($ret);
    $queryt->bindParam(':uemail', $email, PDO::PARAM_STR);
    $queryt->bindParam(':uname', $username, PDO::PARAM_STR);
    $queryt->execute();
    $results = $queryt->fetchAll(PDO::FETCH_OBJ);
    if ($queryt->rowCount() == 0) {
        // Query for Insertion
        $sql = "INSERT INTO userdata(fullname,username,email,mobile,password)
         VALUES(:fname,:uname,:uemail,:umobile,:upassword)";
        $query = $dbh->prepare($sql);
        // Binding Post Values
        $query->bindParam(':fname', $fullname, PDO::PARAM_STR);
        $query->bindParam(':uname', $username, PDO::PARAM_STR);
        $query->bindParam(':uemail', $email, PDO::PARAM_STR);
        $query->bindParam(':umobile', $mobile, PDO::PARAM_INT);
        $query->bindParam(':upassword', $hasedpassword, PDO::PARAM_STR);
        $query->execute();
        $lastInsertId = $dbh->lastInsertId();
        if ($lastInsertId) {
            $msg = "You have signup  Scuccessfully";
        } else {
            $error = "Something went wrong.Please try again";
        }
    } else {
        $error = "Username or Email-id already exist. Please try again";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>PDO | Registration Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--  -->
    <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
    <script src="js/jquery-3.5.0.min.js"></script>
    <script src="css/bootstrap.min.css"></script>
    <link rel="stylesheet" href="css/style.css">
    <!--Javascript for check username availability-->
    <script>
        function checkUsernameAvailability() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "check_availability.php",
                data: 'username=' + $("#username").val(),
                type: "POST",
                success: function(data) {
                    $("#username-availability-status").html(data);
                    $("#loaderIcon").hide();
                },
                error: function() {}
            });
        }
    </script>

    <!--Javascript for check email availability-->
    <script>
        function checkEmailAvailability() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "check_availability.php",
                data: 'email=' + $("#email").val(),
                type: "POST",
                success: function(data) {

                    $("#email-availability-status").html(data);
                    $("#loaderIcon").hide();
                },
                error: function() {
                    event.preventDefault();
                }
            });
        }
    </script>


</head>

<body>
    <form class="form-horizontal" action='' method="post">
        <fieldset>
            <div id="legend" style="padding-left:4%">
                <legend class="">Register | <a href="index.php">Sign in</a></legend>
            </div>
            <!--Error Message-->
            <?php if ($error) { ?><div class="errorWrap">
                    <strong>Error </strong> : <?php echo htmlentities($error); ?></div>
            <?php } ?>
            <!--Success Message-->
            <?php if ($msg) { ?><div class="succWrap">
                    <strong>Well Done </strong> : <?php echo htmlentities($msg); ?></div>
            <?php } ?>




            <div class="control-group">
                <!-- Full name -->
                <label class="control-label" for="fullname">Full Name</label>
                <div class="controls">
                    <input type="text" id="fname" name="fname" pattern="[a-zA-Z\s]+" title="Full name must contain letters only" class="input-xlarge" required>
                    <p class="help-block">Full can contain any letters only</p>
                </div>
            </div>


            <div class="control-group">
                <!-- Username -->
                <label class="control-label" for="username">Username</label>
                <div class="controls">
                    <input type="text" id="username" name="username" onBlur="checkUsernameAvailability()" pattern="^[a-zA-Z][a-zA-Z0-9-_.]{5,12}$" title="User must be alphanumeric without spaces 6 to 12 chars" class="input-xlarge" required>
                    <span id="username-availability-status" style="font-size:12px;"></span>
                    <p class="help-block">Username can contain any letters or numbers, without spaces 6 to 12 chars </p>
                </div>
            </div>

            <div class="control-group">
                <!-- E-mail -->
                <label class="control-label" for="email">E-mail</label>
                <div class="controls">
                    <input type="email" id="email" name="email" placeholder="" onBlur="checkEmailAvailability()" class="input-xlarge" required>
                    <span id="email-availability-status" style="font-size:12px;"></span>
                    <p class="help-block">Please provide your E-mail</p>
                </div>
            </div>

            <div class="control-group">
                <!-- Mobile Number -->
                <label class="control-label" for="mobilenumber">Mobile Number </label>
                <div class="controls">
                    <input type="text" id="mobilenumber" name="mobilenumber" pattern="[0-9]{10}" maxlength="10" title="10 numeric digits only" class="input-xlarge" required>
                    <p class="help-block">Mobile Number Contain only 10 digit numeric values</p>
                </div>
            </div>


            <div class="control-group">
                <!-- Password-->
                <label class="control-label" for="password">Password</label>
                <div class="controls">
                    <input type="password" id="password" name="password" pattern="^\S{4,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Must have at least 4 characters' : ''); if(this.checkValidity()) form.password_two.pattern = this.value;" required class="input-xlarge">
                    <p class="help-block">Password should be at least 4 characters</p>
                </div>
            </div>

            <div class="control-group">
                <!-- Confirm Password -->
                <label class="control-label" for="password_confirm">Password (Confirm)</label>
                <div class="controls">
                    <input type="password" id="password_confirm" name="password_confirm" pattern="^\S{4,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Please enter the same Password as above' : '')""  class=" input-xlarge">
                    <p class="help-block">Please confirm password</p>
                </div>
            </div>




            <div class="control-group">
                <!-- Button -->
                <div class="controls">
                    <button class="btn btn-success" type="submit" name="signup">Signup </button>

                </div>
            </div>
        </fieldset>
    </form>
    <script type="text/javascript">

    </script>
</body>

</html>