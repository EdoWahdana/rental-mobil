<!doctype html>
<html lang="en" class="no-js">

<?php 
    session_start();
    include('includes/config.php');
?>

<style>
.adiv {
    background: #f59161;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
    font-size: 12px;
    height: 46px
}
.chat-btn {
    position: fixed;
    right: 40px;
    bottom: 40px;
    cursor: pointer
}

.chat-btn .close {
    display: none
}

.chat-btn i {
    transition: all 0.9s ease
}

#check:checked~.chat-btn i {
    display: block;
    pointer-events: auto;
    transform: rotate(180deg)
}

#check:checked~.chat-btn .comment {
    display: none
}

.chat-btn i {
    font-size: 22px;
    color: #fff !important
}

.chat-btn {
    width: 60px;
    height: 60px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 20px;
    background-color: #fa2837;
    color: #fff;
    font-size: 15px;
    border: none;
    box-shadow: 2px 2px 10px #1716165e;
    z-index: 1;
}

.wrapper {
    position: fixed;
    right: 20px;
    bottom: 100px;
    width: 300px;
    height: 400px;
    overflow: auto;
    background-color: #ffffff;
    border-radius: 5px;
    opacity: 0;
    transition: all 0.4s;
    z-index: 1;
    box-shadow: 5px 5px 20px black;
}

#check:checked~.wrapper {
    opacity: 1
}

.chat-form {
    display: block;
    position: relative;
    bottom: 0px;
    padding: 20px;
}

.chat-form input,
textarea,
button {
    margin-bottom: 10px;
    font-size: 15px;
}

.chat-form textarea {
    resize: none
}

.form-control:focus,
.btn:focus {
    box-shadow: none
}

.card {
    width: 300px;
    border: none;
    border-radius: 15px
}

.chat {
    border: none;
    background: #E2FFE8;
    font-size: 13px;
    border-radius: 20px
}

.bg-white {
    background: #FFF;
}
.ml-auto {
	margin-left: auto !important;
}
.mr-auto {
	margin-right: auto !important;
}

.d-flex {
    display: -ms-flexbox !important;
    display: flex !important;
}

.flex-row {
    -ms-flex-direction: row !important;
    flex-direction: row !important;
}
</style>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="theme-color" content="#3e454c">
	
	<title>Rental Dedi Jaya | Admin Pesan Pengguna</title>

	<!-- Font awesome -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- Sandstone Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Bootstrap Datatables -->
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<!-- Bootstrap social button library -->
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<!-- Bootstrap select -->
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<!-- Bootstrap file input -->
	<link rel="stylesheet" href="css/fileinput.min.css">
	<!-- Awesome Bootstrap checkbox -->
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<!-- Admin Stye -->
	<link rel="stylesheet" href="css/style.css">
</head>

<body>
	<?php include('includes/header.php');?>

	<div class="ts-main-content">
		<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">

						<h2 class="page-title">Pesan Pengguna</h2>
                        

<?php 
    // Submit pesan
    if(isset($_POST['submit'])) {
        $id_admin = $_POST['id_admin'];
		$id_user = $_POST['id_user'];
        $pesan = $_POST['pesan'];
        $timestamp = $_POST['timestamp'];
		$status = 0;
        $sql = "INSERT INTO tblchat (id_admin, id_user, message, timestamp, status) VALUES (:id_admin, :id_user, :pesan, :timestamp, :status)";
        $query = $dbh->prepare($sql);
		$query->bindParam(':id_admin', $id_admin, PDO::PARAM_INT);
		$query->bindParam(':id_user', $id_user, PDO::PARAM_INT);
		$query->bindParam(':pesan', $pesan, PDO::PARAM_STR);
		$query->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);
		$query->bindParam(':status', $status, PDO::PARAM_INT);
        $query->execute();
        $results=$query->fetchAll(PDO::FETCH_OBJ);
        if($query->rowCount() > 0) 
            header("Refresh:1");
        else 
			var_dump($query->errorInfo());
            
    }

    // Ambil data pesan
    if (isset($_GET['id_user'])) { 
        $id_user = $_GET['id_user'];
        $sql = "SELECT * FROM v_chat WHERE id=$id_user";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results=$query->fetchAll(PDO::FETCH_OBJ);
            if($query->rowCount() > 0) {
?>

<div class="d-flex justify-content-center">
    <div class="card">
        <div class="p-3 adiv text-center"> <span class="display-4">Chat Admin</span></div>
            <div class="chat-group">
                <?php 
                foreach($results as $result) {
                    if($result->id_admin != 0) {
                        echo `<div class="d-flex flex-row p-3"> <img src="https://img.icons8.com/color/48/000000/circled-user-female-skin-type-7.png" width="30" height="5">
                        <div class="chat ml-auto p-3"><span class="text-muted dot" id="text-admin">` . $result->message .`</span></div>
                        </div> <br>`;
                    } else if ($result->id_admin == 0) {
                        echo `<div class="d-flex flex-row p-3">
                        <div class="chat mr-auto bg-white p-3"><span class="text-muted" id="text-user">`. $result->message .`</span></div>
                        </div> <br>`;
                    }
                } // End if foreach
                ?>
            </div>
            <div class="chat-form"> 
                <form action="" method="post">
					<input type="hidden" name="id_user" value="<?= $_GET['id_user'] ?>">
                    <input type="hidden" name="id_admin" value="<?= $_SESSION['id_admin'] ?>">
                    <input type="hidden" name="timestamp" id="timestamp" value="<?= date('Y-m-d H:i:s') ?>">
                    <div class="form-group px-3"> <textarea class="form-control" name="pesan" id="pesan" rows="2" placeholder="Tulis pesan anda..."></textarea> </div>
                    <input type="submit" name="submit" id="send-chat" class="btn btn-success btn-block" value="Kirim Pesan"> 
                </form>
            </div>
        </div>
    </div>

<?php 
    } // End if query row count
} // End if isset 
?>