<?php

require_once './DB.php';

class Hypersistence{
	
	public static $map;
	
	private $loaded = false;
	
	const MANY_TO_ONE = 1;
	const ONE_TO_MANY = 2;
	const MANY_TO_MANY = 3;
	
	private static $TAG_TABLE = 'table';
	private static $TAG_JOIN_COLUMN = 'joinColumn';
	private static $TAG_COLUMN = 'column';
	private static $TAG_INVERSE_JOIN_COLUMN = 'inverseJoinColumn';
	private static $TAG_JOIN_TABLE = 'joinTable';
	private static $TAG_PRIMARY_KEY = 'primaryKey';
	private static $TAG_ITEM_CLASS = 'itemClass';

	public static function init($class){
		$refClass = new ReflectionClass($class);
		self::mapClass($refClass);
		return $refClass->name;
	}
	
	/**
	 * @param ReflectionClass $refClass
	 */
	private static function mapClass($refClass){
		if($refClass->name != 'Hypersistence'){
			if(!isset(self::$map[$refClass->name])){
				self::$map[$refClass->name] = array(
					self::$TAG_TABLE => self::getAnnotationValue($refClass, self::$TAG_TABLE),
					self::$TAG_JOIN_COLUMN => self::getAnnotationValue($refClass, self::$TAG_JOIN_COLUMN),
					'parent' => $refClass->getParentClass()->name,
					'class' => $refClass->name,
					'properties' => array()
				);
				
				$properties = $refClass->getProperties();
				foreach ($properties as $p){
					if($p->class == $refClass->name){
						$col = self::getAnnotationValue($p, self::$TAG_COLUMN);
						$relType = self::getRelType($p);
						$pk = self::is($p, self::$TAG_PRIMARY_KEY);
						if(!is_null($col) || $relType[0] || $pk){
							self::$map[$refClass->name]['properties'][$p->name] = array(
								'var' => $p->name,
								self::$TAG_COLUMN => $col ? $col : $p->name,
								self::$TAG_PRIMARY_KEY => $pk,
								'relType' => $relType[0],
								'loadType' => $relType[1],
								self::$TAG_JOIN_COLUMN => self::getAnnotationValue($p, self::$TAG_JOIN_COLUMN),
								self::$TAG_ITEM_CLASS => self::getAnnotationValue($p, self::$TAG_ITEM_CLASS),
								self::$TAG_JOIN_TABLE => self::getAnnotationValue($p, self::$TAG_JOIN_TABLE),
								self::$TAG_INVERSE_JOIN_COLUMN => self::getAnnotationValue($p, self::$TAG_INVERSE_JOIN_COLUMN)
							);
						}
					}
				}
				
			}
			self::mapClass($refClass->getParentClass());
		}
		
	}
	
	/**
	 * @param ReflectionObject $reflection
	 */
	private static function getAnnotationValue($reflection, $annotation){
		$refComments = $reflection->getDocComment();
		if(preg_match('/@'.$annotation.'[ \t]*\([ \t]*([a-zA-Z_0-9]+)?[ \t]*\)/', $refComments, $matches)){
			if(isset($matches[1])){
				return trim($matches[1]);
			}
			return '';
		}
		return null;
	}
	/**
	 * @param ReflectionObject $reflection
	 */
	private static function is($reflection, $annotation){
		$refComments = $reflection->getDocComment();
		if(preg_match('/@'.$annotation.'/', $refComments)){
			return true;
		}
		return false;
	}
	
	/**
	 * @param ReflectionObject $reflection
	 */
	private static function getRelType($reflection){
		$type = self::getAnnotationValue($reflection, 'manyToOne');
		if($type) return array(self::MANY_TO_ONE, $type);
		$type = self::getAnnotationValue($reflection, 'oneToMany');
		if($type) return array(self::ONE_TO_MANY, $type);
		$type = self::getAnnotationValue($reflection, 'manyToMany');
		if($type) return array(self::MANY_TO_MANY, $type);
		return array(0, null);
	}
	
	public static function getPk($className){
		if($className){
			$i = 0;
			while($className != 'Hypersistence'){
				foreach (self::$map[$className]['properties'] as $p){
					if($p[self::$TAG_PRIMARY_KEY]){
						$p['i'] = $i;
						$p[self::$TAG_TABLE] = self::$map[$className][self::$TAG_TABLE];
						return $p;
					}
				}
				$className = self::$map[$className]['parent'];
				$i++;
			}
		}
		return null;
	}

