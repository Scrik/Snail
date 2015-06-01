<?php
/*
 *	Snail_Compiler
 *
 *	@author		Author: fastin. (https://github.com/fastin)
 *	@git		https://github.com/fastin/snail
 *	@version	0.2
 *	@license	MIT License. (https://github.com/fastin/Snail/blob/master/LICENSE)
 */
class Snail_Compiler implements Snail_CompilerInterface
{	
	private $_env;
	private $_rules = array(
		array(
			"regexp" => '#^(\$.*)\|e$#',
			"value" => '<?php echo htmlspecialchars({key}, ENT_QUOTES); ?>',
			"replace" => array( 1 => "key" ),
		),
		array(
			"regexp" => '#^(\$.*)$#',
			"value" => '<?php echo {key}; ?>',
			"replace" => array( 1 => "key" ),
		),
		array(
			"regexp" => '#^%\s+extends\s+(.*)\s+%$#',
			"value" => '<?php $this->_extend({string}); ?>',
			"replace" => array( 1 => "string" ),
		),
		array(
			"regexp" => '#^%\s+set\s+(\$.*)\s+\=\s+(.*)\s+%$#',
			"value" => '<?php {key1} = {key2}; ?>',
			"replace" => array( 1 => "key1", 2 => "key2" ),
		),
		array(
			"regexp" => '#^%\s+php\s+%$#',
			"value" => '<?php ',
		),
		array(
			"regexp" => '#^%\s+endphp\s+%$#',
			"value" => "?>",
		),
		array(
			"regexp" => '#^%\s+output\s+(.*)\s+%$#',
			"value" => '<?php $this->_output({string}); ?>',
			"replace" => array( 1 => "string" ),
		),
		array(
			"regexp" => '#^%\s+block\s+(.*)\s+%$#',
			"value" => '<?php $this->_block({string}); ?>',
			"replace" => array( 1 => "string" ),
		),
		array(
			"regexp" => '#^%\s+endblock\s+%$#',
			"value" => '<?php $this->_endblock(); ?>',
		),
		array(
			"regexp" => '#^%\s+if\s+(.*)\s+%$#',
			"value" => '<?php if ({string}): ?>',
			"replace" => array( 1 => "string" ),
		),
		array(
			"regexp" => '#^%\s+elseif\s+(.*)\s+%$#',
			"value" => '<?php elseif ({string}): ?>',
			"replace" => array( 1 => "string" ),
		),
		array(
			"regexp" => '#^%\s+else\s+%$#',
			"value" => '<?php else: ?>',
		),
		array(
			"regexp" => '#^%\s+endif\s+%$#',
			"value" => "<?php endif; ?>",
		),
		array(
			"regexp" => '#^%\s+for\s+([^,]+)\s+in\s+(.*)\s+%$#',
			"value" => '<?php foreach ({key2} as {key1}): ?>',
			"replace" => array( 1 => "key1", 2 => "key2", ),
		),
		array(
			"regexp" => '#^%\s+for\s+([^,]+),([^,]+)\s+in\s+(.*)\s+%$#',
			"value" => '<?php foreach ({key1} as {key3} => {key2}): ?>',
			"replace" => [ 1 => "key1", 2 => "key2", 3 => "key3"  ],
		),
		array(
			"regexp" => '#^%\s+endfor\s+%$#',
			"value" => '<?php endforeach; ?>',
		),
	);

	public function __construct(Snail_Enviroment $env)
	{
		$this->_env = $env;
	}
	
	public function compile($tmpl)
	{
		$template = $this->_env->_path . $tmpl;
		$compiled = $this->_env->_compile_path . md5($this->_env->_path . preg_replace('/\..+$/', '', $tmpl)) .".php";

		if (!file_exists($template)) {
			throw new \Exception("Template {$template} not found");
		}
		$timeT = filemtime($template);
		$timeC = file_exists($compiled) ? filemtime($compiled) : 0;
		
		// Compiled version is absent or obsolete
		if ($timeC < $timeT)
		{
			// Get template and find parts looks like "{macro}"
			$text = str_replace(array('&lt;?', '?&gt;'), array('&lt;?', '?&gt;'), file_get_contents($template));
			$parts = preg_split('/(\{.*\})/U', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

			$text = '';
			foreach ($parts as $part) {
				$isPhp = FALSE;
				if ($part{0} == '{' && $part{strlen($part) - 1} == '}') {
					$macro = substr($part, 1, -1);
					foreach($this->_rules as $value)
					{
						if (preg_match($value["regexp"], $macro)) {
							$part = preg_replace_callback($value['regexp'], function($m) use($value) {
									$line = $value['value'];
									if(isset($value['replace'])) {
										foreach($value['replace'] as $key => $node) {
											$line = str_replace("{".$node."}", $m[$key], $line);
										}
									}
									return $line;
								},
								$macro
							);
							break;
						}
					}
				}
				$text .= $part;
			}
			file_put_contents($compiled, $text);
		}	
		return $this;
	}
}