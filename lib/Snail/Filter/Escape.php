<?php
/*
 *	Snail_Filter_Escape
 *
 *	@author		Author: fastin. (https://github.com/fastin)
 *	@git		https://github.com/fastin/snail
 *	@version	0.4
 *	@license	MIT License. (https://github.com/fastin/Snail/blob/master/LICENSE)
 */
class Snail_Filter_Escape implements Snail_Interface_Filter
{
	/*
	 *	Escape value
	 *
	 *	@param $value
	 *	@return string
	*/
	public function filter($value)
	{
		return htmlspecialchars($value, ENT_QUOTES);
	}
}