	/**
	 * @param boolean $forceReload
	 * @return \Hypersistence
	 */
	public function load($forceReload = false){
		if(!$forceReload && $this->loaded){
			return $this;
		}
		$classThis = self::init($this);
		
		$tables = array();
		$joins = array();
		$bounds = array();
		$fields = array();
		
		$aliases = 'abcdefghijklmnopqrstuvwxyz';
		
		$class = $classThis;
		
		if($pk = self::getPk($class)){
			$get = 'get'.$pk['var'];
			$joins[] = $aliases[$pk['i']].'.'.$pk[self::$TAG_COLUMN].' = '.':'.$aliases[$pk['i']].'_'.$pk[self::$TAG_COLUMN];
			$bounds[':'.$aliases[$pk['i']].'_'.$pk[self::$TAG_COLUMN]] = $this->$get();
		}
		
		
		$i = 0;
		while ($class != 'Hypersistence'){
			$alias = $aliases[$i];
			$tables[] = self::$map[$class][self::$TAG_TABLE].' '.$alias;
			
			if(self::$map[$class]['parent'] != 'Hypersistence'){
				$parent = self::$map[$class]['parent'];
				$pk = self::getPk(self::$map[$parent]['class']);
				$joins[] = $alias.'.'.self::$map[$class][self::$TAG_JOIN_COLUMN].' = '.$aliases[$i + 1].'.'.$pk[self::$TAG_COLUMN];
			}
			
			foreach (self::$map[$class]['properties'] as $p){
				if($p['relType'] != self::MANY_TO_MANY && $p['relType'] != self::ONE_TO_MANY){
					$fields[] = $alias.'.'.$p[self::$TAG_COLUMN].' as '.$alias.'_'.$p[self::$TAG_COLUMN];
				}
			}
			
			$class = self::$map[$class]['parent'];
			$i++;
		}
		
		$sql = 'select '.implode(',', $fields).' from '.  implode(',', $tables).' where '.  implode(' and ', $joins);
		
		if($stmt = DB::getDBConnection()->prepare($sql)){
			
			if ($stmt->execute($bounds) && $stmt->rowCount() > 0) {
				$this->loaded = true;
                $result = $stmt->fetchObject();
				$class = $classThis;
				$i = 0;
				while ($class != 'Hypersistence'){
					$alias = $aliases[$i];
					foreach (self::$map[$class]['properties'] as $p){
						$var = $p['var'];
						$set = 'set'.$var;
						if($p['relType'] != self::MANY_TO_MANY && $p['relType'] != self::ONE_TO_MANY){
							$column = $alias.'_'.$p[self::$TAG_COLUMN];
							if(isset($result->$column)){
								if(method_exists($this, $set)){
									if($p['relType'] == self::MANY_TO_ONE){
										$objClass = $p[self::$TAG_ITEM_CLASS];
										self::init($objClass);
										$pk = self::getPk($objClass);
										if($pk){
											$objVar = $pk['var'];
											$objSet = 'set'.$objVar;
											$obj = new $objClass;
											$obj->$objSet($result->$column);
											$this->$set($obj);
											if($p['loadType'] == 'eager'){
												$obj->load();
											}
										}
									}else{
										$this->$set($result->$column);
									}
								}
							}
						}else{
							if($p['relType'] != self::ONE_TO_MANY){
								$objClass = $p[self::$TAG_ITEM_CLASS];
								self::init($objClass);
								$obj = new $objClass;
								
								$this->$set($obj->search());
							}
						}
					}

					$class = self::$map[$class]['parent'];
					$i++;
				}
			}
			
		}
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function delete(){
		
		$classThis = self::init($this);
		
		$tables = array();
		$joins = array();
		$bounds = array();
		
		$class = $classThis;
		
		if($pk = self::getPk($class)){
			$get = 'get'.$pk['var'];
			$joins[] = $pk[self::$TAG_TABLE].'.'.$pk[self::$TAG_COLUMN].' = '.':'.$pk[self::$TAG_TABLE].'_'.$pk[self::$TAG_COLUMN];
			$bounds[':'.$pk[self::$TAG_TABLE].'_'.$pk[self::$TAG_COLUMN]] = $this->$get();
		}
		
		$i = 0;
		while ($class != 'Hypersistence'){
			$tables[] = self::$map[$class][self::$TAG_TABLE];
			$table = self::$map[$class][self::$TAG_TABLE];
			$parent = self::$map[$class]['parent'];
			if($parent != 'Hypersistence'){
				$pk = self::getPk(self::$map[$parent]['class']);
				$joins[] = $table.'.'.self::$map[$class][self::$TAG_JOIN_COLUMN].' = '.self::$map[$parent][self::$TAG_TABLE].'.'.$pk[self::$TAG_COLUMN];
			}
			
			$class = self::$map[$class]['parent'];
			$i++;
		}
		
		$sql = 'delete '.implode(',', $tables).' from '.implode(',', $tables).' where '.  implode(' and ', $joins);
		
		if($stmt = DB::getDBConnection()->prepare($sql)){
			if ($stmt->execute($bounds)) {
				return true;
			}
		}
		return false;
	}
	
	public function save(){
		
		$classThis = self::init($this);
		
		$classes = array();
		
		$class = $classThis;

		$pk = self::getPk($class);
		$get = 'get'.$pk['var'];
		$id = $this->$get();
		
		$new = is_null($id);
		
		while ($class != 'Hypersistence'){
			$classes[] = $class;
			$class = self::$map[$class]['parent'];
		}
		$classes = array_reverse($classes);
		foreach ($classes as $class){
			$bounds = array();
			$fields = array();
			$sql = '';
			
			if(!$new){//UPDATE
				$where = $pk[self::$TAG_COLUMN].' = :'.$pk[self::$TAG_COLUMN];
				$bounds[':'.$pk[self::$TAG_COLUMN]] = $id;
				
				$properties = self::$map[$class]['properties'];
				
				foreach ($properties as $p){
					if($p[self::$TAG_COLUMN] != $pk[self::$TAG_COLUMN] && $p['relType'] != self::MANY_TO_MANY && $p['relType'] != self::ONE_TO_MANY){
						$get = 'get'.$p['var'];
						$fields[] = $p[self::$TAG_COLUMN].' = :'.$p[self::$TAG_COLUMN];
						if($p['relType'] == self::MANY_TO_ONE){
							$obj = $this->$get();
							if($obj && $obj instanceof Hypersistence){
								$objClass = $p[self::$TAG_ITEM_CLASS];
								self::init($objClass);
								$objPk = self::getPk($objClass);
								$objGet = 'get'.$objPk['var'];
								$bounds[':'.$p[self::$TAG_COLUMN]] = $obj->$objGet();
							}else{
								$bounds[':'.$p[self::$TAG_COLUMN]] = null;
							}
						}else{
							$bounds[':'.$p[self::$TAG_COLUMN]] = $this->$get();
						}
					}
				}
				
				if(count($fields)){
					$sql = 'update '.self::$map[$class][self::$TAG_TABLE].' set '.implode(',', $fields).' where '.$where;
				}
			}else{//INSERT
				$values = array();
				$properties = self::$map[$class]['properties'];
				
				$joinColumn = self::$map[$class][self::$TAG_JOIN_COLUMN];
				if($joinColumn){
					$fields[] = $joinColumn;
					$values[] = ':'.$joinColumn;
					$bounds[':'.$joinColumn] = $id;
				}
				
				foreach ($properties as $p){
					if($p['column'] != $pk['column'] && $p['relType'] != self::MANY_TO_MANY && $p['relType'] != self::ONE_TO_MANY){
						$get = 'get'.$p['var'];
						$fields[] = $p[self::$TAG_COLUMN];
						$values[] = ':'.$p[self::$TAG_COLUMN];
						if($p['relType'] == self::MANY_TO_ONE){
							$obj = $this->$get();
							if($obj && $obj instanceof Hypersistence){
								$objClass = $p[self::$TAG_ITEM_CLASS];
								self::init($objClass);
								$objPk = self::getPk($objClass);
								$objGet = 'get'.$objPk['var'];
								$bounds[':'.$p[self::$TAG_COLUMN]] = $obj->$objGet();
							}else{
								$bounds[':'.$p[self::$TAG_COLUMN]] = null;
							}
						}else{
							$bounds[':'.$p[self::$TAG_COLUMN]] = $this->$get();
						}
					}
				}
				
				if(count($fields)){
					$sql = 'insert into '.self::$map[$class][self::$TAG_TABLE].' ('.implode(',', $fields).') values ('.implode(',', $values).')';
				}
				
			}
			var_dump($sql);
			if($sql != ''){
				if($stmt = DB::getDBConnection()->prepare($sql)){
					if($stmt->execute($bounds)){
						if($new){
							$lastId = DB::getDBConnection()->lastInsertId();
							if($lastId){
								$id = $lastId;
							}
						}
					}else{
						var_dump($stmt->errorInfo());
						return false;
					}
				}else{
					var_dump($stmt->errorInfo());
					return false;
				}
			}
			
		}
		return true;
	}
	
	public function search(){
		return new HypersistenceResultSet($this);
	}
	
}

class HypersistenceResultSet{
	
