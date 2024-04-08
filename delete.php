<?php

require_once 'login.php';
$connection = new mysqli($db_hostname, $db_username, $db_password, $db_database); //retrieving values from login.php

$id = $_GET['id']; //get id through query string

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

/*Contacting SQL database and delete movie informations*/
$delete1=mysqli_query($connection,"DELETE FROM movies WHERE id=$id");
$delete2=mysqli_query($connection,"DELETE FROM genre where id=$id");
	
//when data is deleted
if($delete1 || $delete2)
{
    mysqli_close($connection); // Close connection
    header("location:index.php"); // redirects to index page
    exit;
}
else
{
    echo mysqli_error($connection);
}    	
?>