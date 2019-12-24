<?php
	$db=mysqli_connect("xxx", "xxx", "xxx", "xxx", "xxx");
	mysqli_set_charset($db, 'utf8');

	if(isset($_GET['delete'])){
		$_GET['delete']=(int)$_GET['delete'];
		if(!empty($_GET['delete'])){
			$qr='delete from elements_saved where id='.(int)$_GET['delete'];
			$res=mysqli_query($db, $qr);
			die($res!==false?"Ok":"False");
		}
	}

	$elements_older=array();
	$elements_name_older=array();
	$res=mysqli_query($db, 'SELECT * FROM elements_saved order by name');
	while($row=mysqli_fetch_assoc($res)){
		$elements_older[$row['id']]=$row;
	}

?>

<!doctype html>
<html lang="ru">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Сохраненные расчеты</title>
  </head>
  <body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<a class="navbar-brand" href="sessions.php">Сохраненные расчеты</a>
		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav">
				<li class="nav-item"><!--active-->
					<a class="nav-link" href="elements.php">Элементы</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="index.php">Конструктор</a>
				</li>
			</ul>
		</div>	
		<nav class="navbar">
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
		</nav>		
	</nav>
	


	<div class="container-fluid">
	<!-- ****************************** -->
		<h4>Сохраненные расчеты</h4>
		
		<form>
<?php
		foreach($elements_older as $oid=>$odat){
?>
			<div class="form-row mb-1" data-lineid="<?php echo $oid; ?>">
				<div class="col-8">
					<?php echo $odat['name'].' ['.date('d.m.Y H:i',  strtotime($odat['ts'])).']';?>
				</div>
				<div class="col-3">
					<?php
						$dat=json_decode($odat['dat'], true);
						$s=0;
						foreach($dat as $n=>$v){
							$na=explode('-',$n);
							if($na[0]=='p'){
								$price=(float)$v;
								$num=(float)$dat['n-'.$na[1]];
								$s+=$price*$num;
							}
						}
						echo number_format($s,2,',',' ');
					?>
				</div>
				<div class="col-1"><button class="btn btn-sm btn-danger" type="button" title="Удалить" data-deleteid="<?php echo $oid; ?>" data-toggle="modal" data-target="#deleteLinePopUp">&times;</button></div>
			</div>
<?php
		}
?>
		</form>
	</div>

	<div class="modal" tabindex="-1" role="dialog" id="deleteLinePopUp">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Удаление сохраненного расчета</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p>Вы действительно хотите удалить этот сохраненный расчет?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary">Да, хочу удалить</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
				</div>
			</div>
		</div>
	</div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	
	<script>
	$(document).ready(function(){
		$('#deleteLinePopUp').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget);
			var deleteid = button.data('deleteid');
			var modal = $(this)
			modal.find('.btn-primary').click(function(){
				$('div[data-lineid='+deleteid+']').detach();
				$.ajax({
					url: "?delete="+deleteid
				}).done(function(){
				});
				modal.modal('hide');
			});
		})	
	})	
	</script>
  </body>
</html>