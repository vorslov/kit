<?php
//	error_reporting(E_ALL);
//	ini_set('display_errors', '1');

	$db=mysqli_connect("xxx", "xxx", "xxx", "xxx", "xxx");
	mysqli_set_charset($db, 'utf8');
	
	if(isset($_GET['change'])){
		if(!empty($_GET['change'])){
			if(!empty($_GET['type'])){
				if(in_array($_GET['type'], array("n","p"))){
					if(!empty($_GET['value'])){
						$_GET['value']=urldecode($_GET['value']);

						$qr='update elements set '.($_GET['type']=='n'?'n':'p').'='.($_GET['type']=='n'?'"'.mysqli_real_escape_string($db,$_GET['value']).'"':(float)$_GET['value']*100).' where id='.(int)$_GET['change'];
						$res=mysqli_query($db, $qr);
						if(mysqli_affected_rows($db)<=0){
							$qr='insert into elements set id='.(int)$_GET['change'].', pid='.(int)$_GET['pid'].', n="", p=0';
							$res=mysqli_query($db, $qr);
							if($res!==false){
								$qr='update elements set '.($_GET['type']=='n'?'n':'p').'='.($_GET['type']=='n'?'"'.mysqli_real_escape_string($db,$_GET['value']).'"':(float)$_GET['value']*100).' where id='.(int)$_GET['change'];
								$res=mysqli_query($db, $qr);
							}
						}
						die($res!==false?"Ok":"False: {$qr}");
					}
				}
			}
		}
	}
	
	if(isset($_GET['addnewline'])){
		if(isset($_GET['id'])){
			if(!empty($_GET['id'])){
				if(isset($_GET['pid'])){
					if(!empty($_GET['pid'])){
						$qr='insert into elements set id='.(int)$_GET['id'].', pid='.(int)$_GET['pid'].', n="", p=0';
						$res=mysqli_query($db, $qr);
						die($res!==false?"Ok":"False: {$qr}");
					}
				}
			}
		}
	}
	if(isset($_GET['addnewgroup'])){
		if(isset($_GET['id'])){
			if(!empty($_GET['id'])){
				$qr='insert into elements set id='.(int)$_GET['id'].', pid=0, n="", p=0';
				$res=mysqli_query($db, $qr);
				die($res!==false?"Ok":"False");
			}
		}
	}		
	if(isset($_GET['deleteline'])){
		if(!empty($_GET['deleteline'])){
			$qr='delete from elements where id='.(int)$_GET['deleteline'];
			$res=mysqli_query($db, $qr);
			die($res!==false?"Ok":"False");
		}
	}		
	if(isset($_GET['deletegroup'])){
		if(!empty($_GET['deletegroup'])){
			$qr='delete from elements where pid='.(int)$_GET['deletegroup'];
			$res=mysqli_query($db, $qr);
			if($res!==false){
				$qr='delete from elements where id='.(int)$_GET['deletegroup'];
				$res=mysqli_query($db, $qr);
			}
			die($res!==false?"Ok":"False");
		}
	}		
	
	
	$res=mysqli_query($db, 'SELECT id, pid, n, p FROM elements order by n');
	$elements=array();
	while($row=mysqli_fetch_assoc($res)){
		$elements[]=$row;
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

    <title>Элементы</title>
  </head>
  <body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<a class="navbar-brand" href="elements.php">Элементы</a>
		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav">
				<li class="nav-item"><!--active-->
					<a class="nav-link" href="index.php">Конструктор</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="sessions.php">Сохраненные расчеты</a>
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
		
		<h4>Редактор элементов</h4>
		<form>
			<div class="mainDiv"></div>
			<div class="form-row mb-1">
				<div class="col-12">
					<button type="button" type="button" name="addgroup" class="btn btn-primary">Добавить группу</button>
				</div>
			</div>

		</form>

	</div>


	<div class="modal" tabindex="-1" role="dialog" id="deleteLinePopUp">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Удаление строки</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p>Вы действительно хотите удалить эту строку?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary">Да, хочу удалить</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal" tabindex="-1" role="dialog" id="deleteGroupPopUp">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Удаление группы</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p>Вы действительно хотите удалить эту группу и все строки входящие в группу?</p>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>
	
	<script>
	var lineid=0;
	var groupid=0;
	
	var datalines=new Array(
<?php
		$totalElements=count($elements);
		foreach($elements as $row){
			$totalElements--;
			echo 'new Array('.$row['id'].',"'.htmlspecialchars($row['n'],ENT_HTML401,'utf8').'", '.$row['p'].', '.$row['pid'].')'.($totalElements>0?',':'');
		}
?>
	);
	
	$(document).ready(function(){
		$('button[name=addgroup]').each(function(){
			$(this).click(function(){
				var parentDivs=$('div.mainDiv');
				var parentDiv=$(parentDivs[0]);
				lineid++;
				groupid=lineid;
				var addedElement=$(parentDiv).prepend('<div class="form-row mb-1" data-groupid="'+groupid+'"><div class="col-12"><div class="input-group"><input type="text" class="form-control" placeholder="Название группы элементов" title="Название группы элементов" data-id="'+groupid+'" data-pid="0" name="n"><div class="input-group-append"><button class="btn btn-sm btn-danger input-group-text" type="button" title="Удалить" data-toggle="modal" data-deleteid="'+groupid+'" data-target="#deleteGroupPopUp">&times;</button></div></div></div></div><div><div class="form-row mb-1" data-parentid="'+groupid+'"><div class="col-12 text-right"><button type="button" type="button" name="addline" data-parentid="'+groupid+'" class="btn btn-primary">Добавить элемент</button></div></div></div>');
				$('button[name=addline][data-parentid='+groupid+']', $(addedElement)).each(function(){
					$(this).click(click2AddLine);		
				});
				$('input[data-id='+groupid+']').on("change", changeElement);
				$.ajax({
					url: "?addnewgroup=1&id="+groupid
				}).done(function(){
				});
			})
		});
	
		function click2AddLine(){
			var parentDivs=$('div.form-row[data-parentid='+this.dataset.parentid+']');
			var parentDiv=$(parentDivs[0]).parent();
			lineid++;
			$(parentDiv).prepend('<div class="form-row mb-1" data-parentid="'+this.dataset.parentid+'" data-lineid="'+lineid+'"><div class="col-1">&nbsp;</div><div class="col-7"><input type="text" class="form-control" placeholder="Название элемента" title="Название элемента" data-id="'+lineid+'" data-pid="'+this.dataset.parentid+'" name="n"></div><div class="col-4"><div class="input-group"><input type="number" class="form-control" placeholder="Цена" title="Цена" data-id="'+lineid+'" data-pid="'+this.dataset.parentid+'" name="p" step="0.01" min="0"><div class="input-group-append"><button class="btn btn-sm btn-danger input-group-text" type="button" title="Удалить" data-deleteid="'+lineid+'" data-toggle="modal" data-target="#deleteLinePopUp">&times;</button></div></div></div></div>');
			$('input[data-id='+lineid+']').on("change", changeElement);
			$.ajax({
				url: "?addnewline=1&id="+lineid+'&pid='+this.dataset.parentid
			}).done(function(){
			});
		};
		
		$('button[name=addline]').each(function(){
			$(this).click(click2AddLine);		
		});
		
		$('#deleteLinePopUp').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget);
			var deleteid = button.data('deleteid');
			var modal = $(this)
			modal.find('.btn-primary').click(function(){
				$('div.form-row[data-lineid='+deleteid+']').detach();
				$.ajax({
					url: "?deleteline="+deleteid
				}).done(function(){
				});
				modal.modal('hide');
			});
		})		
		
		$('#deleteGroupPopUp').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget);
			var deleteid = button.data('deleteid');
			var modal = $(this)
			modal.find('.btn-primary').click(function(){
				$('div.form-row[data-parentid='+deleteid+']').detach();
				$('div.form-row[data-groupid='+deleteid+']').detach();
				$.ajax({
					url: "?deletegroup="+deleteid
				}).done(function(){
				});
				modal.modal('hide');
			});
		})	
		
		$('input').on("change", changeElement);
		function changeElement(){
			$.ajax({
				url: "?change="+$(this).data('id')+"&pid="+$(this).data('pid')+"&type="+$(this).attr("name")+"&value="+encodeURIComponent($(this).val())
			}).done(function(){
			});
		}
		

		function escapeHtml(unsafe) {
			return unsafe
				 .replace(/&/g, "&amp;")
				 .replace(/</g, "&lt;")
				 .replace(/>/g, "&gt;")
				 .replace(/"/g, "&quot;")
				 .replace(/'/g, "&#039;");
		}
		
		if(datalines.length>0){
			for(i=0; i<datalines.length; i++){
				var row=datalines[i];
				if(row[3]==0){
					var parentDivs=$('div.mainDiv');
					var parentDiv=$(parentDivs[0]);
					groupid=row[0];
					lineid=row[0];
					var val=escapeHtml(row[1]);
					var addedElement=$(parentDiv).prepend('<div class="form-row mb-1" data-groupid="'+groupid+'"><div class="col-12"><div class="input-group"><input type="text" class="form-control" placeholder="Название группы элементов" title="Название группы элементов" value="'+val+'" data-id="'+groupid+'" data-pid="0" name="n"><div class="input-group-append"><button class="btn btn-sm btn-danger input-group-text" type="button" title="Удалить" data-toggle="modal" data-deleteid="'+groupid+'" data-target="#deleteGroupPopUp">&times;</button></div></div></div></div><div><div class="form-row mb-1" data-parentid="'+groupid+'"><div class="col-12 text-right"><button type="button" type="button" name="addline" data-parentid="'+groupid+'" class="btn btn-primary">Добавить элемент</button></div></div></div>');
					$('input[data-id='+groupid+']').on("change", changeElement);
					$('button[name=addline][data-parentid='+groupid+']', $(addedElement)).each(function(){
						$(this).click(click2AddLine);		
					});
				}
			}
			for(i=0; i<datalines.length; i++){
				var row=datalines[i];
				if(row[3]!=0){
					var parentid=row[3];
					var parentDivs=$('div.form-row[data-parentid='+parentid+']');
					var parentDiv=$(parentDivs[0]).parent();
					var lineid4add=row[0];
					if(lineid<row[0]) lineid=row[0];
					price=row[2]/100;
					var val=escapeHtml(row[1]);
					$(parentDiv).prepend('<div class="form-row mb-1" data-parentid="'+parentid+'" data-lineid="'+lineid4add+'"><div class="col-1">&nbsp;</div><div class="col-7"><input type="text" class="form-control" placeholder="Название элемента" title="Название элемента" value="'+val+'" data-id="'+lineid4add+'" data-pid="'+parentid+'" name="n"></div><div class="col-4"><div class="input-group"><input type="number" class="form-control" placeholder="Цена" title="Цена" data-id="'+lineid4add+'" data-pid="'+parentid+'" name="p" step="0.01" min="0" value="'+price+'"><div class="input-group-append"><button class="btn btn-sm btn-danger input-group-text" type="button" title="Удалить" data-deleteid="'+lineid4add+'" data-toggle="modal" data-target="#deleteLinePopUp">&times;</button></div></div></div></div>');
					$('input[data-id='+lineid4add+']').on("change", changeElement);
				}
			}
		}
		
	});
	</script>
  </body>
</html>