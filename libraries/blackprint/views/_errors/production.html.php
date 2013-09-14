<?php
use lithium\analysis\Debugger;
use lithium\analysis\Inspector;

$exception = $info['exception'];
$code = $exception->getCode();
// The actual exception message, ie. "Action `admin_index` not found." ... 
// Which we don't need to display.
// $exception->getMessage();
?>
<div id="center-box-bg" class="shadow">
	<div id="center-box-content">
		<h1>Oops!</h1>
		<p class="lighter_copy">
			<?php
			switch($code) {
				case 404:
					echo 'Sorry, the page you were looking for does not exist. Please check the URL and try again.';
					break;
				case 500:
					echo 'Sorry, there was an error on the server side here. Please try again later.';
					break;
			}
			?>
		</p>
		<p class="lighter_copy">
			You can return to the home page by <?=$this->html->link('clicking here.', '/'); ?><br />
		</p>
	</div>
</div>