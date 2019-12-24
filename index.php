<?php
	$db=mysqli_connect("xxx", "xxx", "xxx", "xxx", "xxx");
	mysqli_set_charset($db, 'utf8');

	if(isset($_GET['savedata'])){
		$_POST['tmpname']=trim($_POST['tmpname']);
		if(!empty($_POST['tmpname'])){
			$qr='insert into elements_saved set ts=now(), name="'.mysqli_real_escape_string($db, $_POST['tmpname']).'", dat="'.mysqli_real_escape_string($db, json_encode($_POST)).'"';
			if(!mysqli_query($db, $qr)){
				header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
				die();
			} else {
				die("Ok");
			}
		} else {
			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
			die();
		}
	}

	$elements_older=array();
	$elements_name_older=array();
	$res=mysqli_query($db, 'SELECT * FROM elements_saved order by name');
	while($row=mysqli_fetch_assoc($res)){
		$elements_older[$row['id']]=$row['dat'];
		$elements_name_older[$row['id']]=$row['name'].' ['.date('d.m.Y H:i',  strtotime($row['ts'])).']';
	}
	
	$elements=array();
	$res=mysqli_query($db, 'SELECT id, pid, n, p FROM elements where pid=0');
	while($row=mysqli_fetch_assoc($res)){
		$elements[]=$row;
		$res2=mysqli_query($db, 'SELECT id, pid, n, p FROM elements where pid='.$row['id']);
		while($row2=mysqli_fetch_assoc($res2)){
			$elements[]=$row2;
		}
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

    <title>Конструктор</title>
	<script>
		<?php
			echo 'var elements_older_key=new Array();';
			echo 'var elements_older_val=new Array();';
			foreach($elements_older as $i=>$v){
				echo 'elements_older_key.push('.$i.');';
				echo 'elements_older_val.push("'.addslashes($v).'");';
			}
		?>
	
		function declension(digit, expr, onlyword = false){
			if (!expr[2]){
				expr[2] = expr[1];
			}
			i = parseInt(digit) % 100;
			
			if (onlyword==true) {
				digit = '';
			} else {
				a = Math.round((digit-i)*100);
				if(a<10) a='0'+a;
				digit = i+','+a;
			}
			if (i >= 5 && i <= 20) {
				res = digit+' '+expr[2];
			} else {
				i%=10;
				if (i == 1) {
					res = digit+' '+expr[0];
				} else {
					if (i >= 2 && i <= 4)
						res = digit+' '+expr[1];
					else
						res = digit+' '+expr[2];
				}
			}
			return new String(res).trim();
		}
	</script>
  </head>
  <body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<a class="navbar-brand" href="index.php">Конструктор</a>
		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav">
				<li class="nav-item"><!--active-->
					<a class="nav-link" href="elements.php">Элементы</a>
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
		<div id="results"></div>
		<h4>Конструктор</h4>
		
		<form id="formData">
			<div class="mainDiv">
<?php
				$rowid=1;
				foreach($elements as $row){
?>			
				<div class="form-row mb-1" data-groupid="1">
				<?php if($row['p']==0){ ?>
					<div class="col-12">
						<?php echo $row['n'];?>
					</div>
				<?php } else { ?>
					<div class="col-1">
					</div>
					<div class="col-4">
						<?php echo $row['n'];?>
					</div>
					<div class="col-3">
						<input type="number" id="rowprice-<?php echo $rowid;?>" data-id="<?php echo $rowid;?>" class="form-control" step="0.01" min="0" value="<?php echo number_format($row['p']/100,2,'.','');?>" name="p-<?php echo $row['id']; ?>">
					</div>
					<div class="col-2">
						<input type="number" id="rowcount-<?php echo $rowid;?>" data-id="<?php echo $rowid;?>" class="form-control" step="0.01" min="0" value="0" name="n-<?php echo $row['id']; ?>">
					</div>
					<div class="col-2" style="padding-top: 7px;">
						<span id="rowsumm-<?php echo $rowid;?>">0</span>
					</div>
				<?php
					$rowid++;
				}
				?>
				</div>
<?php
				}
?>				
			</div>
			<div class="form-row mb-1">
				<div class="col-12 alert alert-success text-right" role="alert">
					Сумма по выбранным элемента составляет: <span id="totalsumm"></span>
				</div>
			</div>
			<div class="form-row mb-1">
				<div class="col-4 text-left" style="padding-top: 7px;">
					Этот расчет
				</div>
				<div class="col-4 text-left">
					<input type="text" id="tmpname" name="tmpname" class="form-control" placeholder="с именем">
				</div>
				<div class="col-4 text-left">
					<button type="button" class="btn btn-primary" id="btnsave">Сохранить</button>
				</div>
			</div>
			<div class="form-row mb-1">
				<div class="col-4 text-left" style="padding-top: 7px;">
					Сохраненные
				</div>
				<div class="col-4 text-left">
					<select class="form-control" id="datas">
					  <option value="-1">-- пустая --</option>
					  <?php
						foreach($elements_name_older as $i=>$n){
							echo '<option value="'.$i.'">'.htmlspecialchars($n, ENT_COMPAT | ENT_HTML401, 'utf8').'</option>';
						}
					  ?>
					</select>					
				</div>
				<div class="col-4 text-left">
					<button type="button" class="btn btn-primary" id="btnload">Загрузить</button>
				</div>
			</div>
		</form>
		
	</div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <!--script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script-->
	<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	
	<script>
		var totalsumm=0;
		$('#totalsumm').html(declension(0, new Array('рубль'   ,'рубля'   ,'рублей')));
		$( document ).ready(function() {
			$('input[id^=rowprice]').each(function(){
				$(this).change(function(){
					var id=$(this).data('id');
					var p=Number.parseFloat($(this).val()*1);
					var n=Number.parseFloat($('#rowcount-'+id).val());
					$('#rowsumm-'+id).html(Math.ceil(p*n*100)/100);
				});
			});
			$('input[id^=rowcount]').each(function(){
				$(this).change(function(){
					var id=$(this).data('id');
					var p=Number.parseFloat($('#rowprice-'+id).val()*1);
					var n=Number.parseFloat($(this).val());
					$('#rowsumm-'+id).html(Math.ceil(p*n*100)/100);
				});
			});
			
			$('input').focus(function(){
				 $(this).select();
			});
			
			$('#btnsave').click(function(){
			  var msg=$('#formData').serialize();
				$.ajax({
				  type: 'POST',
				  url: '?savedata=1',
				  data: msg,
				  success: function(data) {
					$('#results').html('<div class="alert alert-success alert-dismissible fade show" role="alert">Датнные сохранены успешно.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				  },
				  error:  function(xhr, str){
					$('#results').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">Ошибка сохранения.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				  }
				});			
			});
			
			$('#btnload').click(function(){
				var loadedId=parseInt($('#datas').val());
				if(loadedId==-1){
					$('#formData').trigger("reset");
					$('input[id^=rowprice]').each(function(){
						var id=$(this).data('id');
						var p=Number.parseFloat($(this).val()*1);
						var n=Number.parseFloat($('#rowcount-'+id).val());
						$('#rowsumm-'+id).html(Math.ceil(p*n*100)/100);
					});
				} else {
					var idKey=elements_older_key.indexOf(loadedId);
					if(idKey>=0){
						var curDat=JSON.parse(elements_older_val[idKey]);
						for(i in curDat){
							var el=document.querySelector('input[name='+i+']');
							if(el){
								$(el).val(curDat[i]);
							}
						}
						$('input[id^=rowprice]').each(function(){
							var id=$(this).data('id');
							var p=Number.parseFloat($(this).val()*1);
							var n=Number.parseFloat($('#rowcount-'+id).val());
							$('#rowsumm-'+id).html(Math.ceil(p*n*100)/100);
						});
					}
				}
			});
			
			setInterval(function(){
				var totalsumm=0;
				$('span[id^=rowsumm]').each(function(){
					
					var rowsum=parseFloat($(this).html());
					totalsumm+=rowsum;
				});
				$('#totalsumm').html((Math.ceil(totalsumm*100)/100)+' '+declension(totalsumm, new Array('рубль'   ,'рубля'   ,'рублей'), true));
			},500);
		});
	</script>
  </body>
</html>