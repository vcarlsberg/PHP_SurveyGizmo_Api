<?php namespace SurveyGizmo;
use SurveyGizmo\ApiResource;
use SurveyGizmo\iBaseInterface;
class Response extends ApiResource implements iBaseInterface{

	static $path = "/survey/{survey_id}/surveyresponse";
 	// static $regex_pattern = '/\\[(question|url|variable|calc|comment)\\(("(?:\\\\"|[^"])+"|\d+)\\)(?:\\s?,\\s?option\\(("(?:\\\\"|[^"]){0,}"|\d{0,})\\))?(?:\\s?,\\s?question_pipe\\(("(?:\\\\"|[^"]){0,}"|\d{0,})\\))?(?:\\s?,\\s?page_pipe\\(("(?:\\\\"|[^"]){0,}"|\d{0,})\\))?\\]/';
	public function save(){
		return parent::_save();
	}
	public static function get($id){
		return parent::_get(get_class($this),$id);
	}
	public function delete(){
		return parent::_delete();
	}

	public static function fetch($filters=null, $options=null){
		if(!$options['survey_id']){
			return new SurveyGizmoException(SurveyGizmoException::NOT_SUPPORTED);
		}
		self::setPath($options);
		$path = self::getPath();
		$response = parent::_makeRequest($path, $filter);
		if(isset($response)){
			$type = get_class($this);
			$response->data = self::_parseResponse($type,$response->data);
			return $response;
		}else{
			return null;
		}
	}

	//helpers
	private static function _parseResponse($type, $data){
		$return = array();
		if(is_array($data)){
			foreach ($data as $item) {
				$obj = self::_formatData($type, $item);
				$return[] = $obj;
			}
		}
		return $return;
	}

	private static function _formatData($type, $item){
		$obj = new $type;
		foreach ($item as $property => $value) {
			if($property == 'survey_data'){
				$obj->$property = json_decode(json_encode($value),1);
			}
			else{
		   		$obj->$property = $value;
		   	}
		}
		return $obj;
	}

	public static function getPath($append = ""){
		return parent::_getPath(self::$path,$append);
	}

	private static function setPath($options){
		self::$path = parent::_mergePath(self::$path,$options);
	}
}
?>