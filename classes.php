<?php

require_once("config.php");

class Database_connection
{
    private $host;
    private $username;
    private $password;
    private $database;
    protected $conn;


    public function __construct($host, $username, $password, $database)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    // connection to the database
    public function openConn()
    {
        try {
            // 
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->database", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {

            // Throw a custom exception
            throw new Exception('Unable to connect to the database. ' . $e->getMessage());
        }
    }

    // close connection to the database
    public function closeConn()
    {

        // starting the session if it hasn't already been started
        session_status() === PHP_SESSION_NONE && session_start();

        // clearing any user data stored in the session
        $_SESSION["userdata"] = null;

        // removes the "userdata" key from the $_SESSION
        unset($_SESSION["userdata"]);

        // close the database
        $this->conn = null;
    }
}

class Emp_management extends Database_connection
{
    // prepare sql query and execute
    public function prepareQuery($sql, $params = [])
    {

        try {

            // establish a connection to the database
            $this->openConn();
            // prepares the query for execution
            $stmt = $this->conn->prepare($sql);
            // executes the prepared query
            $stmt->execute($params);
            // retrieves all rows of the query result as an associative array and return the fetched data
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            // catches the exception and retrieves the error message
            echo "Query failed: " . $e->getMessage();
            // indicating that the query execution failed
            return false;
        }
    }

    // sign in
    public function signIn()
    {
        // establish a connection to the database before executing the SQL query
        $this->openConn();

        // superglobal variable is used to retrieve the values submitted via a form with the HTTP POST method
        $adminID = $_POST["adminId"];
        $password = $_POST["password"];

        try {
            // check if the values are empty
            if (empty($adminID) || empty($password)) {
                return "Please fill in all fields.";
            }

            // sql query
            $sql = "SELECT * FROM admin_accounts WHERE admin_id = :adminID";

            // prepare the sql statement
            $stmt = $this->conn->prepare($sql);
            // executes the prepared statement with the provided values
            $stmt->execute([":adminID" => $adminID]);
            // get the row count
            $rowCount = $stmt->rowCount();

            // check if the result is not empty
            if ($rowCount > 0) {
                // retrieve result
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // get the password based on the row data
                $currentPassword = $row["password"];

                // check if the provided password is matched in the database
                if (password_verify($password, $currentPassword)) {

                    // call function
                    $this->setUserdata($row);

                    header("Location: dashboard.php");
                } else {
                    return "Invalid password";
                }
            } else {
                return "Invalid credentials pls check your email and password!";
            }
        } catch (PDOException $e) {

            // Throw a custom exception
            throw new Exception('An error occurred during sign-in ' . $e->getMessage());
        }
    }

    // check if there's no session userdata
    public function checkCredentials()
    {

        // return if there's a session data or null
        $userdata = $this->getUserdata();

        // check if null
        if (!$userdata) {
            header("Location: index.php");
            exit();
        }
    }

    // set and get session data
    public function setUserdata($row)
    {
        // starting the session if it hasn't already been started
        session_status() === PHP_SESSION_NONE && session_start();

        // store the row data in SESSION array
        $_SESSION["userdata"] = array("admin_id" => $row["admin_id"]);

        // return session with data
        return $_SESSION["userdata"];
    }

    public function getUserdata()
    {
        // start session
        session_start();

        // check if the SESSION has value
        if (isset($_SESSION["userdata"])) {

            // return session with data
            return $_SESSION["userdata"];
        } else {

            // return session as null
            return null;
        }
    }

    // add employee
    public function addEmployee()
    {
        // establish a connection to the database before executing the SQL query
        $this->openConn();


        // superglobal variable is used to retrieve the values submitted via a form with the HTTP POST method
        $employee_id = date("Y") . "-" . rand(100000, 999999);
        $firstname = $_POST["firstname"];
        $lastname = $_POST["lastname"];
        $address = $_POST["address"];
        $gender = $_POST["gender"];
        $birthday = $_POST["birthday"];
        $contact = $_POST["contact"];
        $role = $_POST["role"];
        $department = $_POST["department"];

        // check if all fields are empty
        if (
            empty($firstname) || empty($lastname) || empty($address) || $gender == "Gender" ||
            empty($birthday) || empty($contact) || $role == "Role" || $department == "Department"
        ) {
            return "Please fill in all fields.";
        }

        try {

            //sql query
            $sql = "INSERT INTO employee_details (`employee_id`, `first_name`, `last_name`, `address`, `gender`, `birthday`, `contact_number`, `role`, `department`) VALUES(:employee_id, :first_name, :last_name, :address, :gender, :birthday, :contact_number, :role, :department)";

            // prepare the sql statement
            $stmt = $this->conn->prepare($sql);
            // executes the prepared statement with the provided values
            $stmt->execute([":employee_id" => $employee_id, ":first_name" => $firstname, ":last_name" => $lastname, ":address" => $address, ":gender" => $gender, ":birthday" => $birthday, ":contact_number" => $contact, ":role" => $role, ":department" => $department]);

            header("Location: dashboard.php");
        } catch (PDOException $e) {

            // Throw a custom exception
            throw new Exception('An error occurred during adding new employee. ' . $e->getMessage());
        }
    }

