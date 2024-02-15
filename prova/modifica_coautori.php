<?php
include_once("connect.php");
session_start();
if(isset($_POST["coautori"]) && isset($_POST["idblo"])){
	$idblog=$_POST["idblog"];
	$coautori = explode(", ", trim($_POST["coautori"]));
	$a=array();
	$updatedData=array();
	$nuovi_coautori=array();

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

    $stmt_nuovi_coautori = mysqli_prepare($link, "SELECT Username FROM coautore, utente WHERE coautore.IdUtente=utente.IdUtente AND coautore.IdBlog=?");
    mysqli_stmt_bind_param($stmt_nuovi_coautori, "i", $idblog);
    mysqli_stmt_execute($stmt_nuovi_coautori);
    $result_nuovi_coautori=mysqli_stmt_get_result($stmt_nuovi_coautori);
    while($row=mysqli_fetch_assoc($result_nuovi_coautori)){
    	array_push($nuovi_coautori, $row["Username"]);
    }
    mysqli_stmt_close($stmt_nuovi_coautori);

    $output = implode(", ", $nuovi_coautori);

	echo json_encode(array('status' => 'OK', 'data' => $output));

}else {
	$response = "Richiesta fallita";
    echo json_encode(array('status' => 'Errore', 'data' => $response));
}

?>
