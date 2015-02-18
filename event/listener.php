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

	static public function getSubscribedEvents()
	{
		return array(
			'core.parse_attachments_modify_template_data' => 'parse_attachments_modify_template_data',
		);
	}

	public function parse_attachments_modify_template_data($event)
	{
		global $topic_data;

		switch ($event['display_cat'])
		{
			// Images
			case ATTACHMENT_CATEGORY_IMAGE:

				$block_array = $event['block_array'];

				// SEO Description img
				$attach_comment = $event['attachment']['attach_comment'];
				if ($attach_comment || !empty($topic_data['topic_title']))
				{
					$attach_comment = ($attach_comment) ? $event['attachment']['attach_comment'] : $topic_data['topic_title'];
					$block_array['DOWNLOAD_NAME'] = $this->strip_code($attach_comment) . ' - ' . utf8_basename($event['attachment']['real_filename']);
				}

				// SEO link img
				$inline_link = generate_board_url() ."/small/{$event['attachment']['attach_id']}.{$event['attachment']['extension']}";
				$download_link = generate_board_url() ."/img/{$event['attachment']['attach_id']}.{$event['attachment']['extension']}";
				$block_array['U_INLINE_LINK'] = $inline_link;
				$block_array['U_DOWNLOAD_LINK'] = $download_link;
				$block_array['COMMENT'] = '';

				// Block add
				$event['block_array'] = $block_array;
			break;

			// Images, but display Thumbnail
			case ATTACHMENT_CATEGORY_THUMB:

				$block_array = $event['block_array'];

				// SEO Description img
				$attach_comment = $event['attachment']['attach_comment'];
				if ($attach_comment || !empty($topic_data['topic_title']))
				{
					$attach_comment = ($attach_comment) ? $event['attachment']['attach_comment'] : $topic_data['topic_title'];
					$block_array['DOWNLOAD_NAME'] = $this->strip_code($attach_comment) . ' - ' . utf8_basename($event['attachment']['real_filename']);
				}

				// SEO link img
				$thumbnail_link = generate_board_url() ."/thumb/{$event['attachment']['attach_id']}.{$event['attachment']['extension']}";
				$download_link = generate_board_url() ."/pic/{$event['attachment']['attach_id']}.{$event['attachment']['extension']}";
				$block_array['THUMB_IMAGE'] = $thumbnail_link;
				$block_array['U_DOWNLOAD_LINK'] = $download_link;

				// Block add
				$event['block_array'] = $block_array;
			break;
		}
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

?>