	private $object;
	
	private $resultList;
	
	public function __construct($object) {
		$this->object = $object;
	}
	
	public function execute(){
		$this->resultList = array();
		$classThis = Hypersistence::init($this->object);
		
		$tables = array();
		$bounds = array();
		$fields = array();
		$filters = array();
		
		$aliases = 'abcdefghijklmnopqrstuvwxyz';
		
		$class = $classThis;
		
		$i = 0;
		while ($class != 'Hypersistence'){
			$alias = $aliases[$i];
			$tables[] = Hypersistence::$map[$class]['table'].' '.$alias;
			
			if(Hypersistence::$map[$class]['parent'] != 'Hypersistence'){
				$parent = Hypersistence::$map[$class]['parent'];
				$pk = Hypersistence::getPk(Hypersistence::$map[$parent]['class']);
				$filters[] = $alias.'.'.Hypersistence::$map[$class]['joinColumn'].' = '.$aliases[$i + 1].'.'.$pk['column'];
			}
			
			foreach (Hypersistence::$map[$class]['properties'] as $p){
				if($p['relType'] != Hypersistence::MANY_TO_MANY && $p['relType'] != Hypersistence::ONE_TO_MANY){
					$fields[] = $alias.'.'.$p['column'].' as '.$alias.'_'.$p['column'];
					
					$get = 'get'.$p['var'];
					$value = $this->object->$get();
					if(!is_null($value)){
						$filters[] = $alias.'.'.$p['column'].' = :'.$alias.'_'.$p['column'];
						if($value instanceof Hypersistence){
							$objClass = $p['itemClass'];
							Hypersistence::init($objClass);
							$objPk = Hypersistence::getPk($objClass);
							$objGet = 'get'.$objPk['var'];
							$bounds[':'.$alias.'_'.$p['column']] = $value->$objGet();
						}else{
							$bounds[':'.$alias.'_'.$p['column']] = $value;
						}
					}
					
				}
			}
			
			$class = Hypersistence::$map[$class]['parent'];
			$i++;
		}
		
		if(count($filters))
			$where = ' where '.  implode(' and ', $filters);
		else
			$where = '';
		
		$sql = 'select '.implode(',', $fields).' from '.  implode(',', $tables).$where;
		
		if($stmt = DB::getDBConnection()->prepare($sql)){
			
			if ($stmt->execute($bounds) && $stmt->rowCount() > 0) {
				
                while($result = $stmt->fetchObject()){
					$class = $classThis;
					$object = new $class;
					$i = 0;
					while ($class != 'Hypersistence'){
						$alias = $aliases[$i];
						foreach (Hypersistence::$map[$class]['properties'] as $p){
							if($p['relType'] != Hypersistence::MANY_TO_MANY && $p['relType'] != Hypersistence::ONE_TO_MANY){
								$column = $alias.'_'.$p['column'];
								if(isset($result->$column)){
									$var = $p['var'];
									$set = 'set'.$var;
									if(method_exists($object, $set)){
										if($p['relType'] == Hypersistence::MANY_TO_ONE){
											$objClass = $p['itemClass'];
											Hypersistence::init($objClass);
											$pk = Hypersistence::getPk($objClass);
											if($pk){
												$objVar = $pk['var'];
												$objSet = 'set'.$objVar;
												$obj = new $objClass;
												$obj->$objSet($result->$column);
												$object->$set($obj);
												if($p['loadType'] == 'eager'){
													$obj->load();
												}
											}
										}else{
											$object->$set($result->$column);
										}
									}
								}
							}
						}

						$class = Hypersistence::$map[$class]['parent'];
						$i++;
					}
					$this->resultList[] = $object;
				}
			}
			
		}
		return $this->resultList;
		
	}
	
}