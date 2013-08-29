<?php namespace atws;
/*
 * Created on 27 Aug 2013
 * 
 */

require_once(__DIR__.DIRECTORY_SEPARATOR.'php-atws-classmap.php'); 

class atws {

	private $_connected = false;
	
	public function atws() {
		$this->soap_options['classmap'] = ClassMap::getClassMap();
	}

	public function connect($url,$username,$password) {
		$this->_connected = false;
		$this->_url = $url;
		$this->soap_options['login']=$username;
		$this->soap_options['password']=$password;
		$this->soap_options['location']=str_replace('wsdl','asmx',strtolower($url));
		
		return $this->_connect();
	}

	private function _connect() {
		if($this->_connected == true) {
			return true;
		}
		try {
			$this->client = new \soapclient($this->_url,$this->soap_options);
		}	
		catch ( SoapFault $fault ) {
			$this->last_connect_fault = $fault;
			$this->_connected = false;
			return false;
		}		
		if($this->client) {
			$this->_connected = true;
			return true;
		}
		$this->_connected = false;
		return false;
	}

    public function getObject($objectType) {
    	
    	return new $objectType();
    	
    }

	public function getPicklist( $entity, $picklist , $attempt = 1 ) {

		if (isset($this->picklists[$entity])) {
			if(isset($this->picklists[$entity][$picklist])) {
				// we only run one loop per entity resultset
				return $this->picklists[$entity][$picklist];
			}
		}
		$GetFieldInfo = new GetFieldInfo();
		$GetFieldInfo->psObjectType = $entity;
		try {
			$picklist_result = $this->client->getFieldInfo($GetFieldInfo);
		} 
		catch ( SoapFault $fault ) {
			$this->last_picklist_fault = $fault;
			if($attempt > 3 ) {
				return false;
			}
			else {
				$attempt++;
				return $this->getPicklist($entity , $picklist, $attempt);
			}
		}

		if (!is_array($picklist_result->GetFieldInfoResult->Field)) {
			return false;
		}
		foreach ($picklist_result->GetFieldInfoResult->Field as $field) {
			if(!empty($field->IsPickList)) {
				if($field->IsPickList == true) {
					$current_picklist = $field->Name;
					if (isset($field->PicklistValues->PickListValue)) {
						if (!empty($field->PicklistValues->PickListValue)) {
							foreach ($field->PicklistValues->PickListValue as $picklist_value) {
								if (is_object($picklist_value)) {
									if(isset($picklist_value->Value) && isset($picklist_value->Label)) {
										$this->picklists[$entity][$current_picklist][$picklist_value->Value]=$picklist_value->Label;
									}
								}
							}
						}
					}
				}
			}
		}
		if(isset ($this->picklists[$entity][$picklist]) ) {
			return $this->picklists[$entity][$picklist];	
		}
		return false;
	}

	public function getAvailablePicklists($entity) {
		if(!isset($this->picklists[$entity])) {
			$this->getPicklist($entity,'DummyPicklist');
		}
		if(!isset($this->picklists[$entity])) {
			// some kind of error
			return false;
		}
		foreach($this->picklists[$entity] as $picklist=>$value) {
			$result[]=$picklist;
		}
		return $result;
	}

	public function getNewQuery() {
		return new atwsquery();
	}
	
	public function query($query,$attempt = 1) {
		if ($attempt > 3) {
			// We have had two attempts and not got it.
			// try last time
			return false;
		}
		$ATWSQuery = $this->getObject('Query');
		if (is_object($query)) {
		    if (is_a($query, 'atwsquery')) {
		    	$ATWSQuery->sXML=$query->getQueryXml();
		    }
		}
		else {
			// treat as passed xml
			$ATWSQuery->sXML = $query;
		}

		$this->_connect();
		try {
				$result = $this->client->query($ATWSQuery);
		}
		catch ( SoapFault $fault ) {
			// disconnect and retry up to three times
			// the api servers sometimes just timeout,
			// so we shouldn't let this break our app
			$this->_connected = false;
			// allows downstream logging
			$this->last_query_fault = $fault;
			$this->last_query_fault_xml = $ATWSQuery->sXML;
			return $this->query($ATWSQuery,$attempt++);
		}
		$this->last_query_xml=$ATWSQuery->sXML;
		return $result;
	}
	
	public function getQueryResults($queryOrResults) {
		if (is_a($queryOrResults,'atwsquery')) {
			return $this->getQueryResults($this->query($queryOrResults));
		}
		if(!isset($queryOrResults->queryResult->ReturnCode)) {
			// i'm not sure if this can happen or not!
			return false;
		}
		if($queryOrResults->queryResult->ReturnCode == -1) {
			// the query failed.  set the last query error array and return false
			if (isset($queryOrResults->queryResult->Errors->ATWSError)) {
				$this->last_query_error=array();
				if(is_array($queryOrResults->queryResult->Errors->ATWSError)) {
					foreach($queryOrResults->queryResult->Errors->ATWSError as $ATWSError) {
						$this->last_query_error[] = $ATWSError->Message;
					}					
				}
				else {
					$this->last_query_error[]=$queryOrResults->queryResult->Errors->ATWSError->Message;
				}
				$this->last_query_error_xml = $this->last_query_xml;
				return false;
			}
		}
		if (!isset($queryOrResults->queryResult->EntityResults->Entity)) {
			return false;
		}
		// return an array of entity objects
		if (is_array($queryOrResults->queryResult->EntityResults->Entity)) {			
			return 	$queryOrResults->queryResult->EntityResults->Entity;
		}
		else {
			return array($queryOrResults->queryResult->EntityResults->Entity);
		}
	}
	
}

