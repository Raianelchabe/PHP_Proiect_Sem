<?php
    if(isset($_POST['login'])){
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Replace 'username' and 'password' with your actual username and password
        if($username === 'username' && $password === 'password'){
            echo "<script>window.open('index.php', '_blank');</script>";
        } else {
            echo "Try again";
        }
    }
?>

<!DOCTYPE html>
<html>
<head>

    <!-- CSS only -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <!-- JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <style>
        .bg-green {
            background-color: #58a79b;
        }
        .text-white {
            color: #fff;
        }

        .footer { 
    position: absolute; 
    bottom: 0; 
    left: 0; 
    z-index: 10;
    background-color: #58a79b;
}
    </style>
</head>
<body>

    <!-- Header -->
    <header class="bg-green text-white p-3">
        <h1 class="text-center">Welcome to My Website</h1>
    </header>

    <!-- Main content -->
    <div class="container">
        <!-- Your login form here -->
    </div>

    
    <div class="container">
        <h2 class="text-center">Login Page</h2>
        <div class="row justify-content-center">
            <div class="col-4">
                <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" name="username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <button type="submit" class="btn btn-primary" name="login">Login</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2024 My Website. All rights reserved.</p>
    </footer>
</body>
</html>