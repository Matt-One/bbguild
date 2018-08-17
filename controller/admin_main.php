<?php
/**
 * bbGuild Mainpage ACP
 *
 * @package   bbguild v2.0
 * @copyright 2018 avathar.be
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace avathar\bbguild\controller;

use phpbb\config\config;
use phpbb\language\language;
use phpbb\log\log;
use phpbb\pagination;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;

/**
 * Class admin_controller
 */
class admin_main
{
	protected $config;
	protected $language;
	/** @var log phpBB log object */
	protected $log;
	protected $pagination;
	/** @var request phpBB request object */
	protected $request;
	/** @var template phpBB template object */
	protected $template;
	/** @var user phpBB user object */
	protected $user;
	/** @var string phpBB root path */
	protected $root_path;
	/** @var string PHP extension */
	protected $php_ext;
	/** @var string Form key used for form validation */
	protected $form_key;
	/** @var string Custom form action */
	protected $u_action;

	public $bb_games_table;
	public $bb_logs_table;
	public $bb_ranks_table;
	public $bb_guild_table;
	public $bb_players_table;
	public $bb_classes_table;
	public $bb_races_table;
	public $bb_gameroles_table;
	public $bb_factions_table;
	public $bb_language_table;
	public $bb_motd_table;
	public $bb_recruit_table;
	public $bb_achievement_track_table;
	public $bb_achievement_table;
	public $bb_achievement_rewards_table;
	public $bb_criteria_track_table;
	public $bb_achievement_criteria_table;
	public $bb_relations_table;
	public $bb_bosstable;
	public $bb_zonetable;
	public $bb_news;
	public $bb_plugins;

	/**
	 * admin_main constructor.
	 * @param config $config
	 * @param language $language
	 * @param log $log
	 * @param pagination $pagination
	 * @param request $request
	 * @param template $template
	 * @param user $user
	 * @param string $phpbb_root_path
	 * @param string $phpEx
	 * @param string $bb_games_table
	 * @param string $bb_logs_table
	 * @param string $bb_ranks_table
	 * @param string $bb_guild_table
	 * @param string $bb_players_table
	 * @param string $bb_classes_table
	 * @param string $bb_races_table
	 * @param string $bb_gameroles_table
	 * @param string $bb_factions_table
	 * @param string $bb_language_table
	 * @param string $bb_motd_table
	 * @param string $bb_recruit_table
	 * @param string $bb_achievement_track_table
	 * @param string $bb_achievement_table
	 * @param string $bb_achievement_rewards_table
	 * @param string $bb_criteria_track_table
	 * @param string $bb_achievement_criteria_table
	 * @param string $bb_relations_table
	 * @param string $bb_bosstable
	 * @param string $bb_zonetable
	 * @param string $bb_news
	 * @param string $bb_plugins
	 */
	public function __construct(config $config, language $language, log $log, pagination $pagination, request $request, template $template, user $user, $phpbb_root_path, $phpEx,
		$bb_games_table,
		$bb_logs_table,
		$bb_ranks_table,
		$bb_guild_table,
		$bb_players_table,
		$bb_classes_table,
		$bb_races_table,
		$bb_gameroles_table,
		$bb_factions_table,
		$bb_language_table,
		$bb_motd_table,
		$bb_recruit_table,
		$bb_achievement_track_table,
		$bb_achievement_table,
		$bb_achievement_rewards_table,
		$bb_criteria_track_table,
		$bb_achievement_criteria_table,
		$bb_relations_table,
		$bb_bosstable,
		$bb_zonetable,
		$bb_news,
		$bb_plugins)
	{

		$this->bb_games_table = $bb_games_table;
		$this->bb_logs_table = $bb_logs_table;
		$this->bb_ranks_table = $bb_ranks_table;
		$this->bb_guild_table = $bb_guild_table;
		$this->bb_players_table = $bb_players_table;
		$this->bb_classes_table = $bb_classes_table;
		$this->bb_races_table = $bb_races_table;
		$this->bb_gameroles_table = $bb_gameroles_table;
		$this->bb_factions_table = $bb_factions_table;
		$this->bb_language_table = $bb_language_table;
		$this->bb_motd_table = $bb_motd_table;
		$this->bb_recruit_table = $bb_recruit_table;
		$this->bb_achievement_track_table = $bb_achievement_track_table;
		$this->bb_achievement_table = $bb_achievement_table;
		$this->bb_achievement_rewards_table = $bb_achievement_rewards_table;
		$this->bb_criteria_track_table = $bb_criteria_track_table;
		$this->bb_achievement_criteria_table = $bb_achievement_criteria_table;
		$this->bb_relations_table = $bb_relations_table;
		$this->bb_bosstable = $bb_bosstable;
		$this->bb_zonetable =  $bb_zonetable;
		$this->bb_news = $bb_news;
		$this->bb_plugins = $bb_plugins;

		$this->config = $config;
		$this->log = $log;
		$this->log->set_log_table($this->bb_logs_table);
		$this->pagination = $pagination;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->root_path = $phpbb_root_path;
		$this->php_ext = $phpEx;

	}

