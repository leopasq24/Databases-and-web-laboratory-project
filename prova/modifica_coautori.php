<?php
include_once("connect.php");
session_start();
if(isset($_POST["coautori"]) && isset($_POST["idblog"])){
	$idblog=$_POST["idblog"];
	$coautori = explode(", ", trim($_POST["coautori"]));
	$a=array();

	foreach ($coautori as $key => $value){
		$stmt_idcoautori=mysqli_prepare($link, "SELECT IdUtente FROM utente WHERE username=?");
		mysqli_stmt_bind_param($stmt_idcoautori,"s",$value);
		mysqli_stmt_execute($stmt_idcoautori);
		$result_idcoautori=mysqli_stmt_get_result($stmt_idcoautori);
		while($row = mysqli_fetch_assoc($result_idcoautori)){
			array_push($a, $row["IdUtente"]);
		}
		mysqli_stmt_close($stmt_idcoautori);
	}

	$stmt_rmv=mysqli_prepare($link, "DELETE FROM coautore WHERE IdBlog=?");
	mysqli_stmt_bind_param($stmt_rmv,"i",$idblog);
	mysqli_stmt_execute($stmt_rmv);


	foreach ($a as $key => $value) {
           $stmt_coautori = mysqli_prepare($link, "INSERT INTO coautore(IdUtente, IdBlog) VALUES (?, ?)");
           mysqli_stmt_bind_param($stmt_coautori, "ii", $value, $idblog);
           mysqli_stmt_execute($stmt_coautori);
           mysqli_stmt_close($stmt_coautori);
        }
        
    echo"OK";
}else {
    	echo "richiesta fallita:".var_dump($_POST);
	}

?>