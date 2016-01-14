<?php
//Connect to our database
require_once('src/connect.php');

//Initialize our variables with default values.
$error = false;
$success = false;
$users = array();

//Check for the addUser in the $_POST super global which means someone submitted the form.
if(@$_POST['addUser']){
    /**
     * New user was submitted. Make sure name and email are present!
     */
    if(!$_POST['email']){
        $error .= '<p>Email is a required field!</p>';
    }

    if(!$_POST['name']){
        $error .= '<p>Name is a required field!</p>';
    }

    /**
     * If we're here...all is well. Process the insert
     */
    $stmt = $dbh->prepare('INSERT INTO users (name, email) VALUES (:name, :email)');
    $result = $stmt->execute(
        array(
            'name'=>$_POST['name'],
            'email'=>$_POST['email']
        )
    );

    //Always check the result for errors!
    if($result){
        $success .= "<p>User " . $_POST['email'] . " was successfully saved.</p>";
    }else{
        $error .= "<p>There was an error saving " . $_POST['email'] . '</p>';
    }
}

/**
 * We'll always want to pull the users to show them in the table
 */
$stmt = $dbh->prepare('SELECT * FROM users');
$result = $stmt->execute();

if(!$result){
    $error .= '<p>There was an error processing your request.</p>';
}else {
    $users = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add User</title>
</head>

<body>
    <div style="color: red;">
        <?php
        if($error){
            echo $error;
        }
        ?>
    </div>

    <div style="color: green;">
        <?php
        if($success){
            echo $success;
        }
        ?>
    </div>

    <h1>Add User</h1>

<!--    Make sure the name attribute of your form elements matches the $_POST array elements. Case matters!-->
    <form name="addUser" method="post">
        <input name="name" placeholder="Your Name" />
        <input name="email" placeholder="Your Email" />
        <button type="submit" name="addUser" value="1">Add New User</button>
    </form>

    <h1>Existing Users</h1>

    <?php
    if($users && count($users)){
    ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($users as $user){
                ?>
                <tr>
                    <td><?php echo $user['name']?></td>
                    <td><?php echo $user['email']?></td>
                    <td><a href="edit.php?id=<?php echo $user['id'];?>">Edit</a></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
    }else{
        echo "There are no users in this system.";
    }
    ?>

</body>

</html>