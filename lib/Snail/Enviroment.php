<?php
/*
 *	Snail_Enviroment
 *
 *	@author		Author: fastin. (https://github.com/fastin)
 *	@git		https://github.com/fastin/snail
 *	@version	0.2
 *	@license	MIT License. (https://github.com/fastin/Snail/blob/master/LICENSE)
 */
class Snail_Enviroment
{
	public	$_compile_path;
	public 	$_path;
	private $_parents	 = array();
	private $_compiler;
	private $_names		 = array();
	private $_blocks	 = array();
	public 	$_array		 = array();
	public 	$_vars		 = array();
	private	$_time		 = 0;
	
	public function __construct($options = array())
	{
		$options = array_merge(array(
			"path"			=> "templates/",
			"compile_path"	=> "compile_tpl/"
		), $options);
		
		$this->setPath($options["path"]);
		$this->setCompilePath($options["compile_path"]);
	}
	
	/*
	 *	Sets the path to the templates
	 *
	 *	@param $path
	 */
	public function setPath($path)
	{
		$this->_path = $_SERVER['DOCUMENT_ROOT'].$path;
	}

	/*
	 *	Specifies the path for the ready-made templates
	 *
	 *	@param $path
	 */
	public function setCompilePath($path)
	{
		$this->_compile_path = $_SERVER['DOCUMENT_ROOT'].$path;
	}
	
	/**
	 *	Gets the Compiler instance.
	 *
	 *	@return Snail_CompilerInterface A Snail_CompilerInterface instance
	 */
	public function getCompiler()
	{
		if (null === $this->_compiler) {
			$this->_compiler = new Snail_Compiler($this);
		}
		return $this->_compiler;
	}
	
	/**
	 *	Sets the Compiler instance.
	 *
	 *	@param Snail_CompilerInterface $compiler A Snail_CompilerInterface instance
	 */
	public function setCompiler(Snail_CompilerInterface $compiler)
	{
		$this->_compiler = $compiler;
	}
	
	// Removes compiled templates
	public function removeCompiled()
	{
		$files = glob($this->_compile_path."/*");
		$c = count($files);
		if (count($files) > 0) {
			foreach ($files as $file) {      
				if (file_exists($file)) {
					unlink($file);
				}   
			}
		}
	}

	/*
	 *	Sets the value of the variable
	 *
	 *	@param $name
	 *	@param $value
	 */
	public function assign($name, $value)
	{
		$this->_vars[$name] = $value;
	}
	
	public function __set($name, $value) {
		$this->assign($name, $value);
	}
	
	/*
	 *	Inherits the template
	 *
	 *	@param $tmpl
	 */
	private function _extend($tmpl)
	{
		array_push($this->_parents, $tmpl);
		ob_start();
	}

	/*
	 *	Opens block
	 *
	 *	@param $block
	 */
	private function _block($block)
    {
		array_push($this->_names, $block);
		ob_start();
	}

	// Close block
	private function _endblock()
	{
		$this->_grab(array_pop($this->_names));
	}
	
	/*
	 *	Create block
	 *
	 *	@param $block
	 *	@return null
	 */
	private function _grab($block)
	{
		if (isset($this->_blocks[$block])) {
			ob_end_clean();
			return;
		}
		$tmp = ob_get_clean();
		if (strlen(trim($tmp)) > 0) {
			$this->_blocks[$block] = $tmp;
		}
	}

	/*
	 *	Returns the value of the unit
	 *
	 *	@param $block
	 *	@return string|null
	 */
	private function _output($block)
	{		
		echo (isset($this->_blocks[$block])) ? $this->_blocks[$block] : null;
	}
	
	/*
	 *	Compiles template
	 *
	 *	@param $tmpl
	 */
	private function _compile($tmpl) {
		$this->getCompiler()->compile($tmpl);
	}
	
	/*
	 *	Returns ready template
	 *
	 *	@param $tmpl
	 *	@return string
	 */
	public function fetch($tmpl)
	{
		$this->_compile($tmpl);
		$template = $this->_compile_path . md5($this->_path . preg_replace('/\..+$/', '', $tmpl)) .".php";
		extract($this->_vars);
		include $template;	
		while (!empty($this->_parents)) {
			$this->_compile($tmpl = array_pop($this->_parents));
			include $this->_compile_path . md5($this->_path . preg_replace('/\..+$/', '', $tmpl)) .".php";
		}
		return ob_get_clean();
	}
	
	/*
	 *	Prints template
	 *
	 *	@param $tmpl
	 */
	public function display($tmpl)
	{
		$this->_time = round(microtime() - $this->_time, 4);
		$this->assign("time", $this->_time);
		
		echo $this->fetch($tmpl);
	}
}
