<?php

class Validator
{
	private static $_instance = null;
	
	public static function get_instance() 
	{
		if (self::$_instance === null) 
		{
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}

	// Singleton
	private function __construct() 
	{}

	// $rule => type required message
	public function validate($rules, $values)
	{
		foreach ($rules as $index => $rule)
		{
			// 没有对应的参数
			if (!isset($values[$index]))
			{
				// 必须传入，即返回false
				if ($rule[1])
				{
					return new ValidatorResult(false, '必须传入参数' . $index);
				}

				// 否则继续
				continue;
			}
			
			$fn_name = '_is_'.$rule[0];
			if (!method_exists($this, $fn_name))
			{
				return new ValidatorResult(false, '尚不支持此类型');
			}

			$message = (isset($rule[2]) && $rule[2] !== '') ? : $index . '参数类型不正确';
			if (!$this->$fn_name($values[$index])) 
			{
				// 根据message是否传入、为空，决定返回的message
				return new ValidatorResult(false, $message);
			}

			// 类型为array，并且设置了子类型
			if ($rule[0] === 'array' && isset($rule[3]))
			{
				$sub_fn = '_is_'.$rule[3];
				if (!method_exists($this, $sub_fn))
				{
					return new ValidatorResult(false, '尚不支持此类型');
				}
				
				foreach ($values[$index] as $v) {
					if (!$this->$sub_fn($v)) 
					{
						return new ValidatorResult(false, $message);	
					}
				}	
			}
		}
		return new ValidatorResult(true);
	}

	private function _is_string($v) 
	{
		return is_string($v);
	}
	
	private function _is_array($v) 
	{
		return is_array($v);
	}
	
	private function _is_int($v) 
	{
		return is_int($v);
	}
}

class ValidatorResult
{
	public $valid;
	public $message;

	public function __construct($valid, $message)
	{
		$this->valid = $valid;

		if (!$valid) 
		{
			$this->message	= $message ? : '参数错误';
		}
	}
}

?>
