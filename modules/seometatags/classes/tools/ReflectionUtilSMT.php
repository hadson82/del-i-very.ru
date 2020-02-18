<?php

class ReflectionUtilSMT
{
	public static function getClassSource(ReflectionClass $class)
	{
		$path = $class->getFileName();
		$lines = @file( $path );
		$from = $class->getStartLine();
		$to = $class->getEndLine();
		$len = $to - $from + 1;
		return implode( array_slice( $lines, $from-1, $len ));
	}

	public static function getClassSourceByClassName($class_name)
	{
		self::getClassSource(new ReflectionClass($class_name));
	}
}