<?php
namespace Hypersistence\Core;

class Statement extends \PDOStatement{
	
	public function execute($input_parameters = null){
	
		if(!is_null($input_parameters)){
			foreach ($input_parameters as $key => $param){
				if(is_array($param)){
					if(isset($param[1]) && $param[1] == \PDO::PARAM_INT){
						$this->bindValue($key, $param[0], \PDO::PARAM_INT);
					}else{
						$this->bindValue($key, $param[0]);
					}
				}else{
					$this->bindValue($key, $param);
				}
			}
		}
		return parent::execute();
	}
}