<?php

class ObjectModelSMT extends ObjectModel
{
	public function getImage()
	{
		if ($this->checkImage())
			return $this->getPathImage();
		return false;
	}

	public function uploadImage($tmp_name)
	{
		if (!$this->id)
			return false;
		if ($tmp_name)
		{
			if ($this->checkImage())
				$this->deleteImg();
			if (ToolsModuleSMT::checkImage($tmp_name))
			{
				$width = null;
				$height = null;
				if (property_exists($this, 'image_size'))
				{
					$width = $this->{'image_size'}[0];
					$height = $this->{'image_size'}[1];
				}
				ImageManager::resize($tmp_name, $this->getFullPathImage(), $width, $height);
			}
			return true;
		}
		return false;
	}

	public function getFullPathImage()
	{
		return _PS_MODULE_DIR_.ToolsModuleSMT::getModNameForPath(__FILE__).'/views/img/'.Tools::strtolower($this->getClassName()).'/'.(int)$this->id.'.jpg';
	}

	public function getPathImage()
	{
		return _MODULE_DIR_.ToolsModuleSMT::getModNameForPath(__FILE__).'/views/img/'.Tools::strtolower($this->getClassName()).'/'.(int)$this->id.'.jpg';
	}

	public function checkImage()
	{
		return file_exists($this->getFullPathImage());
	}

	public function deleteImg()
	{
		if ($this->checkImage())
			unlink($this->getFullPathImage());
	}

	public function getClassName()
	{
		return 'object_model';
	}

	public function toArray()
	{
		$array = array(
			'id' => $this->id
		);
		foreach (static::$definition['fields'] as $field_name => $field)
		{
			unset($field);
			if (Tools::substr($field_name, 0, 4) == 'ids_')
				$field_name = Tools::substr($field_name, 4);
			$method = 'toArray'.Tools::toCamelCase($field_name, true);
			if (method_exists($this, $method))
				$array[$field_name] = $this->{$method}();
			else
				$array[$field_name] = $this->{$field_name};
		}
		return $array;
	}
}