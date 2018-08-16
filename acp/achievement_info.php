<?php
/**
 * @package bbguild v2.0
 * @copyright 2018 avathar.be
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace avathar\bbguild\acp;

/**
 * info class for acp module achievements
 *
 *   @package bbguild
 */
class achievement_info
{
	/**
	 * phpbb module function
	 */
	public function module()
	{
		return array(
			'filename'    => '\avathar\bbguild\acp\achievement_module',
			'title'        => 'ACP_BBGUILD_PLAYER',
			'version'    => '2.0.0',
			'modes'        => array(
				'addachievement'        => array(
					'title' => 'ACP_ADDACHIEV',
					'auth' => 'ext_avathar/bbguild && acl_a_board && acl_a_bbguild',
					'cat' => array('ACP_BBGUILD_PLAYER'),
					'display' => false),
				'listachievements'        => array(
					'title' => 'ACP_LISTACHIEV',
					'auth' => 'ext_avathar/bbguild && acl_a_board && acl_a_bbguild',
					'cat' => array('ACP_BBGUILD_PLAYER')),
			),
		);
	}

}
