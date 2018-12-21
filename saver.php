<?php

error_reporting(E_ALL);
ini_set('display_errors', True);

	$mapname= $_POST['setname'];
	$filename= $_POST['filename'];
	$layernameadmin= $_POST['layernameadm'];
	$OrderList= $_POST['OrderList'];
	$basemap= $_POST['basemap'];
	$mapid= $_POST['mapid'];
	$polycolor= $_POST['polycolor'];
	$multicolor= $_POST['multicolorsArray'];
	$zoomLat= $_POST['zoomLat'];
	$zoomLong= $_POST['zoomLong'];
	$zoomLevel= $_POST['zoomLevel'];
	$owner= $_POST['owner'];
	$ownerOrg= $_POST['ownerOrg'];
	$focusCountry= $_POST['focusCountry'];
	$about= $_POST['about'];
	
	$params = array("ID"=>$mapid, "mapname"=>$mapname, "filename"=>$filename, "layernameadmin"=>$layernameadmin, "OrderList"=>$OrderList, "basemap"=>$basemap, "polycolor"=>$polycolor, "multicolor"=>$multicolor, "zoomLat"=>$zoomLat, "zoomLong"=>$zoomLong, "zoomLevel"=>$zoomLevel, "owner"=>$owner, "ownerOrg"=>$ownerOrg, "focusCountry"=>$focusCountry, "about"=>$about);
	
	$jsonparams = file_get_contents('usersettings/templatesettings.json');
	$tempArray = json_decode($jsonparams);
	//print_r($tempArray);
	$ExistingMap = 0; 
	foreach ($tempArray as &$value) {
		$IDVal = $value->{'ID'};
		if ($IDVal == $mapid){
			echo "A matching ID has been found $IDVal <br>";
			$ExistingMap = 1; 
			
			$value->{"mapname"}=$mapname;
			$value->{"filename"}=$filename;
			$value->{"layernameadmin"}=$layernameadmin;
			$value->{"OrderList"}=$OrderList;
			$value->{"basemap"}=$basemap;
			$value->{"polycolor"}=$polycolor;
			$value->{"multicolor"}=$multicolor;
			$value->{"zoomLat"}=$zoomLat;
			$value->{"zoomLong"}=$zoomLong;
			$value->{"zoomLevel"}=$zoomLevel;
			$value->{"owner"}=$owner; 
			$value->{"ownerOrg"}=$ownerOrg;
			$value->{"focusCountry"}=$focusCountry;
			$value->{"about"}=$about;
		}
		
	}
	echo(error_get_last());
	if ($ExistingMap == 0){
	array_push($tempArray, $params);
	}
	$jsonData = json_encode($tempArray);
	file_put_contents('usersettings/templatesettings.json', $jsonData);
	
	echo "complete!";
	
?>