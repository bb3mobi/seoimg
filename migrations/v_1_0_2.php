<?php
/**
*
* @package SEO Images in Attachment
* @copyright BB3.Mobi 2015 (c) Anvar (apwa.ru)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace bb3mobi\seoimg\migrations;

class v_1_0_2 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['seoimg_version']) && version_compare($this->config['seoimg_version'], '1.0.2', '>=');
	}

	static public function depends_on()
	{
		return array('\bb3mobi\seoimg\migrations\v_1_0_1');
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
			array('permission.add', array('f_download_images', false, 'f_download')),

			/*array('permission.permission_set', array('ROLE_FORUM_READONLY', 'f_download_images', 'role', true)),
			array('permission.permission_set', array('ROLE_FORUM_LIMITED', 'f_download_images', 'role', true)),
			array('permission.permission_set', array('ROLE_FORUM_LIMITED_POLLS', 'f_download_images', 'role', true)),
			array('permission.permission_set', array('ROLE_FORUM_STANDARD', 'f_download_images', 'role', true)),
			array('permission.permission_set', array('ROLE_FORUM_POLLS', 'f_download_images', 'role', true)),
			array('permission.permission_set', array('ROLE_FORUM_FULL', 'f_download_images', 'role', true)),
			array('permission.permission_set', array('ROLE_FORUM_ONQUEUE', 'f_download_images', 'role', true)),
			array('permission.permission_set', array('ROLE_FORUM_BOT', 'f_download_images', 'role', true)),
			array('permission.permission_set', array('ROLE_FORUM_NEW_MEMBER', 'f_download_images', 'role', true)),*/

			// Current version
			array('config.update', array('seoimg_version', '1.0.2')),
		);
	}

	public function revert_data()
	{
		return array(
			array('permission.remove', array('f_download_images', false)),
		);
	}
}
