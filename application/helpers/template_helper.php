<?php
if (!function_exists('display_message_info')) :
	function display_message_info($message = []) {
		$html = '';
		foreach ($message as $k => $v) :
			if (!$v) : continue;
			endif;
			$alertCls = ($k == '1') ? 'alert-success' : 'alert-danger';
			$html .= '<div class="alert ' . $alertCls . '">';
			//$html .= '<button class="close" data-close="alert"></button>';
			$html .= '<span class="message">' . $v . '</span>';
			$html .= '</div>';
		endforeach;
		return $html;
	}

endif;

?>