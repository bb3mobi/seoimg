<?php
/**
*
* @package SEO Images in Attachment
* @copyright BB3.Mobi 2015 (c) Anvar (apwa.ru)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace bb3mobi\seoimg\migrations;

class v_1_0_1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['seoimg_version']) && version_compare($this->config['seoimg_version'], '1.0.1', '>=');
	}

	static public function depends_on()
	{
		return array('\bb3mobi\seoimg\migrations\v_1_0_0');
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
			array('config.update', array('seoimg_version', '1.0.1')),
		);
	}
}
