<?php
//Connect to the DB
require_once('src/connect.php');

//Initialize variables to default values
$error = false;
$success = false;
$user = array();

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
        $error = '<p>Email is a required field.</p>';
    }

    if(!$_POST['name']){
        $error .= '<p>Name is a required field.</p>';
    }

    if(!$_GET['id']){
        $error .= '<p>ID is a required field.</p>';
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
        $success .= '<p>Record successfully updated!</p>';
    }else{
        $error .= '<p>There was a problem updating the record</p>';
    }
}

//Pull the record AFTER any possible updates!
$stmt = $dbh->prepare('SELECT * FROM users WHERE id=:id');
$result = $stmt->execute(array('id'=>$_GET['id']));

//$result will be true if all went well.
if(!$result){
    $error .= '<p>There was an error processing your request</p>';
}else {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
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
        }
        ?>
    </div>

    <div style="color: red;">
        <?php
        if($error){
            echo $error;
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