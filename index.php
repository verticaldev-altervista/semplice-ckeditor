<?php
/****************************************************
*
* 	SEMPLICE  21/11/2010 
*	vroby.mail@gmail.com
*
*****************************************************/
include "lang.php";
function S_GET($n){if (isset($_GET[$n]))return$_GET[$n];else	return "";}
function S_POST($n){if (isset($_POST[$n]))return$_POST[$n];else	return "";}
function S_COOKIE($n){if (isset($_COOKIE[$n]))return$_COOKIE[$n];else	return "";}
function fsave( $filename, $dati){ $fp = fopen($filename, 'w'); fwrite($fp, $dati); fclose($fp); }
function fcheck($path){ @touch($path."/tmp"); $res= (file_exists($path."/tmp")); @unlink($path."/tmp"); return $res;}
function filter($dati){ 
	//$dati= str_replace("\"","",$dati); 
	$dati= str_replace("\\","",$dati); 
	return $dati;}

$msg="";
$op=S_GET('op');

// carica configurazione e sicurezza --------------------------------------------------------
if (file_exists("pages/admin.php"))
    $admin=file("pages/admin.php");
else
	if ($op!='saveadmin'){ 
		$op='admin';
		$msg=_PRIMA_INSTALAZIONE;
		$admin[1]=md5("");
	}

// verifica parametri passati dal form di configurazione ------------------------------------------------
if ($op=="saveadmin"){ 
	if(S_POST('password')!=S_POST('confpassword')){$msg=_UNCONF_PASSWORD; $op='admin';}
	if(S_POST('email')==""){$msg=_NOMAIL; $op='admin';}
	if(trim(S_POST('email'))!=trim(S_POST('confemail'))){$msg=_UNCONF_EMAIL; $op='admin';}
}
//------------------------------------------------------------------------------------------------------------------------------------

// salvataggio configurazione -----------------------------------------------------------------------------------------	
if ($op=="saveadmin"){
	$fp = fopen("pages/admin.php", 'w'); 
	fwrite($fp, "<?php /*\n");
	if (S_POST('password')!="") fwrite($fp, md5(S_POST('password'))."\n");else fwrite($fp,$admin[1]);		
	fwrite($fp, S_POST('email')."\n"); 
	fwrite($fp, S_POST('sitename')."\n"); 		
	fwrite($fp, S_POST('headers')."\n"); 
	fwrite($fp, "*/ ?>\n"); 
	fclose($fp);
	if (S_POST('sendpassword')!=""){
		$subject=_CAMBIO_PASSWORD.S_POST('sitename');
		$message="password : ".S_POST('password');
		mail(S_POST('email'),$subject,$message);
	}
	$msg=_ADMIN_UPDATE;
	$op="";
}
//----------------------------------------------------------------------------------------------------------------------------

	$page=S_GET('page');

// filtri per evitare pagine strane -------------------------------------------------------------------------
$page=str_replace("/","",$page);
$page=str_replace(".","",$page);
if ($page=="")$page="main";
//----------------------------------------------------------------------------------------------------------------------------

//login & logout ------------------------------------------------------------------------------------------------------
if( $op=='logout') {setcookie("admin","",NULL,"");  echo "<script language=javascript>window.location='index.php'</script>";exit();}
if(S_POST('password') !="") $mypassword=md5(S_POST('password')); else $mypassword=S_COOKIE('admin');

if ($op=='login' ){if( trim($admin[1])!=trim($mypassword))$op=""; else {setcookie("admin",$mypassword,0,""); echo "<script language=javascript>window.location='index.php?page=$page'</script>";exit();}}
//----------------------------------------------------------------------------------------------------------------------------

//supporto cookie  e autenticazione -operazioni --------------------------------------------------------
if ($op=='save' && trim($admin[1])!=trim($mypassword))$op="";
if ($op=='admin' && trim($admin[1])!=trim($mypassword) && file_exists("pages/admin.php"))$op="";
//----------------------------------------------------------------------------------------------------------------------------

//salvataggio pagine editate ------------------------------------------------------------------------------------
if ($op=="save"){
	$title=filter(S_POST('title'));
	$textpage=filter(S_POST('textpage'));
	$sidebar=filter(S_POST('sidebar'));
	$footer=filter(S_POST('footer'));

	fsave("pages/title",$title);
	fsave("pages/$page",$textpage);
	fsave("pages/sidebar",$sidebar);
	fsave("pages/footer",$footer);
	$msg= _MODIFICHE_SALVATE;  
}
//----------------------------------------------------------------------------------------------------------------------------

