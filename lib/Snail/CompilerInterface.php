<?php
/*
 *	Snail_CompilerInterface
 *
 *	@author		Author: fastin. (https://github.com/fastin)
 *	@git		https://github.com/fastin/snail
 *	@version	0.2
 *	@license	MIT License. (https://github.com/fastin/Snail/blob/master/LICENSE)
 */
interface Snail_CompilerInterface
{  
	/**
	 * Compiles template.
	 *
	 * @param $id
	 *
	 * @return Snail_CompilerInterface The current compiler instance
	 */
	public function compile($id);
}