    // add Role
    public function addRole()
    {

        // establish a connection to the database before executing the SQL query
        $this->openConn();

        // superglobal variable is used to retrieve the values submitted via a form with the HTTP POST method
        $role = $_POST["role"];
        $role_acronym = $_POST["role_acronym"];

        // check if all fields are empty
        if (empty($role) || empty($role_acronym)) {

            return "Please fill the email field";
        }

        try {

            //sql query
            $sql = "INSERT INTO role (`role`, `acronym`) VALUES(:role, :acronym)";

            // prepare the sql statement
            $stmt = $this->conn->prepare($sql);
            // executes the prepared statement with the provided values
            $stmt->execute([":role" => $role, ":acronym" => $role_acronym]);

            header("Location: dashboard.php");
        } catch (PDOException $e) {

            // Throw a custom exception
            throw new Exception('An error occurred during adding new role. ' . $e->getMessage());
        }
    }

    // fetch role
    public function getRoles()
    {

        // establish a connection to the database before executing the SQL query
        $this->openConn();

        try {

            //sql query
            $sql = "SELECT * FROM role";

            // prepare the sql statement
            $stmt = $this->conn->prepare($sql);
            // executes the prepared statement with the provided values
            $stmt->execute();
            // get the row count
            $rowCount = $stmt->rowCount();


            // check if the result is not empty
            if ($rowCount > 0) {
                // retrieve result
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

                return $row;
            } else {
                return "There's no data";
            }
        } catch (PDOException $e) {

            // Throw a custom exception
            throw new Exception('An error occurred during fetching all the roles. ' . $e->getMessage());
        }
    }

    // add Department
    public function addDepartment()
    {

        // establish a connection to the database before executing the SQL query
        $this->openConn();

        // superglobal variable is used to retrieve the values submitted via a form with the HTTP POST method
        $department_id = $_POST["department_id"];
        $department_name = $_POST["department_name"];
        $department_acronym = $_POST["department_acronym"];
        $manager = $_POST["manager"];

        // check if all fields are empty
        if (empty($department_id) || empty($department_name) || empty($department_acronym) || empty($manager)) {

            return "Please fill the email field";
        }

        try {

            //sql query
            $sql = "INSERT INTO department (`department_id`, `department_name`, `acronym`, `manager`) VALUES(:department_id, :department_name, :acronym, :manager)";

            // prepare the sql statement
            $stmt = $this->conn->prepare($sql);
            // executes the prepared statement with the provided values
            $stmt->execute([":department_id" => $department_id, ":department_name" => $department_name, ":acronym" => $department_name, ":manager" => $manager]);

            header("Location: dashboard.php");
        } catch (PDOException $e) {

            // Throw a custom exception
            throw new Exception('An error occurred during adding new department. ' . $e->getMessage());
        }
    }

    // fetch department
    public function getDepartments()
    {

        // establish a connection to the database before executing the SQL query
        $this->openConn();

        try {

            //sql query
            $sql = "SELECT * FROM department";

            // prepare the sql statement
            $stmt = $this->conn->prepare($sql);
            // executes the prepared statement with the provided values
            $stmt->execute();
            // get the row count
            $rowCount = $stmt->rowCount();

            // check if the result is not empty
            if ($rowCount > 0) {
                // retrieve result
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

                return $row;
            } else {
                return "There's no data";
            }
        } catch (PDOException $e) {

            // Throw a custom exception
            throw new Exception('An error occurred during fetching all departments. ' . $e->getMessage());
        }
    }

    //fetch employees
    public function getEmployees()
    {

        // establish a connection to the database before executing the SQL query
        $this->openConn();

        try {

            //sql query
            $sql = "SELECT ed.*, ea.email FROM employee_accounts AS ea RIGHT JOIN employee_details AS ed ON ea.employee_id = ed.employee_id";

            // prepare the sql statement
            $stmt = $this->conn->prepare($sql);
            // executes the prepared statement with the provided values
            $stmt->execute();
            // get the row count
            $rowCount = $stmt->rowCount();

            // check if the result is not empty
            if ($rowCount > 0) {
                // retrieve result
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

                return $row;
            } else {
                return [];
            }
        } catch (PDOException $e) {

            // Throw a custom exception
            throw new Exception('An error occurred during fetching all employees. ' . $e->getMessage());
        }
    }

