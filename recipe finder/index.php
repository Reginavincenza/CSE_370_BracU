<?php // we need to connect the database to validate 
require_once('DBconnect.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Recommendation</title>
    
    <!-- Bootstrap CSS and other stylesheets -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animate.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">

    <style>
        body {
            background-color: #f2f2f2; /* Light background color */
        }
        #header {
            background: #fff;
            padding: 15px 0;
            border-bottom: 2px solid #ccc;
        }
        #header .nav-links {
            text-align: right;
        }
        #header .nav-links a {
            margin-left: 15px;
            font-size: 18px;
            text-decoration: none;
            color: #9933FF;
            font-weight: bold;
        }
        #section1 {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 70vh; /* Center vertically */
        }
        #section1 .title {
            font-size: 28px;
            margin-bottom: 20px;
            font-weight: bold;
            color: #333;
        }
        .form_design {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .form_design label {
            font-weight: bold;
        }
        .form_design input[type="text"],
        .form_design input[type="password"] {
            width: 250px;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form_design input[type="submit"] {
            background: #9933FF;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .form_design input[type="submit"]:hover {
            background: #6600CC;
        }
    </style>
</head>
<body>

    <!-- ====== Header Section ====== -->
    <section id="header">
        <div class="container">
            <div class="row" style="font-size:30px; color:#9933FF;">
                <div class="col-md-10 text-center"> 
                    <strong>Recipe Recommendation</strong>
                </div>
                <div class="col-md-2 nav-links">
                    <a href="recipe_page.php">Recipe</a>
                    <a href="search.php">Search</a>
                    <a href="user_review.php">Review</a>
                    <a href="Saved.php">Saved</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ====== Sign In Section ====== -->
    <section id="section1">
        <div class="title">SIGN IN</div>
        <form action="login.php" class="form_design" method="post">
            <label>Username:</label><br>
            <input type="text" name="uname"> <br>
            
            <label>Password:</label><br>
            <input type="password" name="pass"> <br>
            
            <input type="submit" value="Sign In">
        </form>
    </section>

    <!-- ====== Footer Section ====== -->
    <section id="footer">
        <div class="container">
            <p style="text-align:center;">© 2025 Recipe Recommendation. All Rights Reserved.</p>
        </div>
    </section>

    <!-- ====== JS Files ====== -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
