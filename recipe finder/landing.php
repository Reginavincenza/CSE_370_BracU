<?php 
// connecting to database
include('DBconnect.php');

// Fetch featured recipes 
$sql = "SELECT RecipeId, RecipeName, Description FROM recipe ORDER BY Popularity DESC LIMIT 5"
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
        <title> RR | Your go-togo website for recipe recommendations | Find recipes, save favorites and create them by yourself! </title>

    <!-- Bootstrap CSS and other stylesheets -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome.min.css" rel="stylesheet">
    <link href="css/animate.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">

</head>
<body> 
    <!-- ====== Header Section ====== -->
    <section id="header">
        <div class="container">
            <div class="row" style="font-size:20px; font-family: montserrat;">
                <div class="col-md-10" style="font-size:30px; text-align:left; color:#B53324;"> 
                    <a href="landing.php">RR</a> <!-- logo image to be added -->
                </div>
                <div class="col-md-2" style="margin:20px; text-align:center; color:#FEB21A;">
                    <a href="recipes.php" >Recipes</a>
                    <a href="History.php" style="margin-left: 20px;">History</a>
                    <a href="Saved.php" style="margin-left: 20px;">Saved</a>
                    <a href="profiletemplate.php" style="margin-left: 20px;"> Profile</a>   
                </div>
            </div>
        </div>
    </section>
    <!-- ====== Sign In Section ====== -->
     <section id="sign-in">
        <div class="btn-container">
                <button >
                <span class="text" >
     <a href="login.php" style="text-align:center; font-size:20px;"> Login/Signup 
</a>      
                    </span>
                <div class="icon-container">
                    <div class="icon icon--left">
                        <svg>
                            <use xlink:href="#arrow-right"></use>
                        </svg>
                    </div>
                    <div class="icon icon--right">
                        <svg>
                            <use xlink:href="arrow-right"></use>
                        </svg>
                    </div>
                </div>
            </button>
            </div>
     </section>

    <section id= "search-box"> <!--i want to make the search box in the center of the page -->
         <div class="container h-100">
      <div class="d-flex justify-content-center h-100">
        <div class="search">
          <input class="search_input" type="text" name="" placeholder="What do you want to create?">
          <a href="searchoption.php" class="search_icon"><i class="fa fa-search"></i></a>
        </div>
      </div>
    </div>
    </section>

    <section>
        <h1> Featured Recipes </h1>
        <ul>
            <?php while ($row = $result->fetch_assoc()){
                ?> 
                <li>
                    <a href="recipes.php?id=<?php echo $row['RecipeId']; ?>">
                        <!-- I am concerned about what the id means here -->
                        <?php echo htmlspecialchars($row['RecipeName']); ?>
                    </a>
                    <p> <?php echo htmlspecialchars($row['Description']); ?>
                    </p>
                </li>
            <?php } ?>
        </ul>
    </section>

    <footer> <div class="container">
            <p style="text-align:center;">© 2025 RR. Navana Sheikh. ReginaVincenza. All Rights Reserved.</p>
        </div>
    </footer>
    <!-- ====== JS Files ====== -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
