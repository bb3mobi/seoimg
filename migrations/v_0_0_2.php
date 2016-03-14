<?php
/**
*
* @package SEO Images in Attachment
* @copyright BB3.Mobi 2015 (c) Anvar (apwa.ru)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace bb3mobi\seoimg\migrations;

class v_0_0_2 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['seoimg_version']) && version_compare($this->config['seoimg_version'], '0.0.2', '>=');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return array(
		);
	}

	public function revert_schema()
	{
		return array(
		);
	}

	public function update_data()
	{
		return array(
			// Current version
			array('config.add', array('seoimg_version', '0.0.2')),
		);
	}

	public function revert_data()
	{
		return array(
			// Remove version
			array('config.remove', array('seoimg_version')),
		);
	}
}
