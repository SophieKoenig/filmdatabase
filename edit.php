<?php

require_once 'login.php';
$connection = new mysqli($db_hostname, $db_username, $db_password, $db_database); //retrieving values from login.php

//get id through query string
$id = $_GET['id'];

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

//select query
$qry = mysqli_query($connection,"SELECT movies.Title, movies.Director, movies.Year, genre.Category FROM movies INNER JOIN genre ON movies.id=genre.id WHERE movies.id =$id");

//when click on Update button
if(isset($_POST['update'])) 
{   //collecting the data
    $title = $_POST['title'];
    $director = $_POST['director'];
    $year = $_POST['year'];
    $category = $_POST['genre'];
	
     /*Contacting SQL database and update movie informations*/
    $edit1=mysqli_query($connection,"UPDATE movies SET Title='$title', Director='$director', Year='$year' WHERE id=$id");
    $edit2=mysqli_query($connection,"UPDATE genre SET category='$category' where id=$id");
	
    //when data is updated
    if($edit1 || $edit2)
    {
        mysqli_close($connection); //Close connection
        header("location:index.php"); //redirects to index page
        exit;
    }
    else
    {
        echo mysqli_error($connection);
    }    	
}
?>

<h3>Update Data</h3>

<!-- creating radiobuttons and textfields --> 
<form method="POST">
    <p>Choose a category:</p>
    <p>Horror<input type="radio" name="genre" value="Horror">
    Romantic<input type="radio" name="genre" value="Romantic">
    Swedish<input type="radio" name="genre" value="Swedish">
    Animated<input type="radio" name="genre" value="Animated">
    Comedy<input type="radio" name="genre" value="Comedy">
    </p>
    <p>Title: <input type="text" name="title" id="title">
        Director: <input type="text" name="director" id="director">
        Year: <input type="text" name="year" id="year">
    </p>
    <p>
        <input type="submit" name="update" value="Update">
    </p>
</form>