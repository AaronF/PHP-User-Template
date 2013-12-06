<?php
function createWhereParams($array) {
	$whereParamsPre = array();
	foreach ($array as $key => $value) {
		$whereParamsPre[] = $key." = :".strtolower($key);
	}
	$whereParams = implode(' AND ',$whereParamsPre);
	return $whereParams;
}

function createFieldParams($array) {
	$fieldParamsPre = array();
	foreach ($array as $key => $value) {
		$fieldParamsPre[] = $key;
	}
	$fieldParams = "(".implode(', ',$fieldParamsPre).")";
	return $fieldParams;
}

function createValueParams($array) {
	$valueParamsPre = array();
	foreach ($array as $key => $value) {
		$valueParamsPre[] = ":".strtolower($key);
	}
	$valueParams = "(".implode(', ',$valueParamsPre).")";
	return $valueParams;
}

function createSetParams($array){
	$updateParamsPre = array();
	foreach ($array as $key => $value) {
		$updateParamsPre[] = $key." = :".strtolower($key);
	}
	$updateParams = implode(' ,',$updateParamsPre);
	return $updateParams;
}

class Data {
	public function getData($table, $field, $params, $endparams=NULL){	
		global $pdo_db, $db_table_prefix;

		if(!empty($params)) {
			$whereParams = " WHERE ".createWhereParams($params);
		} else {
			$whereParams = '';
		}

		$getdata = $pdo_db->prepare("SELECT ".$field." FROM ".$table." ".$whereParams."  ".$endparams." ");
		foreach ($params as $key => $value) {
			$getdata->bindParam(":".strtolower($key)."", $value);
		}
		$getdata->execute();
		$getdata->setFetchMode(PDO::FETCH_ASSOC); 

		if($getdata->rowCount() > 0) {
			$getdata = $getdata->fetchAll();
			return $getdata;
		} else {
			return false;
		}
	}

	public function insertData($table, $field, $params) {
		global $pdo_db, $db_table_prefix;

		$fieldParams = createFieldParams($params);
		$valueParams = createValueParams($params);

		$insertdata = $pdo_db->prepare("INSERT INTO ".$table." ".$fieldParams." VALUES ".$valueParams." ");
		foreach ($params as $key => $value) {
			$insertdata->bindValue(strtolower($key), $value);
		}
		// $insertdata->execute();
		$return = $insertdata->execute();
		if($return){
			return true;
		} else {
			return false;
		}
	}

	public function updateData($table, $field, $params, $whereparams) {
		global $pdo_db, $db_table_prefix;
		$set = createSetParams($params);
		$where = createWhereParams($whereparams);

		$updatedata = $pdo_db->prepare("UPDATE ".$table." SET ".$set." WHERE ".$where." ");
		foreach ($params as $key => $value) {
			$updatedata->bindValue(strtolower($key), $value);
		}
		foreach ($whereparams as $wherekey => $wherevalue) {
			$updatedata->bindValue(strtolower($wherekey), $wherevalue);
		}
		$updatedata->execute();
		$return = $updatedata->execute();
		if($return){
			return true;
		} else {
			return false;
		}
	}

	public function deleteData($table, $field, $params, $whereparams) {
		global $pdo_db, $db_table_prefix;
		$where = createWhereParams($whereparams);

		$deletedata = $pdo_db->prepare("DELETE FROM ".$table." WHERE ".$where." ");
		foreach ($whereparams as $key => $value) {
			$deletedata->bindValue(strtolower($key), $value);
		}
		$deletedata->execute();
		$return = $deletedata->execute();
		if($return){
			return true;
		} else {
			return false;
		}
	}
}
?>