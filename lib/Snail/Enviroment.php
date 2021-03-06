<?php
/*
 *	Snail_Enviroment
 *
 *	@author		Author: fastin. (https://github.com/fastin)
 *	@git		https://github.com/fastin/snail
 *	@version	0.4
 *	@license	MIT License. (https://github.com/fastin/Snail/blob/master/LICENSE)
 */
class Snail_Enviroment
{
	public	$_compile_path;
	public 	$_path;
	private $_parents	= array();
	private $_compiler;
	private $_names		= array();
	private $_blocks	= array();
	public 	$_array		= array();
	public 	$_vars		= array();
	private	$_time		= 0;
	private $_filters	= array();
	
	/*
	 *	Constructor
	 *
	 * 	Available options:
	 *
	 *	* path: The path to the templates.
	 *
	 *	* compile_path: The path to the compiled templates.
	 *
	 *	@param $options
	 */
	public function __construct($options = array())
	{
		$options = array_merge(array(
			"path"			=> "templates/",
			"compile_path"	=> "compile_tpl/"
		), $options);
		
		$this->setPath($options["path"]);
		$this->setCompilePath($options["compile_path"]);
		$this->_addDefaultFilters();
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
	 *	@return Snail_Interface_Compiler
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
	 *	@param Snail_Interface_Compiler $compiler
	 */
	public function setCompiler(Snail_Interface_Compiler $compiler)
	{
		$this->_compiler = $compiler;
	}

	/**
	 *	Adds the filter
	 *
	 *	@param $filter
	 *	@param Snail_Interface_Filter $class 
	 */
	public function addFilter($filter, Snail_Interface_Filter $class)
	{
		if (!$class instanceof Snail_Interface_Filter) {
			throw new LogicException('A filter must be an instance of Snail_Interface_Filter');
		} else if ($class instanceof Snail_Interface_Filter) {
			$this->_filters[$filter] = array(
				"class" => $class,
			);
		}
	}
	
	/**
	 *	Gets the filter
	 *
	 *	@param $filter
	 *	@return Snail_Interface_Filter
	 */
	public function getFilter($filter)
	{
		if(isset($this->_filters[$filter])) {
			if(isset($this->_filters[$filter]["class"])) {
				return $this->_filters[$filter]["class"];
			}
		} else {
			throw new Snail_Exception_Runtime("Undefined filter: {$filter}");
		}
	}
	
	/**
	 *	Removes the filter
	 *
	 *	@param $filter
	 */
	public function removeFilter($filter)
	{
		if(isset($this->_filters[$filter])) {
			unset($this->_filters[$filter]);
		} else {
			throw new Snail_Exception_Runtime("Undefined filter: {$filter}");
		}
	}
	
	// Adds default filters
	private function _addDefaultFilters()
	{
		$this->addFilter("e", new Snail_Filter_Escape);
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
	 *	Returns a variable or displays an error if the variable is not found.
	 *
	 *	@param $name
	 *	@return string
	 */
	public function __get($name) {
		if(isset($this->_vars[$name])) {
			return $this->_vars[$name];
		} else {
			throw new Snail_Exception_Runtime("Undefined index: {$name}");
		}
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