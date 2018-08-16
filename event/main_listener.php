<?php
/**
 * @package bbguild v2.0
 * @copyright 2018 avathar.be
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace avathar\bbguild\event;

use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class main_listener implements EventSubscriberInterface
{
	/* @var helper */
	protected $helper;

	/* @var template */
	protected $template;

	/* @var user */
	protected $user;

	/* @var config */
	protected $config;

	/**
	 * main_listener constructor.
	 *
	 * @param \phpbb\controller\helper $helper
	 * @param \phpbb\template\template $template
	 * @param \phpbb\user              $user
	 * @param \phpbb\config\config     $config
	 */
	public function __construct(helper $helper,
		template $template,
		user $user,
		config $config
	)
	{

		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
		$this->config = $config;
	}


	/**
	 * Assign functions defined in this class to event listeners in the core
	 *
	 * @return array
	 */
	static public function getSubscribedEvents()
	{
		return array(
			// for all defined events, write a function below
			'core.common'                           => 'global_calls',
			'core.user_setup'                        => 'load_language_on_setup',
			'core.page_header'                        => 'add_page_header_link',
			'core.permissions'                        => 'add_permission_cat',
		);
	}
	/**
	 * core.common
	 * Handles logic that needs to be called on every page.
	 *
	 * @param array $event Array containing situational data.
	 */
	public function global_calls($event)
	{
		// Assign global template vars.
		$this->template->assign_vars(
			array(
			'S_BBGUILD_ENABLED'   => true,
			)
		);
	}

	/**
	 * core.user_setup
	 *
	 * @param $event
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'avathar/bbguild',
			'lang_set' => array('common','admin') ,
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}


	/**
	 * core.page_header
	 *
	 * @param $event
	 */
	public function add_page_header_link($event)
	{
		$this->template->assign_vars(
			array(
			'U_GUILD'    => $this->helper->route(
				'avathar_bbguild_00',
				array(
					'guild_id' => 1,
					'page' => 'roster'
				)
			),
			)
		);
	}


	/**
	 * bbGuild permission category
	 *
	 * @param $event
	 */
	public function add_permission_cat($event)
	{
		$perm_cat = $event['categories'];
		$perm_cat['bbguild'] = 'ACP_BBGUILD';
		$event['categories'] = $perm_cat;

		$permission = $event['permissions'];
		$permission['a_bbguild']    = array('lang' => 'ACL_A_BBGUILD',        'cat' => 'bbguild');
		$permission['u_bbguild']    = array('lang' => 'ACL_U_BBGUILD',        'cat' => 'bbguild');
		$permission['u_charclaim']    = array('lang' => 'ACL_U_CHARCLAIM',    'cat' => 'bbguild');
		$permission['u_charadd']    = array('lang' => 'ACL_U_CHARADD',        'cat' => 'bbguild');
		$permission['u_chardelete']    = array('lang' => 'ACL_U_CHARUPDATE',    'cat' => 'bbguild');
		$permission['u_charupdate']    = array('lang' => 'ACL_U_CHARDELETE',    'cat' => 'bbguild');
		$event['permissions'] = $permission;
	}

}
