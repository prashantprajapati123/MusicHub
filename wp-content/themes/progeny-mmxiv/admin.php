<?php
include'connection.php';
  


    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

        if(isset($_POST['login']))
        {
             $sql ="SELECT * FROM `users` WHERE `username` = '$username' and `password`='$password'"; 


             $result=mysqli_query($conn, $sql);



             if(mysqli_num_rows($result)>0)
             {
                while($row=mysqli_fetch_assoc($result))
                {
                    $_SESSION['id']=$row['school_id'];
                    $_SESSION['id']==TRUE;
                    header('location:userprofile.php');
                }
                
             }

        }
    // }

    

?>
<!DOCTYPE html>
<html>

<head>
    <title>login page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row head">
            <div class="col-sm-2">
                  <a style="color:#fff; margin-top:25px" class="navbar-brand" href="http://www.ssappsnwebs.com/spass/admin2/dashboard"><span style="font-size: 25px;">SPASS</span></a>
            </div>
            <div class="col-sm-10 heading">Login</div>
        </div>
        <div class="row nv">
            <div class="col-sm-12 white-border-box"></div>
        </div>
        <div class="row">
            <div class="col-sm-offset-3 col-sm-6">
                <div class="row msg">
                    <img src="http://www.ssappsnwebs.com/spass/admin2//assets/images/imgpsh_fullsize.png" >
                </div>
                <div>
                    <form action="" method="post">
                        <div class="form-group">
                          <label for="username">Username:</label>
                          <input type="username" class="form-control" id="username" placeholder="Enter username" name="username">
                        </div>
                        <div class="form-group">
                          <label for="pwd">Password:</label>
                          <input type="password" class="form-control" id="pwd" placeholder="Enter password" name="password">
                        </div>
                        <div class="checkbox">
                          <label><input type="checkbox" name="remember">  Keep me signed in
                        </label>
                        </div>
                        <button type="submit" name="login" class="btn btn-default center-block">Login</button>

                        <div class="text-center">
                            <a class="tg-btnforgotpassword" id="tg-btnforgotpassword">Forgot your password?</a> |  <a class="tg-btnforgotpassword" href="http://www.ssappsnwebs.com/spass/admin2/Auth/signup">Signup</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row footer"></div>
    </div>
</body>
</html>