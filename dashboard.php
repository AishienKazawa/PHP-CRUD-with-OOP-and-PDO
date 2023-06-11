<?php

require_once("classes.php");
$error = "";

// check if there's a session userdata
$EM->checkCredentials();

// checks if the HTTP request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = $EM->addEmployee();
}

// check if the delete parameter in the url is set as true
if (isset($_GET["delete"])) {
    $EM->deleteEmployee();
}

// check if the logout parameter in the url is set as true
if (isset($_GET["logout"])) {
    $EM->closeConn();

    header("location: index.php");
}

// invoking methods from an instantiated object
$departments = $EM->getDepartments();
$roles = $EM->getRoles();
$employees = $EM->getEmployees();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin | Dashboard</title>

    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
</head>

<body>
    <form method="POST">
        <input type="text" name="firstname" placeholder="First Name" value="<?php echo isset($_POST['firstname']) ? $_POST['firstname'] : ''; ?>" />
        <input type="text" name="lastname" id="" placeholder="Last Name" value="<?php echo isset($_POST['lastname']) ? $_POST['lastname'] : ''; ?>" />
        <input type="text" name="address" id="" placeholder="Address" value="<?php echo isset($_POST['address']) ? $_POST['address'] : ''; ?>" />
        <input type="text" name="contact" id="" placeholder="Contact Number" value="<?php echo isset($_POST['contact']) ? $_POST['contact'] : ''; ?>" />
        <input type="text" name="birthday" id="" placeholder="Birthday" value="<?php echo isset($_POST['birthday']) ? $_POST['birthday'] : ''; ?>" />

        <select name="gender">
            <option value="Gender">Gender</option>
            <option value="Male" <?php echo isset($_POST['gender']) && $_POST['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
            <option value="Female" <?php echo isset($_POST['gender']) && $_POST['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
        </select>

        <select name="role">
            <option value="Role">Role</option>
            <?php foreach ($roles as $role) { ?>
                <option value="<?= $role["role"] ?>" <?php echo isset($_POST['role']) && $_POST['role'] == $role["role"] ? 'selected' : ''; ?>><?php echo $role["role"] ?></option>
            <?php } ?>
        </select>

        <select name="department">
            <option value="Department">Department</option>
            <?php foreach ($departments as $department) { ?>
                <option value="<?= $department["department_name"] ?>" <?php echo isset($_POST['department']) && $_POST['department'] == $department["department_name"] ? 'selected' : ''; ?>><?php echo $department["department_name"] ?></option>
            <?php } ?>
        </select>

        <p style="color: red;"><?php echo $error; ?></p>

        <button type="submit">Add Employee</button>
    </form>

    <!-- table -->

    <table style="width: 100%; margin-top: 2rem;">
        <tr>
            <th>Employee ID</th>
            <th>Employee Name</th>
            <th>Contact Number</th>
            <th>Email Address</th>
            <th>Role</th>
            <th>Department</th>
            <th>Account</th>
            <th>Actions</th>
        </tr>

        <?php if (!empty($employees)) { ?>
            <?php foreach ($employees as $employee) { ?>
                <tr>
                    <td>
                        <?php echo $employee["employee_id"] ?>
                    </td>
                    <td>
                        <?php echo $employee["first_name"] . " " . $employee["last_name"] ?>
                    </td>
                    <td>
                        <?php echo $employee["contact_number"] ?>
                    </td>
                    <td>
                        <?php echo $employee["email"] ?>
                    </td>
                    <td>
                        <?php echo $employee["role"] ?>
                    </td>
                    <td>
                        <?php echo $employee["department"] ?>
                    </td>
                    <td>
                        <?php
                        echo isset($employee["email"]) && !empty($employee["email"]) ? "Account Created" : '<a href="employeeAccount.php?employee_id=' . $employee["employee_id"] . '">Create Account</a>';
                        ?>
                    </td>
                    <td>
                        <a href="edit.php?employee_id=<?= $employee["employee_id"] ?>">edit</a>
                        <a href="dashboard.php?delete=true&employee_id=<?= $employee["employee_id"] ?>">delete</a>
                    </td>
                </tr>
            <?php }  ?>
        <?php } else { ?>
            <p>There's no data</p>
        <?php } ?>

    </table>


    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
        <a href="dashboard.php?logout=true" class="btn btn-danger" role="button">Logout</a>
        <a href="department.php" class="btn btn-danger" role="button">Add Department</a>
        <a href="role.php" class="btn btn-danger" role="button">Add Role</a>
    </div>
</body>

</html>