	/**
	 * Main handler, called by the ACP module
	 *
	 * @return void
	 */
	public function main()
	{
		$this->form_key = 'acp_topic_prefixes';
		add_form_key($this->form_key);

		$action = $this->request->variable('action', '');
		$prefix_id = $this->request->variable('prefix_id', 0);
		$this->set_forum_id($this->request->variable('forum_id', 0));

		switch ($action)
		{
			case 'add':
				$this->add_prefix();
			break;

			case 'edit':
			case 'delete':
				$this->{$action . '_prefix'}($prefix_id);
			break;

			case 'move_up':
			case 'move_down':
				$this->move_prefix($prefix_id, str_replace('move_', '', $action));
			break;
		}

		$this->display_settings();
	}

	/**
	 * Display topic prefix settings
	 *
	 * @return void
	 */
	public function display_settings()
	{
		foreach ($this->manager->get_prefixes($this->forum_id) as $prefix)
		{
			$this->template->assign_block_vars('prefixes', [
				'PREFIX_TAG'		=> $prefix['prefix_tag'],
				'PREFIX_ENABLED'	=> (int) $prefix['prefix_enabled'],
				'U_EDIT'			=> "{$this->u_action}&amp;action=edit&amp;prefix_id=" . $prefix['prefix_id'] . '&amp;forum_id=' . $this->forum_id . '&amp;hash=' . generate_link_hash('edit' . $prefix['prefix_id']),
				'U_DELETE'			=> "{$this->u_action}&amp;action=delete&amp;prefix_id=" . $prefix['prefix_id'] . '&amp;forum_id=' . $this->forum_id,
				'U_MOVE_UP'			=> "{$this->u_action}&amp;action=move_up&amp;prefix_id=" . $prefix['prefix_id'] . '&amp;forum_id=' . $this->forum_id . '&amp;hash=' . generate_link_hash('up' . $prefix['prefix_id']),
				'U_MOVE_DOWN'		=> "{$this->u_action}&amp;action=move_down&amp;prefix_id=" . $prefix['prefix_id'] . '&amp;forum_id=' . $this->forum_id . '&amp;hash=' . generate_link_hash('down' . $prefix['prefix_id']),
			]);
		}

		$this->template->assign_vars([
			'S_FORUM_OPTIONS'	=> make_forum_select($this->forum_id, false, false, true),
			'FORUM_ID'			=> $this->forum_id,
			'U_ACTION'			=> $this->u_action,
		]);
	}

	/**
	 * Add a prefix
	 *
	 * @return void
	 */
	public function add_prefix()
	{
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key($this->form_key))
			{
				$this->trigger_message('FORM_INVALID', E_USER_WARNING);
			}

