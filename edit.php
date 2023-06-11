<?php

require_once("classes.php");
$error = "";
// check if there's a session userdate
$EM->checkCredentials();

// checks if the HTTP request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = $EM->editEmployee();
}

// invoking methods from an instantiated object
$employees = $EM->getEmployeeDetails();
$roles = $EM->getRoles();
$departments = $EM->getDepartments();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin | Edit Employee</title>
</head>

<body>

    <form method="POST">
        <input type="text" name="employee_id" value="<?= $employees[0]["employee_id"] ?>" placeholder="Employee ID" disabled />
        <input type="text" name="firstname" value="<?= isset($_POST['firstname']) ? $_POST['firstname'] : $employees[0]["first_name"] ?>" placeholder="First Name" />
        <input type="text" name="lastname" value="<?= isset($_POST['last_name']) ? $_POST['last_name'] : $employees[0]["last_name"] ?>" placeholder="Last Name" />
        <input type="text" name="address" value="<?= isset($_POST['address']) ? $_POST['address'] : $employees[0]["address"] ?>" placeholder="Address" />
        <input type="text" name="email" value="<?= isset($employees[0]['email']) ? (isset($_POST["email"]) ? $_POST['email'] : $employees[0]['email']) : 'No Account'; ?>" placeholder="Email Address" <?php echo isset($employees[0]['email']) ? '' : 'disabled'; ?> />


        <input type="text" name="contact" value="<?= $employees[0]["contact_number"] ?>" placeholder="Contact Number" />
        <input type="text" name="birthday" value="<?= $employees[0]["birthday"] ?>" placeholder="Birthday" />

        <select name="gender">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <select name="role">
            <?php foreach ($roles as $role) { ?>
                <option value="<?= $role["role"] ?>" <?php if ($employees[0]["role"] == $role["role"])
                                                            echo "selected" ?>>
                    <?php echo $role["role"] ?>
                </option>
            <?php } ?>
        </select>

        <select name="department">
            <?php foreach ($departments as $department) { ?>
                <option value="<?= $department["department_name"] ?>" <?php if ($employees[0]["department"] == $department["department_name"])
                                                                            echo "selected" ?>><?php echo $department["department_name"] ?>
                </option>
            <?php } ?>
        </select>

        <button type="submit">Edit Employee</button>
        <a href="dashboard.php">Back</a>

        <p style="color: red;"><?php echo $error; ?></p>
    </form>
</body>

</html>