//eliminazione pagina --------------------------------------------------------------------------------
if ($op=='delete' && trim($admin[1])==trim($mypassword)) {
	unlink("pages/$page"); 
	$msg=" <font color='f00'>$page "._ELIMINATA."</font> " ; 
	$op='admin'; 
}
//----------------------------------------------------------------------------------------------------------------------------
//upload file
if($op=="upload"){
	if (is_uploaded_file($_FILES['myfile']['tmp_name'])) {
		if(substr_count($_FILE['myfile']['name'],".")==0){
			 if (!move_uploaded_file($_FILES['myfile']['tmp_name'], 'pages/'.$_FILES['myfile']['name'])) {
				$msg = _UPLOAD_ERROR;
				break;
			}
		}else {
			$msg = _UPLOAD_BADEXTENSION;
			break;
		}
	}
	$op="admin";
}
?>
<html>
	<head>
		<cfheader name="X-XSS-Protection" value="0">
		<title><?=trim($admin[3]) ?></title>
		<meta name="robots" content="" >
		<meta name="generator" content="" >
		<meta name="keywords" content="<?=trim($admin[4]) ?>" >
		<meta name="description" content="" >
		<meta name="MSSmartTagsPreventParsing" content="true" >
		<meta http-equiv="distribution" content="global" >
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
		<meta http-equiv="Resource-Type" content="document" >

		<link rel="stylesheet" type="text/css" href="theme.css" media="all" />
		<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
		<script type="text/javascript">
			var sBasePath ="ckeditor/";
			CKEDITOR.on( 'instanceReady', function( ev ) {
					
		});
		</script>
	</head>

<body>
<?php if($op=='admin') { ?>

<!-- adminFrame  --------------------------------------------------->
<div id='div-adminframe'>
<fieldset>
<legend><?=_CONFIGURA; ?><?=$msg ?></legend>
<form name="admin" action="index.php?op=saveadmin" method="post"  >

<?=_NOME_SITO; ?><input type='entry' name='sitename'  size='80' value='<?=@$admin[3];?>'><br/>
headers  <input type='entry' name='headers' size='120'  value='<?=@$admin[4];?>'><br/>

<?=_EMAIL; ?> <input type='entry' name='email' value='<?=@$admin[2];?>'><?=_CONFERMA_EMAIL; ?> <input type='entry' name='confemail' value='<?=@$admin[2];?>'><br/>

<?=_PASSWORD; ?>  <input type='password' name='password'> <?=_CONFERMA_PASSWORD; ?><input type='password' name='confpassword'> <input type='checkbox' name='sendpassword' ><?=_INVIA_PASSWORD; ?><br/>

<p align='right'><input type='submit'  value='<?=_SALVA; ?>'  /></p>  
</form>
</fieldset>
<br/>
<fieldset>
<legend><b> <?= _PERMESSI; ?></b></legend>
<br/>
<?php
if ( fcheck ("pages") ) 
	echo ""._CARTELLA_SCRIVIBILE ;  
else 	
	echo ""._CARTELLA_NON_SCRIVIBILE; 

echo "<br/><br/>\n";
echo "<table>";
echo "<tr><td><b>"._PAGE."</b></td><td><b>"._DATE."</b><td><b>"._SIZE."</b></td><td><b>"._VEDI."</b></td><td><b>"._ELIMINA."</b></td><td><b>"._STATO."</b></td></tr>";
$fd=opendir("pages/");
while (false !== ($nf= readdir($fd))){
	if (substr_count($nf,".")==0){
		echo "<tr><td>$nf</td>";
		echo"<td><i>".date("j/m/y h:i", filemtime("pages/$nf"))."<i></td>\n";
		echo"<td><i>". filesize("pages/$nf")."<i></td>\n";
		echo"<td>[<a href='pages/$nf' target='new'>"._VEDI."</a>]</td>";
		echo"<td>[<a href='?op=delete&page=$nf'  onclick=\"if (confirm('cancellare'))return true; else return false;\">"._ELIMINA."</a>]</td>";
		if (is_writable("pages/$nf") )
			echo"<td>"._SCRIVIBILE ." </td></tr>\n";
		else 
			echo "<td>". _NON_SCRIVIBILE."</td></tr>\n";
		
	}
 }
closedir($fd);
echo "</table>";
?>
<hr/>
<div align='right'>
<form action="index.php?op=upload" method="post" enctype="multipart/form-data">
<input name="myfile" type="file"  />
<input type="submit" value="carica" />
</form>
</div>
</fieldset><br/>
<div align='right'>
<a href='?page=<?=$page ?>'><input type='button' value='<?=_ESCI; ?>' /></a>
</div>
</div>
<?php } ?>
<!-------------------------------------------------------------------------->


