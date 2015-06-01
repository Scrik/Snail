# Snail
[![Latest Stable Version](https://poser.pugx.org/fastin/snail/v/stable)](https://packagist.org/packages/fastin/snail) [![Total Downloads](https://poser.pugx.org/fastin/snail/downloads)](https://packagist.org/packages/fastin/snail) [![License](https://poser.pugx.org/fastin/snail/license)](https://packagist.org/packages/fastin/snail)
## Installing
1. Use [Composer](http://getcomposer.org) to install Snail into your project:

  ```bash
  composer require "fastin/snail": "0.*"
  ```

2. Connect the Snail in your project and use:

  main.php:
  ```php
  $snail = new Snail_Enviroment(array(
	"path" => "path/to/templates/",
	"compile_path" => "path/to/compile_tpl/"
  ));
  $snail->content = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.";
  $snail->title = "Lorem ipsum";
  $snail->display("main.tpl");
  ```
  main.tpl:
  ```html
  <!DOCTYPE html>
  <html lang="en">
  	<head>
  		<title>{$title}</title>
  	</head>
  	<body>
  		{$content}
  	</body>
  </html>
  ```
  
## Documentation
* [Russian](https://github.com/fastin/Snail/wiki/Домашняя-страница)

