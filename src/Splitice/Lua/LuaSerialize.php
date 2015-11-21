<?php
namespace Splitice\Lua;

class LuaSerialize
{
	private static function is_assoc($array)
	{
		return (bool)count(array_filter(array_keys($array), 'is_string')) && array_keys($array) !== range(0, count($array) - 1);
	}

	function serialize($value)
	{
		if (is_object($value)) {
			$value = (array)$value;
		}
		if (is_array($value)) {
			$members = array();
			if (self::is_assoc($value)) {
				foreach ($value as $k => $v) {
					$members[] = '[' . $this->serialize($k) .']=' . $this->serialize($v);
				}
			} else {
				foreach ($value as $k => $v) {
					$members[] = $this->serialize($v);
				}
			}
			return '{'.implode(',', $members).'}';
		} elseif (is_bool($value)) {
			return $value ? 'true' : 'false';
		} elseif (is_string($value)) {
			return var_export($value, true);
		} elseif (is_numeric($value)) {
			return $value;
		} elseif ($value === null) {
			return 'nil';
		}
		throw new \Exception('Unknown type to serialize: ' . $value);
	}
}