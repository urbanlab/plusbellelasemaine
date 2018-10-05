<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Import LD</title>
</head>

<body>

<h1>My Dance Company > Import LD</h1>


<?php
	if(isset($error)) { echo('<div style="color:#ff0000; margin:20px 0;">'.$error.'</div>'); }
?>

<?php
	echo form_open_multipart('utils/import/import_xlsx');
?> 

	<p>Séléctionner un fichier XLSX à charger</p>
	<input type="file" name="xlsxFile" />
	<br><br>
	<input type="submit" value="Importer" />
</form>

</body>
</html>
