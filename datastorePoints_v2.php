<?php
	$filename = $_POST['filename'];
	$data = $_POST['data'];

	$fp = fopen('data/output/'.$filename.'.geojson', 'w');
	fwrite($fp, $data);
	fclose($fp);
	echo "Data successfully written to GeoJSON File at $filename .geojson";
?>