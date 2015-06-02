<?php
/*
 *	Snail_Interface_Filter
 *
 *	@author		Author: fastin. (https://github.com/fastin)
 *	@git		https://github.com/fastin/snail
 *	@version	0.4
 *	@license	MIT License. (https://github.com/fastin/Snail/blob/master/LICENSE)
 */
interface Snail_Interface_Filter
{  
	/**
	 *	Filters value.
	 *
	 *	@param $value
	 *	@return string
	 */
	public function filter($value);
}