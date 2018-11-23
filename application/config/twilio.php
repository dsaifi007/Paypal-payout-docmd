<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	* Name:  Twilio
	*
	* Author: Ben Edmunds
	*		  ben.edmunds@gmail.com
	*         @benedmunds
	*
	* Location:
	*
	* Created:  03.29.2011
	*
	* Description:  Twilio configuration settings.
	*
	*
	*/

	/**
	 * Mode ("sandbox" or "prod")
	 **/
	$config['mode']   = 'sandbox';

	/**
	 * Account SID
	 **/
	//$config['account_sid']   = 'AC7d5193f578d8217a526370ed4a3a2116';
	$config['account_sid']   = 'ACcd098ede3b5cbaa28f1fe17b6432503b';
	/**
	 * Auth Token
	 **/
	//$config['auth_token']    = '8911d1dc304d460f815fe5baebac570f';
	$config['auth_token']    = 'f25607fd48056aa0083c58f216560130';

	/**
	 * API Version
	 **/
	$config['api_version']   = '2010-04-01';
	//hFp9IMXqTkI/1p05O3usI8CcdsJPRlYcFW0lXYp3
	/**
	 * Twilio Phone Number
	 **/
	$config['number']     = '14242383234';


/* End of file twilio.php */
