<?php

require_once("classes.php");
$error = "";

// checks if the HTTP request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = $EM->signIn();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin | Sign In Form</title>
</head>

<body>
    <form method="POST" style="display:grid; width: 30rem; gap: 1rem;">
        <input type="text" name="adminId" placeholder="Admin ID" />
        <input type="text" name="password" placeholder="Password" />

        <p style="color: red;"><?php echo $error; ?></p>

        <button type="submit">SignIn</button>
    </form>
</body>

</html>