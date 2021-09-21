<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('includes/indonesian_date.php');
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="keywords" content="">
<meta name="description" content="">
<title>Car Rental Portal | Status Pesanan</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<!--Custome Style -->
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<!--OWL Carousel slider-->
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<!--slick-slider -->
<link href="assets/css/slick.css" rel="stylesheet">
<!--bootstrap-slider -->
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<!--FontAwesome Font Style -->
<link href="assets/css/font-awesome.min.css" rel="stylesheet">
        
<!-- Fav and touch icons -->
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/images/favicon-icon/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/images/favicon-icon/apple-touch-icon-114-precomposed.html">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/images/favicon-icon/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="assets/images/favicon-icon/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="assets/images/favicon-icon/favicon.png">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
</head>
<body>
        
<!--Header-->
<?php include('includes/header.php');?>
<?php
    $id_order=$_GET['order'];
    $sql = "SELECT tblvehicles.VehiclesTitle, tblvehicles.PricePerDay, tblvehicles.Vimage1, tblbooking.VehicleId, tblbooking.FromDate, tblbooking.ToDate, tblbooking.message, tblbooking.Status, tblbooking.TotalPay, tblbooking.Payment FROM tblbooking JOIN tblvehicles ON tblbooking.VehicleId = tblvehicles.id WHERE tblbooking.id=:orderid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':orderid',$id_order,PDO::PARAM_STR);
    $query->execute();
    $results=$query->fetchAll(PDO::FETCH_OBJ);
    $cnt=1;
    if($query->rowCount() > 0) {
        foreach($results as $result) {
 ?>

 <?php 
 if(isset($_POST['bayar'])) {
    $id = $_POST['id_order'];
    $filename = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(20/strlen($x)) )),1,20) . ".jpg";
    move_uploaded_file($_FILES["bukti"]["tmp_name"], "./bukti_pembayaran/" . $filename);
    
    $sql = "UPDATE tblbooking SET Payment=:bukti WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':bukti', $filename, PDO::PARAM_STR);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $count = $query->rowCount();

    if($count > 0)
        echo "<script type='text/javascript'> 
                alert('Bukti pembayaran berhasil diupload');
                window.location.href = 'my-booking.php'; </script>";
    else 
        echo "<script type='text/javascript'> 
                alert('Terjadi kesalan. Silahkan coba lagi');
                window.location.href = 'status.php?order=".$id."'; </script>";
}
?>

<?php
if(isset($_POST['cancel'])) {
    $id = $_POST['id_order'];
    $sql = "DELETE FROM tblbooking WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $count = $query->rowCount();

    if($count > 0)
        echo "<script type='text/javascript'> 
                alert('Pesanan berhasil dibatalkan');
                window.location.href = 'my-booking.php'; </script>";
    else 
    echo "<script type='text/javascript'> 
                alert('Terjadi kesalan. Silahkan coba lagi');
                window.location.href = 'status.php?order=".$id."'; </script>";
}
?>