			$tag = $this->request->variable('prefix_tag', '', true);
			$prefix = $this->manager->add_prefix($tag, $this->forum_id);
			$this->log->set_log_table()
			$this->log($prefix['prefix_tag'], 'ACP_LOG_PREFIX_ADDED');
		}
	}

	/**
	 * Edit a prefix
	 *
	 * @param int $prefix_id The prefix identifier to edit
	 * @return void
	 */
	public function edit_prefix($prefix_id)
	{
		if (!$this->check_hash('edit' . $prefix_id))
		{
			$this->trigger_message('FORM_INVALID', E_USER_WARNING);
		}

		try
		{
			$prefix = $this->manager->get_prefix($prefix_id);
			$this->manager->update_prefix($prefix['prefix_id'], ['prefix_enabled' => !$prefix['prefix_enabled']]);
		}
		catch (\OutOfBoundsException $e)
		{
			$this->trigger_message($e->getMessage(), E_USER_WARNING);
		}
	}

	/**
	 * Delete a prefix
	 *
	 * @param int $prefix_id The prefix identifier to delete
	 * @return void
	 */
	public function delete_prefix($prefix_id)
	{
		if (confirm_box(true))
		{
			try
			{
				$prefix = $this->manager->get_prefix($prefix_id);
				$this->manager->delete_prefix($prefix['prefix_id']);
				$this->log($prefix['prefix_tag'], 'ACP_LOG_PREFIX_DELETED');
			}
			catch (\OutOfBoundsException $e)
			{
				$this->trigger_message($e->getMessage(), E_USER_WARNING);
			}

			$this->trigger_message('TOPIC_PREFIX_DELETED');
		}

		confirm_box(false, $this->user->lang('DELETE_TOPIC_PREFIX_CONFIRM'), build_hidden_fields([
			'mode'		=> 'manage',
			'action'	=> 'delete',
			'prefix_id'	=> $prefix_id,
			'forum_id'	=> $this->forum_id,
		]));
	}

	/**
	 * Move a prefix up/down
	 *
	 * @param int    $prefix_id The prefix identifier to move
	 * @param string $direction The direction (up|down)
	 * @param int    $amount    The amount of places to move (default: 1)
	 * @return void
	 */
	public function move_prefix($prefix_id, $direction, $amount = 1)
	{
		if (!$this->check_hash($direction . $prefix_id))
		{
			$this->trigger_message('FORM_INVALID', E_USER_WARNING);
		}

		try
		{
			$this->manager->move_prefix($prefix_id, $direction, $amount);
		}
		catch (\OutOfBoundsException $e)
		{
			$this->trigger_message($e->getMessage(), E_USER_WARNING);
		}

		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(['success' => true]);
		}
	}

	/**
	 * Set u_action
	 *
	 * @param string $u_action Custom form action
	 * @return main_controller
	 */
	public function set_u_action($u_action)
	{
		$this->u_action = $u_action;
		return $this;
	}

	/**
	 * Set forum ID
	 *
	 * @param int $forum_id Forum identifier
	 * @return main_controller
	 */
	public function set_forum_id($forum_id)
	{
		$this->forum_id = $forum_id;
		return $this;
	}

	/**
	 * Check link hash helper
	 *
	 * @param string $hash A hashed string
	 * @return bool True if hash matches, false if not
	 */
	protected function check_hash($hash)
	{
		return check_link_hash($this->request->variable('hash', ''), $hash);
	}

	/**
	 * Trigger a message and back link for error/success dialogs
	 *
	 * @param string $message A language key
	 * @param int    $error   Error type constant, optional
	 * @return void
	 */
	protected function trigger_message($message = '', $error = E_USER_NOTICE)
	{
		trigger_error($this->user->lang($message) . adm_back_link("{$this->u_action}&amp;forum_id={$this->forum_id}"), $error);
	}

	/**
	 * Helper for logging topic prefix admin actions
	 *
	 * @param string $tag     The topic prefix tag
	 * @param string $message The log action language key
	 * @return void
	 */
	protected function log($tag, $message)
	{
		$forum_data = $this->get_forum_info($this->forum_id);

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, $message, time(), [$tag, $forum_data['forum_name']]);
	}

	/**
	 * Get a forum's information
	 *
	 * @param int $forum_id
	 * @return mixed Array with the current row, false, if the row does not exist
	 */
	protected function get_forum_info($forum_id)
	{
		if (!class_exists('acp_forums'))
		{
			include $this->root_path . 'includes/acp/acp_forums.' . $this->php_ext;
		}

		$acp_forums = new \acp_forums();

		return $acp_forums->get_forum_info($forum_id);
	}
}
