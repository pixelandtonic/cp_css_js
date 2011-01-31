<?php if (! defined('APP_VER')) exit('No direct script access allowed');


/**
 * CP CSS & JS extension for EE2
 *
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2011 Pixel & Tonic
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */

class Cp_css_js_ext {

	var $name           = 'CP CSS &amp; JS';
	var $version        = '1.0';
	var $description    = 'Allows you to add custom CSS and JS to the Control Panel pages';
	var $settings_exist = 'y';
	var $docs_url       = 'http://github.com/brandonkelly/cp_css_js';

	/**
	 * Class Constructor
	 */
	function __construct($settings = array())
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();

		$this->settings = $settings;
	}

	// --------------------------------------------------------------------

	/**
	 * Activate Extension
	 */
	function activate_extension()
	{
		// -------------------------------------------
		//  Add the extension hooks
		// -------------------------------------------

		$hooks = array(
			'cp_css_end',
			'cp_js_end'
		);

		foreach($hooks as $hook)
		{
			$this->EE->db->insert('extensions', array(
				'class'    => get_class($this),
				'method'   => $hook,
				'hook'     => $hook,
				'settings' => '',
				'priority' => 10,
				'version'  => $this->version,
				'enabled'  => 'y'
			));
		}
	}

	/**
	 * Update Extension
	 */
	function update_extension($current = '')
	{
		// Nothing to change...
		return FALSE;
	}

	/**
	 * Disable Extension
	 */
	function disable_extension()
	{
		// -------------------------------------------
		//  Delete the extension hooks
		// -------------------------------------------

		$this->EE->db->where('class', get_class($this))
		             ->delete('exp_extensions');
	}

	function settings()
	{
		$settings = array(
			'custom_css' => array('t', array('rows' => '10'), ''),
			'custom_js'  => array('t', array('rows' => '10'), '')
		);

		return $settings;
	}

	// --------------------------------------------------------------------

	private function _add($data, $which)
	{
		// If another extension shares the same hook,
		// we need to get the latest and greatest config
		if ($this->EE->extensions->last_call !== FALSE)
		{
			$data = $this->EE->extensions->last_call;
		}

		$data .= NL . $this->settings['custom_'.$which];

		return $data;
	}

	/**
	 * cp_css_end ext hook
	 */
	function cp_css_end($data)
	{
		return $this->_add($data, 'css');
	}

	/**
	 * cp_js_end ext hook
	 */
	function cp_js_end($data)
	{
		return $this->_add($data, 'js');
	}

}