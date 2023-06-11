<?php

require_once("classes.php");
$error = "";
// check if there's a session userdate
$EM->checkCredentials();

// checks if the HTTP request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = $EM->addDepartment();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin | Add Department</title>
</head>

<body>
    <form method="POST">
        <input type="text" name="department_id" placeholder="Department_ID" />
        <input type="text" name="department_name" placeholder="Department Name" />
        <input type="text" name="department_acronym" placeholder="Acronym" />
        <input type="text" name="manager" placeholder="Manager" />
        <button type="submit">Add Department</button>
        <a href="dashboard.php">Back</a>

        <p style="color: red;"><?php echo $error; ?></p>
    </form>
</body>

</html>