class atwsquery {
    //pretty much managed to make building atws queries like sql

    
    // these are for ide autofill
    public $Equals='Equals';
    public $NotEqual='NotEqual';
    public $GreaterThan='GreaterThan';
    public $LessThan='LessThan';
    public $GreaterThanorEquals='GreaterThanorEquals';
    public $LessThanOrEquals='LessThanOrEquals';
    public $BeginsWith='BeginsWith';
    public $EndsWith='EndsWith';
    public $Contains='Contains';
    public $IsNotNull='IsNotNull';
    public $IsNull='IsNull';
    public $IsThisDay='IsThisDay';
    public $Like='Like';
    public $NotLike='NotLike';
    public $SoundsLike='SoundsLike';

	private $_spaces = 3;

    public function qFROM($entity){
        $this->_entity=$entity;
    }

    public function qWHERE($field_name,$field_condition,$field_value,$udf=false) {
        $this->_addFieldCriteria($field_name, $field_condition, $field_value,$udf);
    }
    public function qOR($field_name,$field_condition,$field_value,$udf=false){
    	$this->_startNestedCondition('OR');
        $this->_addFieldCriteria($field_name, $field_condition, $field_value,$udf);
        $this->_endNestedCondition();
    }
    public function qAND($field_name,$field_condition,$field_value,$udf=false) {
        $this->_startNestedCondition();
        $this->_addFieldCriteria($field_name, $field_condition, $field_value,$udf);
        $this->_endNestedCondition();
    }
    public function openBracket() {
        $this->_operations[]=array('TYPE'=>'BRACKET','STATUS'=>true);
    }
    public function closeBracket() {
        $this->_operations[]=array('TYPE'=>'BRACKET','STATUS'=>false);
    }

    public function reset() {
        $this->_operations=array();
        $this->_spaces=3;
        $this->_xml="";
        $this->_entity="";
    }

 
    public function atwsquery() {
		$this->reset();
    }

    private function _addFieldCriteria($name,$condition,$value,$udf=false) {
        
        if(is_object($value)) {
        	if(get_class($value) == "DateTime") {
        		// automatically convert to the correct timestamp format
        		// for the api
        		$value = $this->formatDateStamp($value);
        	}
        }
        
        $this->_operations[]=array(
			'TYPE'=>'FIELD',
			'NAME'=>$name,
			'CONDITION'=>$condition,
			'VALUE'=>$value,
			'UDF'=>$udf
			);
    }
            
    private function _startNestedCondition($operator='AND') {
        
        $this->_operations[]=array('TYPE'=>'NEST','OPERATOR'=>$operator,'STATUS'=>true);    	
    }


    private function _endNestedCondition() {
        
        $this->_operations[]=array('TYPE'=>'NEST','STATUS'=>false);
    }

        
    public function getQueryXml() {
    	$this->_buildXml();
        $xml=$this->_xml;
        $this->_xml="";
        return $xml;
    }

    
    private function _buildXml() {
        foreach($this->_operations as $operation) {
        	$function='_build'.$operation['TYPE'];
            $this->$function($operation);
        }
        $qxml=$this->_xml;
        $this->_xml="<queryxml>\n <entity>$this->_entity</entity>\n  <query>$qxml\n  </query>\n</queryxml>";
    	
    }

        
    private function _buildFIELD($operation) {
        $name=$operation['NAME'];
        $condition=$operation['CONDITION'];
        $value=$operation['VALUE'];
        $udf=$operation['UDF'];
        $fspacer=str_repeat(" ",$this->_spaces);
        $espacer=str_repeat(" ",$this->_spaces +1);

        if ($udf === true){
            $udf=" udf='true'";
        }
        else {
            $udf="";
        }
        
        $exml="$espacer<expression op='$condition'>$value</expression>";
        $fxml="\n$fspacer<field$udf>$name\n$exml\n$fspacer</field>";
        $this->_xml.=$fxml;    	
    	
    }


    private function _buildNEST($operation) {
        if ($operation['STATUS'] === false) {
            $this->_spaces--;
            $cspacer=str_repeat(" ",$this->_spaces);
            $this->_xml.="\n$cspacer</condition>";        	
        }
        else {
            $cspacer=str_repeat(" ",$this->_spaces);
            $cxml="\n$cspacer<condition operator='${operation['OPERATOR']}'>";
            $this->_xml.=$cxml;
            $this->_spaces++; 	
        }
    }

    private function _buildBRACKET($operation) {
    	if ($operation['STATUS'] === false) {
            $this->_spaces--;
            $cspacer=str_repeat(" ",$this->_spaces);
            $this->_xml.="\n$cspacer</condition>";   		
    	}
		else {
            $cspacer=str_repeat(" ",$this->_spaces);
            $cxml="\n$cspacer<condition>";
            $this->_xml.=$cxml;
            $this->_spaces++;
		}
    }
	private function formatDateStamp($datetime=false) {
		// this function formats the date correctly for the api
		// date fields.
		// input is either datetime object or unixtimestamp
		
		// @todo: build in timezone adjustment, as the API is wonky
		if($datetime === false) {
			$datetime = new DateTime();
		}
		if(is_object($datetime)) {
			if(get_class($datetime) == "DateTime") {
				return $datetime->format("Y-m-d H:i:s");			
			}
		}
		if(is_int($datetime)) {
			//assume this is a unix timestamp
			return date('Y-m-d H:i:s',$datetime);
		}
	}
}
 
?>