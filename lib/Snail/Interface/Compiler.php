<?php
/*
 *	Snail_Interface_Compiler
 *
 *	@author		Author: fastin. (https://github.com/fastin)
 *	@git		https://github.com/fastin/snail
 *	@version	0.4
 *	@license	MIT License. (https://github.com/fastin/Snail/blob/master/LICENSE)
 */
interface Snail_Interface_Compiler
{  
	/**
	 *	Compiles template.
	 *
	 *	@param $id
	 *
	 *	@return Snail_Interface_Compiler The current compiler instance
	 */
	public function compile($id);
}