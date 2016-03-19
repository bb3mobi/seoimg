<?php
/**
*
* @package SEO Images in Attachment
* @copyright BB3.Mobi 2014 (c) Anvar [apwa.ru]
* @link http://bb3.mobi
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace bb3mobi\seoimg\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var string phpbb_root_path */
	protected $phpbb_root_path;

	/** @var string phpEx */
	protected $php_ext;

	public function __construct(\phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, $phpbb_root_path, $php_ext)
	{
		$this->user = $user;
		$this->auth = $auth;
		$this->db = $db;
		$this->helper = $helper;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.viewtopic_modify_post_data'	=> 'attach_list_image_auth',
			'core.parse_attachments_modify_template_data'	=> 'parse_attachments_modify_template_data',
		);
	}

	public function attach_list_image_auth($event)
	{
		$forum_id = $event['forum_id'];
		if (!$this->auth->acl_get('u_download') || $this->auth->acl_get('f_download', $forum_id) || (!$this->auth->acl_get('f_download', $forum_id) && !$this->auth->acl_get('f_download_images', $forum_id)))
		{
			return;
		}

		// Pull attachment data
		$attach_list = array();
		foreach ($event['rowset'] as $row)
		{
			if (!$row['post_attachment'])
			{
				continue;
			}
			$attach_list[] = $row['post_id'];
		}

		if (sizeof($attach_list))
		{
			$this->user->add_lang_ext('bb3mobi/seoimg', 'info_acp_permissions');

			$attachments = $event['attachments'];

			$sql = 'SELECT * FROM ' . ATTACHMENTS_TABLE . '
				WHERE ' . $this->db->sql_in_set('post_msg_id', $attach_list) . '
					AND in_message = 0
				ORDER BY filetime DESC, post_msg_id ASC';
				//AND (mimetype = "image/jpeg" OR mimetype = "image/png" OR mimetype = "image/gif")
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				switch ($row['mimetype'])
				{
					case 'image/jpeg':
					case 'image/png':
					case 'image/gif':
						$attachments[$row['post_msg_id']][] = $row;
					break;

					default:
						$row['attach_comment'] = $this->user->lang['SORRY_AUTH_DOWNLOAD_ATTACH'];
						if (!$this->user->data['is_registered'])
						{
							$row['attach_comment'] .= '<p><a href="' . append_sid("{$this->phpbb_root_path}ucp.{$this->php_ext}", 'mode=login') . '">' . $this->user->lang['LOGIN_REQUIRED'] . '</a></p>';
						}
						$attachments[$row['post_msg_id']][] = $row;
					break;
				}
			}
			$this->db->sql_freeresult($result);

			// Attachments exist
			if (sizeof($attachments))
			{
				$event['attachments'] = $attachments;
				$event['display_notice'] = false;
			}
		}
	}

	public function parse_attachments_modify_template_data($event)
	{
		$attachment = $event['attachment'];
		if ($attachment['in_message'])
		{
			return;
		}

		switch ($event['display_cat'])
		{
			// Images
			case ATTACHMENT_CATEGORY_IMAGE:
			case ATTACHMENT_CATEGORY_THUMB:
				global $topic_data;

				$block_array = $event['block_array'];

				// SEO Description img
				$attach_comment = $attachment['attach_comment'];
				if ($attach_comment || !empty($topic_data['topic_title']))
				{
					$attach_comment = ($attach_comment) ? $attachment['attach_comment'] : $topic_data['topic_title'];
					$block_array['DOWNLOAD_NAME'] = $this->strip_code($attach_comment) . ' - ' . utf8_basename($attachment['real_filename']);
				}
			break;

			default:
				return;
			break;
		}

		switch ($event['display_cat'])
		{
			// Images
			case ATTACHMENT_CATEGORY_IMAGE:

				// SEO link img
				$inline_link = $this->helper->route('bb3mobi_seoimg', array(
					'mode'	=> 'small',
					'attach_id'	=> $attachment['attach_id'],
					'extension'	=> $attachment['extension']),
					false, '', true
				);
				$download_link = $this->helper->route('bb3mobi_seoimg', array(
					'mode'	=> 'img',
					'attach_id'	=> $attachment['attach_id'],
					'extension'	=> $attachment['extension']),
					false, '', true
				);

				$block_array['U_INLINE_LINK'] = $inline_link;
				$block_array['U_DOWNLOAD_LINK'] = $download_link;
				$block_array['COMMENT'] = '';
			break;

			// Images, but display Thumbnail
			case ATTACHMENT_CATEGORY_THUMB:
				// SEO link img
				$thumbnail_link = $this->helper->route('bb3mobi_seoimg', array(
					'mode'	=> 'thumb',
					'attach_id'	=> $attachment['attach_id'],
					'extension'	=> $attachment['extension']),
					false, '', true
				);
				$download_link = $this->helper->route('bb3mobi_seoimg', array(
					'mode'	=> 'pic',
					'attach_id'	=> $attachment['attach_id'],
					'extension'	=> $attachment['extension']),
					false, '', true
				);

				$block_array['THUMB_IMAGE'] = $thumbnail_link;
				$block_array['U_DOWNLOAD_LINK'] = $download_link;
			break;
		}

		// Block add
		$event['block_array'] = $block_array;
	}

	private function strip_code($text)
	{
		$text = censor_text($text);
		strip_bbcode($text);
		$text = str_replace(array("&quot;", "/", "\n", "\t", "\r"), ' ', $text);
		$text = preg_replace("@(http(s)?://)?(([a-z0-9.-]+)?[a-z0-9-]+(!?\.[a-z]{2,4}))@", ' ', $text);
		return preg_replace("/[^A-ZА-ЯЁ.,-–?]+/ui", " ", $text);
	}
}
