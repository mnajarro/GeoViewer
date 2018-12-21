<?php

ini_set('memory_limit', '1024M');
ini_set('max_input_vars', 3000);
	//Bring in variables from client
	
	$csvdata = $_POST['csvdata'];
	$filename = $_POST['filename'];
	$selectedID = $_POST['selectedID'];
	$bjoinID= $_POST['bjoinID'];
	$boundname= $_POST['boundname'];
	
	
	$boundaries = file_get_contents('data/'.$boundname);
	
	
	$chopbound = json_decode(str_replace("var admbdy=","",$boundaries), true);
	
	$match = array();
	$count = 0; 
	$matchcount = 0; 
	$matchedRow = array();
	$itemMatch = 0;
	
	$len = count($csvdata);
	$features2 = array();
	
	$BndCnt = count($chopbound['features']);
	$z = 0; 
	$emptyRows = 0;
	
	for($x = 0; $x < $len; $x++){
		
		$CSVMatchVal = isset($csvdata[$x][$selectedID]) ? $csvdata[$x][$selectedID] : '-9999';
		if($CSVMatchVal=='-9999'||$CSVMatchVal==''||$CSVMatchVal=='#N/A'){
			$emptyRows = $emptyRows +1; 
			continue; 
		}
		$CSVDataLine = $csvdata[$x];
		
			
		for($z = 0; $z < $BndCnt; $z++){
			
			$props = $chopbound; 
			
				if ($CSVMatchVal == $props['features'][$z]['properties'][$bjoinID]) {
					
					$itemMatch = 1;
					$matchcount = $matchcount +1; 
					array_push($matchedRow, $x);
							$CombinedProps = $props['features'][$z]['properties']+$csvdata[$x]; 
							 
							$props['features'][$z]['properties'] = $CombinedProps;
							$feat = $props['features'][$z]; 
								array_push($features2, $feat);
								break(1); 	
				}
				
		}
	}
	
	
	$finalfeatures= array("type"=>"FeatureCollection","features"=>$features2);
	
	
	echo "$len records processed from your CSV file.<br> $matchcount matches made.<br><br> "; 
	
	try {
	$fp = fopen('data/output/'.$filename.'.geojson', 'w');
	} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	try {
	fwrite($fp, json_encode($finalfeatures));
	} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	try {
	fclose($fp);
	} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
		$CSVRows = count($csvdata);
		if ($CSVRows == 0) {
			echo "$len Failure! I'm sorry, no data was found in your CSV file. Please check your file and try again.";
	}	elseif ($matchcount == 0) {
			echo "Failure! I'm sorry, no matches were made between your data and the administrative boundaries. Please ensure you are selecting the proper join fields. Refer to this <a href='templates.html' target='_blank'>data template</a> for data structure help.";
	}	elseif ($CSVRows == $BndCnt && $CSVRows == $matchcount) {
			echo "Success! A 100% Match! All $CSVRows rows from your CSV file were joined with $BndCnt administrative boundaries";
	} 	
		elseif ($CSVRows == $matchcount && $CSVRows > $BndCnt) {
			echo "Notice: All $CSVRows rows in your CSV file were matched to administrative boundaries, however, your file contained more records than the number of administrative boundaries. As a result, some data will be hidden under other data.";
	} 	
		elseif ($CSVRows == $matchcount) {
			echo "Success! All $CSVRows rows in your CSV file were matched to administrative boundaries and written to $filename. The matching boundary areas will appear on the map. ";
	}
		elseif ($matchcount+$emptyRows == $CSVRows) {
			echo "Notice: $emptyRows rows were processed with blank join-field values. These records were not included in your dataset."; 
	} 	elseif ($CSVRows > $matchcount) {
			echo "Warning! Some data from your CSV file was successfully joined to administrative boundaries and will appear on the map, however there were some errors. \n\n<br><br>The following rows in your CSV file failed to join: \n<br>";


			$missing = array();
			for ($i = 0; $i < $CSVRows; $i++) {
				if (!in_array($i, $matchedRow)) $missing[] = $i;
			}
			
			foreach ($missing as $b){
				$RecNum = $b+2; 
				echo "Row $RecNum\n<br>";
			}
	} 	elseif ($CSVRows < $matchcount) {
			echo "Warning! There were more matches than records in your dataset. This indicates a non-1:1 relationship between your dataset and the administrative boundaries.";
	} 	else {
			echo "Data join complete. The system cannot tell if the join was successful. Please check the map. If an error has occurred, please contact the administrator. CSV Records: $CSVRows, Admin Boundaries: $BndCnt, Matches: $matchcount";
	}

	
?>