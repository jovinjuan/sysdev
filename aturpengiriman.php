<?php
require "config.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $idpesanan = $_POST['idpesanan'];
    $tanggalpengiriman = $_POST['tanggalpengiriman'];

    // Prepare the SQL query to update the tanggalpengiriman in the pesanan table
    $sql = "UPDATE pesanan SET tanggalpengiriman = :tanggalpengiriman WHERE idpesanan = :idpesanan";
    $query = $conn->prepare($sql);
    $query->bindParam(':idpesanan',$idpesanan,PDO::PARAM_INT);
    $query->bindParam(':tanggalpengiriman',$tanggalpengiriman,PDO::PARAM_STR);
    // Execute the query with the form data
    $result = $query->execute();

    if($result){
    // Redirect back to the pesanan page after successful update
    header("Location: pesanan.php");
    exit();
    }
}
else {
    // If the request method is not POST, redirect to pesanan.php
    header("Location: pesanan.php");
    echo "Err";
    exit();
}


?>