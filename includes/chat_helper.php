<?php

session_start();
include('config.php');

$id_customer = $_POST['id_customer'];
$pesan = isset($_POST['pesan']) ? $_POST['pesan'] : '';
$timestamp = isset($_POST['timestamp']) ? $_POST['timestamp'] : '';

if($id_customer != NULL && $pesan != NULL && $timestamp != NULL) {
    $sql = "INSERT INTO tblchat (id_user, message, timestamp) VALUES (:puser, :pmessage, :ptimestamp)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':puser', $id_customer, PDO::PARAM_STR);
    $query->bindParam(':pmessage', $pesan, PDO::PARAM_STR);
    $query->bindParam(':ptimestamp', $timestamp, PDO::PARAM_STR);
    $query->execute();


} else if($id_customer != NULL && $pesan == NULL && $timestamp == NULL) {
    $sql = "SELECT * FROM tblchat WHERE id_user=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id_customer, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    echo json_encode($results);
}