    // create account for employees
    public function createEmployeeAccount()
    {

        // establish a connection to the database before executing the SQL query
        $this->openConn();

        // superglobal variable is used to retrieve the values submitted via a form with the HTTP POST method
        $password = password_hash("admin", PASSWORD_DEFAULT);
        $employee_id = $_GET["employee_id"];
        $email = $_POST["email"];

        // check if the email is empty
        if (empty($email)) {
            return "Please fill the email field";
        }

        // Validate email
        $validatedEmail = filter_var($email, FILTER_VALIDATE_EMAIL);

        if (!$validatedEmail) {
            return "Invalid email format";
        }

        try {

            //sql query
            $sql = "INSERT INTO employee_accounts (`employee_id`, `email`, `password`) VALUES(:employee_id, :email, :password)";

            // prepare the sql statement
            $stmt = $this->conn->prepare($sql);
            // executes the prepared statement with the provided values
            $stmt->execute([":employee_id" => $employee_id, ":email" => $email, ":password" => $password]);

            header("Location: dashboard.php");
        } catch (PDOException $e) {

            // Throw a custom exception
            throw new Exception('An error occurred during creating employee account. ' . $e->getMessage());
        }
    }

    // get employee details
    public function getEmployeeDetails()
    {

        // establish a connection to the database before executing the SQL query
        $this->openConn();

        $employee_id = $_GET["employee_id"];

        try {

            //sql query
            $sql = "SELECT ed.*, ea.email FROM employee_accounts AS ea RIGHT JOIN employee_details AS ed ON ea.employee_id = ed.employee_id WHERE ed.employee_id = :employee_id";

            // prepare the sql statement
            $stmt = $this->conn->prepare($sql);
            // executes the prepared statement with the provided values
            $stmt->execute(["employee_id" => $employee_id]);
            // get the row count
            $rowCount = $stmt->rowCount();

            // check if the result is not empty
            if ($rowCount > 0) {
                // retrieve result
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

                return $row;
            } else {
                return "There's no data";
            }
        } catch (PDOException $e) {

            // Throw a custom exception
            throw new Exception('An error occurred during fetching employee details. ' . $e->getMessage());
        }
    }

    // edit employee details
    public function editEmployee()
    {

        // establish a connection to the database before executing the SQL query
        $this->openConn();

        // superglobal variable is used to retrieve the values submitted via a form with the HTTP POST method
        $employee_id = $_GET["employee_id"];
        $firstname = $_POST["firstname"];
        $lastname = $_POST["lastname"];
        $address = $_POST["address"];
        $gender = $_POST["gender"];
        $birthday = $_POST["birthday"];
        $email = $_POST["email"];
        $contact = $_POST["contact"];
        $role = $_POST["role"];
        $department = $_POST["department"];

        // check if all fields are empty
        if (
            empty($firstname) || empty($lastname) || empty($address) || $gender == "Gender" ||
            empty($birthday) || empty($contact) || $role == "Role" || $department == "Department"
        ) {
            return "Please fill in all fields.";
        }

        try {

            //sql query
            $sql = "UPDATE employee_accounts AS ea RIGHT JOIN employee_details AS ed ON ea.employee_id = ed.employee_id SET ed.first_name = :first_name, ed.last_name = :last_name, ed.address = :address, ed.gender = :gender, ed.birthday = :birthday, ed.contact_number = :contact_number, ed.role = :role, ed.department = :department, ea.email = :email WHERE ed.employee_id = :employee_id";

            // prepare the sql statement
            $stmt = $this->conn->prepare($sql);
            // executes the prepared statement with the provided values
            $stmt->execute([":first_name" => $firstname, ":last_name" => $lastname, ":address" => $address, ":gender" => $gender, ":birthday" => $birthday, ":contact_number" => $contact, ":role" => $role, ":department" => $department, "email" => $email, "employee_id" => $employee_id]);

            header("Location: dashboard.php");
        } catch (PDOException $e) {

            // Throw a custom exception
            throw new Exception('An error occurred during updating employee details. ' . $e->getMessage());
        }
    }

    // delete employee
    public function deleteEmployee()
    {
        // establish a connection to the database before executing the SQL query
        $this->openConn();

        $employee_id = $_GET["employee_id"];

        try {

            // sql query
            $sql = "DELETE FROM employee_details WHERE employee_id = :employee_id";

            // prepare the sql statement
            $stmt = $this->conn->prepare($sql);
            // executes the prepared statement with the provided values
            $stmt->execute([":employee_id" => $employee_id]);

            header("Location: dashboard.php");
        } catch (PDOException $e) {

            // Throw a custom exception
            throw new Exception('An error occurred during deleting employee data. ' . $e->getMessage());
        }
    }
}

// create an instance of the Emp_management class
$EM = new Emp_management(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