<section class="contact_us section-padding">
  <div class="container">
    <div  class="row">
      <div class="col-md-6">
        <h3>Pesanan Anda</h3>
        <div class="contact_form gray-bg">
          <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="id_order" value="<?= $_GET['order'] ?>">
            <div class="form-group">
              <label class="control-label"></label>
              <img src="admin/img/vehicleimages/<?= $result->Vimage1 ?>" class="img-thumbnail" width="300">
            </div>
            <div class="form-group">
              <label class="control-label">Nama Mobil</label>
              <h6><?= $result->VehiclesTitle ?> </h6>
            </div>
            <div class="form-group">
              <label class="control-label">Harga per Hari</label>
              <?php 
                if($_SESSION['login'] != '') { 
                  $sql = "SELECT COUNT(id_user) AS user_count FROM tblbooking WHERE id_user=". $_SESSION['id_user'] ." AND Status=1;";
                  $query = $dbh->prepare($sql);
                  $query->execute();
                  $count = $query->fetchAll(PDO::FETCH_OBJ);
                  if($count[0]->user_count % 2 == 0 && $count[0]->user_count != 0) { 
                    $promo = floor($result->PricePerDay / 2); ?>
                    <sup><del class="text-danger"><p class="text-danger" style="margin: 0; padding: 0; font-size: 12px"> <?php echo htmlentities(number_format($result->PricePerDay, 0, ',', '.'));?> </p></del></sup>
                    <h6 style="margin-top: -10px; padding: 0;"> <?php echo htmlentities(number_format($promo, 0, ',', '.'));?></h6>
              <?php } else { ?>
                    <h6>Rp. <?php echo htmlentities(number_format($result->PricePerDay, 0, ',', '.'));?></h6>
              <?php  } 
                }
              ?>
            </div>
            <div class="form-group">
              <label class="control-label">Tanggal Sewa</label>
              <h6><?= indonesian_date($result->FromDate) ?> </h6>
            </div>
            <div class="form-group">
              <label class="control-label">Tanggal Kembali</label>
              <h6><?= indonesian_date($result->ToDate) ?> </h6>
            </div>
            <div class="form-group">
              <label class="control-label">Pesan</label>
              <h6><?= $result->message ?> </h6>
            </div>
            <div class="form-group">
              <label class="control-label">Total Harga : </label>
              <h3>Rp. <?= number_format($result->TotalPay, 0, ',', '.') ?></h3>
            </div>
        <?php if($result->Payment == '') { ?>
            <div class="form-group">
              <button class="btn btn-sm" type="button" data-toggle="modal" data-target="#uploadForm">Upload Bukti <span class="angle_arrow"><i class="fa fa-angle-right"></i></span></button>
              <button class="btn btn-sm btn-primary" name="cancel" type="submit">Cancel Pesanan<span class="times"><i class="fa fa-times" aria-hidden="true"></i></span></button>
            </div>
        <?php } else { ?>
            <div class="form-group">
              <label class="control-label">Bukti Pembayaran : </label>
              <a href="./bukti_pembayaran/<?= $result->Payment ?>" target="blank" class="badge badge-primary">Lihat Bukti Pembayaran</a>
            </div>
        <?php } ?>
            <!-- Modal Upload -->
            <div class="modal fade" id="uploadForm">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">Upload Bukti Pembayaran</h3>
                        <p class="text-danger">* Harap melakukan pengecekan terhadap bukti pembayaran. Proses ini tidak dapat diulang.</p>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                        <div class="login_wrap">
                            <div class="col-md-12 col-sm-6">
                                <div class="form-group">
                                <input type="file" class="form-control" name="bukti" placeholder="Bukti Pembayaran*">
                                </div>
                                <div class="form-group">
                                <input type="submit" name="bayar" value="Upload" class="btn btn-block">
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <!-- End of Modal Upload -->
            
          </form>
        </div>
      </div>
      <div class="col-md-6">
        <h3>Instruksi Pembayaran</h3>
        <div class="contact_detail">
          <ul>
            <li>
              <div class="icon_wrap"><i class="fa fa-money" aria-hidden="true"></i></div>
              <div class="contact_info_m">
                <h5>Pembayaran via Transfer Bank</h5>
                <p>Silahkan melakukan pembayaran sesuai tagihan melalui transfer pada rekening berikut ini : </p>
                <div class="row">
                    <div class="col-sm-3 offset-sm-3">
                        <img src="./assets/images/logo_bri.png" class="img img-responsive" width="50">
                    </div>
                    <div class="col-sm-6">
                        <b>RENTAL DEDI JAYA</b>
                        <b>981 9913 3881</b>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-3 offset-sm-3">
                        <img src="./assets/images/logo_bca.png" class="img img-responsive" width="100">
                    </div>
                    <div class="col-sm-6">
                        <b>RENTAL DEDI JAYA</b>
                        <b>981 9913 3881</b>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-3 offset-sm-3">
                        <img src="./assets/images/logo_mandiri.png" class="img img-responsive" width="100">
                    </div>
                    <div class="col-sm-6">
                        <b>RENTAL DEDI JAYA</b>
                        <b>981 9913 3881</b>
                    </div>
                </div>
              </div>
            </li>
            <br>
            <hr>
            <br>
            <li>
              <div class="icon_wrap"><i class="fa fa-mobile" aria-hidden="true"></i></div>
              <div class="contact_info_m">
                <h5>Pembayaran via Dompet Digital</h5>
                <p>Silahkan melakukan pembayaran sesuai tagihan melalui transfer pada dompet digital berikut ini : </p>
                <div class="row">
                    <div class="col-sm-3 offset-sm-3">
                        <img src="./assets/images/logo_gopay.png" class="img img-responsive" width="50">
                    </div>
                    <div class="col-sm-6">
                        <b>RENTAL DEDI JAYA</b>
                        <b>981 9913 3881</b>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-3 offset-sm-3">
                        <img src="./assets/images/logo_dana.png" class="img img-responsive" width="100">
                    </div>
                    <div class="col-sm-6">
                        <b>RENTAL DEDI JAYA</b>
                        <b>981 9913 3881</b>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-3 offset-sm-3">
                        <img src="./assets/images/logo_ovo.png" class="img img-responsive" width="100">
                    </div>
                    <div class="col-sm-6">
                        <b>RENTAL DEDI JAYA</b>
                        <b>981 9913 3881</b>
                    </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<?php }} ?>

<!--Footer -->
<?php include('includes/footer.php');?>
<!-- /Footer--> 

<!--Back to top-->
<div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
<!--/Back to top--> 

<!--Login-Form -->
<?php include('includes/login.php');?>
<!--/Login-Form --> 

<!--Register-Form -->
<?php include('includes/registration.php');?>

<!--/Register-Form --> 

<!--Forgot-password-Form -->
<?php include('includes/forgotpassword.php');?>
<!--/Forgot-password-Form --> 

<!-- Scripts --> 
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<!--Switcher-->
<script src="assets/switcher/js/switcher.js"></script>
<!--bootstrap-slider-JS--> 
<script src="assets/js/bootstrap-slider.min.js"></script> 
<!--Slider-JS--> 
<script src="assets/js/slick.min.js"></script> 
<script src="assets/js/owl.carousel.min.js"></script>

</body>

<!-- Mirrored from themes.webmasterdriver.net/carforyou/demo/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 16 Jun 2017 07:26:12 GMT -->
</html>