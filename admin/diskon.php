<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else {

if(isset($_POST['submit'])) {
	$id = $_POST['id_vehicle'];
	$diskon = $_POST['diskon'];
	$sql = "UPDATE tblvehicles SET Discount=:diskon WHERE id=:id_vehicle";
	$query = $dbh->prepare($sql);
	$query->bindParam(':diskon', $diskon, PDO::PARAM_INT);
	$query->bindParam(':id_vehicle', $id, PDO::PARAM_INT);
	$query->execute();
	$msg="Diskon berhasil disubmit";
}

if(isset($_POST['hapus']))
{
	$delid=intval($_POST['id_diskon']);
	$sqlUpdate = "UPDATE tblvehicles SET Discount=NULL WHERE id=:delid";
	$queryUpdate = $dbh->prepare($sqlUpdate);
	$queryUpdate -> bindParam(':delid', $delid, PDO::PARAM_INT);
	$queryUpdate -> execute();
	$msg="Diskon berhasil dihapus";
}

 ?>

<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="theme-color" content="#3e454c">
	
	<title>Car Rental Portal | Admin Diskon Mobil</title>

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
  <style>
		.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
		</style>

</head>

<body>
	<?php include('includes/header.php');?>

	<div class="ts-main-content">
		<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">

						<h2 class="page-title">Diskon Mobil</h2>

						<!-- Zero Configuration Table -->
						<div class="panel panel-default">
							<div class="panel-heading">Detail Mobil</div>
							<div class="panel-body">
							<?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
								<table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nama Mobil</th>
											<th>Merk</th>
											<th>Harga per Hari</th>
											<th>Model Tahun</th>
											<th>Diskon</th>
											<th>Harga Diskon</th>
											<th>Aksi</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>#</th>
											<th>Nama Mobil</th>
											<th>Merk</th>
											<th>Harga per Hari</th>
											<th>Model Tahun</th>
											<th>Diskon</th>
											<th>Harga Diskon</th>
											<th>Aksi</th>
										</tr>
									</tfoot>
									<tbody>

<?php $sql = "SELECT tblvehicles.VehiclesTitle,tblbrands.BrandName,tblvehicles.PricePerDay,tblvehicles.ModelYear,tblvehicles.id, tblvehicles.Discount from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{				?>	
										<tr>
											<td><?php echo htmlentities($cnt);?></td>
											<td><?php echo htmlentities($result->VehiclesTitle);?></td>
											<td><?php echo htmlentities($result->BrandName);?></td>
											<td>Rp. <?php echo htmlentities(number_format($result->PricePerDay, 0, ',', '.'));?></td>
											<td><?php echo htmlentities($result->ModelYear);?></td>
											<td><?php echo htmlentities($result->Discount ? $result->Discount : "0");?> % </td>
											<td>Rp. <?php echo htmlentities(number_format($result->Discount ? ($result->PricePerDay - floor($result->PricePerDay * $result->Discount / 100)) : $result->PricePerDay, 0, ',', '.'))?></td>
											<td></td>
											<td>
												<form method="post">
													<input type="hidden" name="id_diskon" value="<?= $result->id ?>">
													<a href="#diskonForm" id="button-modal" data-toggle="modal" data-dismiss="modal" data-id="<?= $result->id ?>"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
													
													<button type="submit" name="hapus" ><i class="fa fa-trash"></i></button>
												</form>
											</td>
										</tr>
										<?php $cnt=$cnt+1; }} ?>
										
									</tbody>
								</table>

						

							</div>
						</div>

					

					</div>
				</div>

			</div>
		</div>
	</div>
	
	<div class="modal fade" id="diskonForm">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h3 class="modal-title">Persentase Diskon</h3>
		  </div>
		  <div class="modal-body">
			<div class="row">
			  <div class="login_wrap">
				<div class="col-md-12 col-sm-6">
				  <form method="post">
					<div class="form-group">
						<input type="hidden" name="id_vehicle" id="hidden-id" value="">
						<input type="number" class="form-control" name="diskon" min=0 max=100 placeholder="Diskon dalam persen">
					</div>
					<div class="form-group">
					  <input type="submit" name="submit" value="Submit" class="btn btn-block">
					</div>
				  </form>
				</div>
			   
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	</div>

	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
	
	<script>
		$(document).on("click", "#button-modal", function() {
			var id = $(this).data("id");
			$("#hidden-id").val(id);
			console.log(id);
		});
	</script>
</body>
</html>
<?php } ?>