<!-- Toolbar --------------------------------------------------------->

<?php if($op=='edit') { ?>
<div id='div-toolbar' style="position: fixed; top:0px;width:99%;">
<form name="base" action="index.php?op=save&page=<?=$page?>" method="post"  >
<div id="mytoolbar" style=" width: 100%;"></div>
</div>
<?php }else{ ?>
<div id='div-toolbar' >
	<?php  if (S_COOKIE('admin')!="" && $op!='logout' ) { ?>
<form action="index.php?op=edit&page=<?=$page?>" method='post' > 
<p align='right'> <b><?=$admin[3] ?></b> 
<?=$msg." " ?>
<a href="index.php?op=admin&page=<?=$page?>"><?=_ADMIN; ?></a>
<a href='index.php?op=logout'><?=_LOGOUT ?></a>
<button>edit</button></p></form>
</div> 
<?php }else{ ?>
<?php if ($op=='auth'){ ?>
<div id='div-toolbar' >
<form action="index.php?op=login&page=<?=$page?>" method='post' > 
<p align='right'> <b><?=$admin[3]; ?> </b> 
<?=$msg." " ?>
<input type='password' name ='password'   />
<button><?=_LOGIN; ?></button>
</p>
</form> 
</div>
<?php } ?>
<?php } ?>
<?php } ?>
<!-------------------------------------------------------------------------->


<!-- Title --------------------------------------------------------------->
<div id='div-title' <?php if($op=='edit') echo "style=\"margin-top:132px;\" "; ?>>
<?php if($op=='edit') { ?>
<textarea name="title" id="title" >
<?php echo filter((@join(@file("pages/title")))); ?>
</textarea> 
<?php }else{ ?>
<?php echo filter((@join(@file("pages/title")))); ?>
<?php } ?>
</div>
<!-------------------------------------------------------------------------->

<!-- sidebar ----------------------------------------------------------->
<div id='div-sidebar'>
<?php if($op=='edit') { ?>
<textarea name="sidebar" id="sidebar" >
<?php echo filter((@join(@file("pages/sidebar")))); ?>
</textarea> 
<?php }else{ ?>
<?php echo filter((@join(@file("pages/sidebar")))); ?>
<?php } ?>
</div>
<!--------------------------------------------------------------------------->

<!-- textpage --------------------------------------------------------------->
<div id='div-textpage'>
<?php if($op=='edit') { ?>
<textarea name="textpage" id="textpage" >
<?php echo filter((@join(@file("pages/$page"))));  ?>
</textarea><?php }else{ ?>
<?php echo filter((@join(@file("pages/$page")))); ?>
<?php } ?>
</div>
<!--------------------------------------------------------------------------->


<!-- Footer ------------------------------------------------------------->
<div id='div-footer'>
<?php if($op=='edit') { ?>
<textarea name="footer" id="footer" >
<?php echo filter((@join(@file("pages/footer")))); ?>
</textarea> 
<?php }else{ ?>
<?php echo filter((@join(@file("pages/footer")))); ?>
<?php } ?>
</div>
<!--------------------------------------------------------------------------->
<div id='mybottom' ></div>

<?php if($op=='edit') { ?>
</form>
<?php } ?>
<?php if($op=='edit') { ?>
<script type="text/javascript"> 
	CKEDITOR.replace( 'title',{
		sharedSpaces :
		{
			top : 'mytoolbar',
			bottom : 'mybottom'
		},
		height : '80',	
		removePlugins : 'maximize,resize'
	} );  
	
	CKEDITOR.replace( 'sidebar' ,{
		sharedSpaces :
		{
			top : 'mytoolbar',
			bottom : 'mybottom'
		},
		height : '100%',
		removePlugins : 'maximize,resize'
	});
	CKEDITOR.replace( 'textpage',{
		sharedSpaces :
		{
			top : 'mytoolbar',
			bottom : 'mybottom'
		},
		height : '80%',
		removePlugins : 'maximize,resize'
	} );
	CKEDITOR.replace( 'footer',{
		sharedSpaces :
		{
			top : 'mytoolbar',
			bottom : 'mybottom'
		},
		height : '50',			
		removePlugins : 'maximize,resize'
			} ); 
	</script>
<?php } ?>
</body>
</html>
