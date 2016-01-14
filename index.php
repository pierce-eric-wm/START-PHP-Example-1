<?php
//This will show any PHP errors. It is turned off by default for security reasons.
ini_set('display_errors', 1);

require_once('src/connect.php');
$error = false;
$success = false;

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

    if($result){
        $success .= "User " . $_POST['email'] . " was successfully saved.";
    }else{
        $error .= "There was an error saving " . $_POST['email'];
    }
}

/**
 * We'll always want to pull the users to show them in the table
 */
$stmt = $dbh->prepare('SELECT * FROM users');
$stmt->execute();
$users = $stmt->fetchAll();
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
            echo '<br /><br />';
        }
        ?>
    </div>

    <div style="color: green;">
        <?php
        if($success){
            echo $success;
            echo '<br /><br />';
        }
        ?>
    </div>

    <h1>Add User</h1>

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