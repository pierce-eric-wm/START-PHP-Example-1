<?php
//Connect to the DB
require_once('src/connect.php');

//Initialize variables to default values
$error = false;
$success = false;

//Be sure they are passing an ID
if(!$_GET['id']){
    /**
     * A die() will kill the execution of the program in place. Nothing
     * beyond this code will be executed.
     */
    die('An ID must be passed');
}

/**
 * Check for an edit. The @ symbol tells PHP to supress any error
 * messages if that array property doesn't edit since it won't
 * exist when just loading the page.
 */
if(@$_POST['editUser']){
    /**
     * Input validation ensures that the variables you use later
     * have correct values. This is a very basic example and by far
     * not best practice but it's close enough to get the idea.
     */
    if(!$_POST['email']){
        $error = 'Email is a required field. <br />';
    }

    if(!$_POST['name']){
        $error .= 'Name is a required field. <br />';
    }

    if(!$_GET['id']){
        $error .= 'ID is a required field.<br />';
    }

    /**
     * Using the UPDATE MySQL syntax. When updating you ALWAYS want to have a WHERE
     * condition. It's usually a unique identifier like the primary key. It's also a good
     * idea to use the LIMIT especially when you only mean to update a single record.
     * This prevents accidents or hack attempts from affecting more rows than you want.
     */
    $stmt = $dbh->prepare('UPDATE users set name = :name, email = :email WHERE id = :id LIMIT 1');
    $result = $stmt->execute(array('id'=>$_POST['id'], 'name'=>$_POST['name'], 'email'=>$_POST['email'], 'id'=>$_GET['id']));

    //Result will be TRUE if the update was successful
    if($result){
        $success = 'Record successfully updated!';
    }else{
        $error = 'There was a problem updating the record: ' . $stmt->errorInfo()[2];
    }
}

//Pull the record AFTER any possible updates!
$stmt = $dbh->prepare('SELECT * FROM users WHERE id=:id');
$result = $stmt->execute(array('id'=>$_GET['id']));

//If there is an error code, set the $error variable to the message.
if($result){
    $error .= $stmt->errorInfo()[2];
}

$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
</head>

<body>
    <h1>Edit User - <?php echo $user['name']?></h1>
    <div style="color: green;">
        <?php
        if($success){
            echo $success;
            echo '<br /></br />';
        }
        ?>
    </div>

    <div style="color: red;">
        <?php
        if($error){
            echo $error;
            echo '<br /><br />';
        }
        ?>
    </div>

    <form action="" method="post">
        <input name="name" placeholder="Name" value="<?php echo $user['name']?>"/>
        <input name="email" placeholder="Email" value="<?php echo $user['email']?>"/>
        <button type="submit" name="editUser" value="1">Save Changes</button>
        <button type="button" onclick="Javascript:window.location = 'index.php'">Go Back</button>
    </form>
</body>
</html>