<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Praktiskt Prov</title>
</head>
<body>
<h1>Filmdatabase</h1>
<?php
    //Connecting to MySQL
    require_once 'login.php';
    $connection = new mysqli($db_hostname, $db_username, $db_password, $db_database); //retrieving values from login.php

    //Check connection
    if ($connection->connect_error) die ($connection->connect_error); 

    /*Prevent SQL injections and XSS attacks*/
    function mysql_entities_fix_string($string)
    {
        return htmlentities(mysql_fix_string($string)); //protecting against XSS attacks
    }

    function mysql_fix_string($string)
    {
        if (get_magic_quotes_gpc()) $string = stripslashes($string);
        return mysqli_real_escape_string($string); //protecting against SQL injections
    }

    /*Contacting SQL database and collecting movie informations*/
    $result = mysqli_query($connection,"SELECT movies.Title, movies.Director, movies.Year, genre.Category, movies.id FROM movies INNER JOIN genre ON movies.id=genre.id")
                          or die("Failed to query database ".mysqli_error($connection));

    
    /*fetching a result row and returning an array*/ 
    $data = array();

    for($i=0;$row = $result->fetch_array();$i++){
        $data[$i]=array($row[0],$row[1],$row[2],$row[3],$row[4]);
    }
    
    /*prepared statement to prepare and bind*/
    $stmt1 = $connection->prepare("INSERT INTO movies (Title, Director, Year) VALUES (?, ?, ?)");
    $stmt2 = $connection->prepare("INSERT INTO genre (Category) VALUES (?)");  

    /*collecting the data*/ 
    $title = $_POST['title'] ?? "";
    $director = $_POST['director'] ?? "";
    $year = $_POST['year'] ?? "";
    $category = $_POST['genre'] ?? "";
?>

<!-- create a table with different categories -->
<table>
    <thead>
        <tr> 
            <th>Title</th>
            <th>Director</th>
            <th>Year</th>
            <th>Category</th>
            <th>Update</th>
            <th>Delete</th>
        </tr>
    </thead>
    
    <!-- calling all the rows from the database and including two buttons --> 
    <tbody>
        <?php foreach($data as $row) : ?> 
        <tr>
                <td><?php echo $row[0]; ?></td>
                <td><?php echo $row[1]; ?></td>
                <td><?php echo $row[2]; ?></td>
                <td><?php echo $row[3]; ?></td>
                <td><a href="edit.php?id=<?php echo $row[4]; ?>">Edit</a></td>
                <td><a href="delete.php?id=<?php echo $row[4]; ?>">Delete</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
        //Button clicked
        if(isset($_POST['submit'])){
        
        //binds the parameters to query
        $stmt1->bind_param("sss", $title, $director, $year);
        $stmt2->bind_param("s", $category);
        
        //checking if the textfields are empty 
        if(empty($title) || empty($director) || empty($year) || empty($category)){
            echo "<br>Textfield is empty!";
            }
        else{
            $stmt1->execute(); //writes out rows from the database
            $stmt2->execute();
            echo "<meta http-equiv='refresh' content='0'>"; //refreshes page 
            }
        }
?>

<!-- create radiobuttons -->
<form method="POST" action="index.php">
    <h2>My movie storage</h2>
    <p>Choose a category:</p>
    <p>Horror<input type="radio" name="genre" value="Horror">
    Romantic<input type="radio" name="genre" value="Romantic">
    Swedish<input type="radio" name="genre" value="Swedish">
    Animated<input type="radio" name="genre" value="Animated">
    Comedy<input type="radio" name="genre" value="Comedy">
    </p>
    <!-- create textfields -->
    <p>Title: <input type="text" name="title" id="title">
    Director: <input type="text" name="director" id="director">
    Year: <input type="text" name="year" id="year">
    </p>
    <!-- create button -->
    <p>
    <input type="submit" name="submit" value="Store">
    </p>
</form>
</body>
</html>