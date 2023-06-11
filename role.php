<?php

require_once("classes.php");
$error = "";
// check if there's a session userdate
$EM->checkCredentials();

// checks if the HTTP request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = $EM->addRole();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin | Add Role</title>
</head>

<body>
    <form method="POST">
        <input type="text" name="role" placeholder="Role" />
        <input type="text" name="role_acronym" placeholder="Accronym" />
        <button type="submit">Add Role</button>
        <a href="dashboard.php">Back</a>

        <p style="color: red;"><?php echo $error; ?></p>
    </form>
</body>

</html>