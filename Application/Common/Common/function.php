<?php 

function html_escape($var)
{
	if(is_array($var))
	{
		foreach ($var as $key => $value) {
			$var[$key] = html_escape($value);
		}
	}
	else
	{
		if(is_string($var))
			$var = htmlspecialchars($var);
	}
	return $var;
}