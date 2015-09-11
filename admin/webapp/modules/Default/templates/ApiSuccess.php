<?php
	header("Content-Type: text/plain");
	/* @var $response Zend_Http_Response */
	$response = $this->getContext()->getRequest()->getAttribute('response');
	if (!is_null($response)) {
	    \Mojavi\Logging\LoggerManager::error(__METHOD__ . " :: " . $response->getBody());
	     
		echo $response->getBody();
	} else {
		$bad_result = array('result' => 'ERROR', 'errors'	=> array('Bad response from server'));
		echo json_encode($bad_result);
	}
?>