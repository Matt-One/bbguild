<?php
/**
 * bbDKP database installer
 *
 * @package   bbguild v2.0
 * @copyright 2018 avathar.be
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace avathar\bbguild\migrations;

use phpbb\db\migration\migration;

/**
 * Migration stage 1: Initial schema
 */
class release_2_0_0_m01_schema extends migration
{
	protected $table_prefix;
	protected $bbgames_table;
	protected $news_table;
	protected $bblogs_table;
	protected $player_ranks_table;
	protected $player_list_table;
	protected $class_table;
	protected $race_table;
	protected $faction_table;
	protected $guild_table;
	protected $bb_language;
	protected $motd_table;
	protected $bbrecruit_table;
	protected $bb_gamerole_table;
	protected $plugins_table;
	protected $achievement_table;
	protected $achievement_track_table;
	protected $achievement_criteria_table;
	protected $achievement_rewards_table;
	protected $bb_relations_table;
	protected $criteria_track_table;

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\gold');
	}

	/**
	 * Add the bbguild table schema to the database:
	 *
	 * @return array Array of table schema
	 */
	public function update_schema()
	{
		$this->GetTablenames();

		return array(
			'add_tables'    => array(
				/*1*/
				$this->news_table    => array(
					'COLUMNS'    => array(
						'news_id'           => array('UINT', null, 'auto_increment'),
						'news_headline'     => array('VCHAR_UNI', ''),
						'news_message'      => array('TEXT_UNI', ''),
						'news_date'         => array('TIMESTAMP', 0),
						'user_id'           => array('UINT', 0),
						'bbcode_bitfield'   => array('VCHAR:20', ''),
						'bbcode_uid'        => array('VCHAR:8', ''),
						'bbcode_options'    => array('VCHAR:8', '')
					),
					'PRIMARY_KEY'    => 'news_id',
				),
				/*2*/
				$this->bb_language    => array(
					'COLUMNS'    => array(
						'id'                => array('UINT', null, 'auto_increment'),
						'game_id'           => array('VCHAR:10', ''),
						'attribute_id'      => array('UINT', 0),
						'language'          => array('CHAR:2', ''),
						'attribute'         => array('VCHAR:30', ''),
						'name'              => array('VCHAR_UNI:255', ''),
						'name_short'        => array('VCHAR_UNI:255', ''),
					),
					'PRIMARY_KEY'     => array('id'),
					'KEYS'            => array('UQ01' => array('UNIQUE', array('game_id', 'attribute_id', 'language', 'attribute')),
					)),
				/*3*/
				$this->faction_table  => array(
					'COLUMNS'    => array(
						'game_id'           => array('VCHAR:10', ''),
						'f_index'           => array('USINT', null, 'auto_increment'),
						'faction_id'        => array('USINT', 0),
						'faction_name'      => array('VCHAR_UNI', ''),
						'faction_hide'      => array('BOOL', 0),
					),
					'PRIMARY_KEY'    => 'f_index',
					'KEYS'         => array('UQ02'    => array('UNIQUE',  array('game_id', 'faction_id'))),
				),
				/*4*/
				$this->class_table    => array(
					'COLUMNS'    => array(
						'c_index'           => array('USINT', null, 'auto_increment'),
						'game_id'           => array('VCHAR:10', ''),
						'class_id'          => array('USINT', 0),
						'class_faction_id'  => array('USINT', 0),
						'class_min_level'   => array('USINT', 0),
						'class_max_level'   => array('USINT', 0),
						'class_armor_type'  => array('VCHAR_UNI', ''),
						'class_hide'        => array('BOOL', 0),
						'imagename'         => array('VCHAR:255', ''),
						'colorcode'         => array('VCHAR:10', ''),
					),
					'PRIMARY_KEY'    => 'c_index',
					'KEYS'         => array('UQ01'    => array('UNIQUE', array('game_id', 'class_id'))),
				),

				/*5*/
				$this->race_table    => array(
					'COLUMNS'    => array(
						'game_id'           => array('VCHAR:10', ''),
						'race_id'           => array('USINT', 0),
						'race_faction_id'   => array('USINT', 0),
						'race_hide'         => array('BOOL', 0),
						'image_female'      => array('VCHAR:255', ''),
						'image_male'        => array('VCHAR:255', ''),
					),
					'PRIMARY_KEY'    => array('game_id', 'race_id') ,
				),

				/*6*/
				$this->guild_table    => array(
					'COLUMNS'    => array(
						'id'                => array('USINT', 0),
						'name'              => array('VCHAR_UNI:255', ''),
						'realm'             => array('VCHAR_UNI:255', ''),
						'region'            => array('VCHAR:3', ''),
						'battlegroup'       => array('VCHAR:255', ''),
						'roster'            => array('BOOL', 0),
						'aion_legion_id'    => array('USINT', 0),
						'aion_server_id'    => array('USINT', 0),
						'level'             => array('UINT', 0),
						'players'           => array('UINT', 0),
						'achievementpoints' => array('UINT', 0),
						'guildarmoryurl'    => array('VCHAR:255', ''),
						'emblemurl'         => array('VCHAR:255', ''),
						'game_id'           => array('VCHAR:10', ''),
						'min_armory'        => array('UINT', 90),
						'rec_status'        => array('BOOL', 0),
						'guilddefault'      => array('BOOL', 0),
						'armory_enabled'    => array('BOOL', 0),
						'armoryresult'      => array('VCHAR_UNI:255', ''),
						'recruitforum'      => array('UINT', 0),
						'faction'           => array('UINT', 0),
					),
					'PRIMARY_KEY'      => array('id'),
					'KEYS'         => array('UQ01'    => array('UNIQUE', array('name', 'id') )),
				),

				/*7*/
				$this->player_ranks_table    => array(
					'COLUMNS'    => array(
						//rank_id is not auto-increment
						'guild_id'          => array('USINT', 0),
						'rank_id'           => array('USINT', 0),
						'rank_name'         => array('VCHAR_UNI:50', ''),
						'rank_hide'         => array('BOOL', 0),
						'rank_prefix'       => array('VCHAR:75', ''),
						'rank_suffix'       => array('VCHAR:75', ''),
					),
					'PRIMARY_KEY'    => array('rank_id', 'guild_id'),
				),

				/*8*/
				$this->player_list_table => array(
					'COLUMNS'    => array(
						'player_id'         => array('UINT', null, 'auto_increment'),
						'game_id'           => array('VCHAR:10', ''),
						'player_name'       => array('VCHAR_UNI:100', ''),
						'player_region'     => array('VCHAR', ''),
						'player_realm'      => array('VCHAR:30', ''),
						'player_title'      => array('VCHAR_UNI:100', ''),
						'player_level'      => array('USINT', 0),
						'player_race_id'    => array('USINT', 0),
						'player_class_id'   => array('USINT', 0),
						'player_rank_id'    => array('USINT', 0),
						'player_role'       => array('VCHAR:20', ''),
						'player_comment'    => array( 'TEXT_UNI' , ''),
						'player_joindate'   => array('TIMESTAMP', 0),
						'player_outdate'    => array('TIMESTAMP', 0),
						'player_guild_id'   => array('USINT', 0),
						'player_gender_id'  => array('USINT', 0),
						'player_achiev'     => array('UINT', 0),
						'player_armory_url' => array('VCHAR:255', ''),
						'player_portrait_url' => array('VCHAR', ''),
						'phpbb_user_id'     => array('UINT', 0),
						'player_status'     => array('BOOL', 0) ,
						'deactivate_reason' => array('VCHAR_UNI:255', ''),
						'last_update'       => array('TIMESTAMP', 0)

					),
					'PRIMARY_KEY'  => 'player_id',
					'KEYS'         => array('UQ01'    => array('UNIQUE', array('player_guild_id', 'player_name', 'player_realm'))),
				),

				/*9*/
				$this->motd_table        => array(
					'COLUMNS'    => array(
						'motd_id'         => array('INT:8', null, 'auto_increment'),
						'motd_title'      => array('VCHAR_UNI', ''),
						'motd_msg'        => array('TEXT_UNI', ''),
						'motd_timestamp'  => array('TIMESTAMP', 0),
						'bbcode_bitfield' => array('VCHAR:255', ''),
						'bbcode_uid'      => array('VCHAR:8', ''),
						'user_id'         => array('INT:8', 0),
						'bbcode_options'  => array('UINT', 7),
					),
					'PRIMARY_KEY'    => 'motd_id'
				),

				/*10*/
				$this->bbgames_table    => array(
					'COLUMNS'    => array(
						'id'             => array('UINT', null, 'auto_increment'),
						'game_id'        => array('VCHAR:10', ''),
						'game_name'      => array('VCHAR_UNI:255', ''),
						'region'         => array('VCHAR:3', ''),
						'status'         => array('VCHAR:30', ''),
						'imagename'      => array('VCHAR:20', ''),
						'armory_enabled' => array('UINT', 0),
						'bossbaseurl'    => array('VCHAR:255', ''),
						'zonebaseurl'    => array('VCHAR:255', ''),
						'apikey'         => array('VCHAR:255', ''),
						'apilocale'      => array('VCHAR:5', ''),
						'privkey'        => array('VCHAR:255', '')
					),
					'PRIMARY_KEY'     => array('id'),
					'KEYS'            => array('UQ01' => array('UNIQUE', array('game_id')))
				),
				/*11*/
				$this->bb_gamerole_table    => array(
					'COLUMNS'    => array(
						'role_pkid'        => array('INT:8', null, 'auto_increment'),
						'game_id'          => array('VCHAR:10', ''),
						'role_id'          => array('INT:8', 0),
						'role_color'       => array('VCHAR', ''),
						'role_icon'        => array('VCHAR', ''),
						'role_cat_icon'    => array('VCHAR', ''),
					),
					'PRIMARY_KEY'    => 'role_pkid',
					'KEYS'         => array('UQ01'    => array('UNIQUE', array('game_id', 'role_id')))
				),
				/*12*/
				$this->bbrecruit_table    => array(
					'COLUMNS'    => array(
						'id'               => array('INT:8', null, 'auto_increment'),
						'guild_id'         => array('USINT', 0),
						'role_id'          => array('INT:8', 0),
						'class_id'         => array('UINT',  0),
						'level'            => array('UINT',  0),
						'positions'        => array('USINT', 0),
						'applicants'       => array('USINT', 0),
						'status'           => array('USINT', 0),
						'applytemplate_id' => array('UINT',  0),
						'last_update'      => array('TIMESTAMP', 0),
						'note'             => array('TEXT_UNI', ''),
					),
					'PRIMARY_KEY'          => 'id',
				),

				/*13*/
				$this->bblogs_table    => array(
					'COLUMNS'    => array(
						'log_id'        => array('UINT', null, 'auto_increment'),
						'log_date'      => array('TIMESTAMP', 0),
						'log_type'      => array('VCHAR_UNI:255', ''),
						'log_action'    => array('TEXT_UNI', ''),
						'log_ipaddress' => array('VCHAR:45', ''), // ipv6 is 45 char
						'log_sid'       => array('VCHAR:32', ''),
						'log_result'    => array('VCHAR', ''),
						'log_userid'    => array('UINT', 0),
					),
					'PRIMARY_KEY'  => 'log_id',
					'KEYS'         => array(
						'I01'    => array('INDEX', 'log_userid'),
						'I02'    => array('INDEX', 'log_type'),
						'I03'    => array('INDEX', 'log_ipaddress')),
				),

				/* entity achievements */
				/*14*/
				$this->achievement_table    => array(
					'COLUMNS'    => array(
						'id'              => array('UINT', 0),
						'game_id'         => array('VCHAR:10', ''),
						'title'           => array('VCHAR_UNI:255', ''),
						'points'          => array('UINT', 0),
						'description'     => array('VCHAR_UNI:255', ''),
						'icon'            => array('VCHAR_UNI:255', ''),
						'factionid'       => array('BOOL', 0),
						'reward'          => array('VCHAR_UNI:255', ''),
					),
					'PRIMARY_KEY'  => 'id',
				),
				/*15 entity achievements criteria */
				$this->achievement_criteria_table    => array(
					'COLUMNS'    => array(
						'criteria_id'   => array('UINT', 0),
						'description'   => array('VCHAR_UNI:255', ''),
						'orderIndex'    => array('UINT', 0),
						'max'           => array('TIMESTAMP', 0)
					),
					'PRIMARY_KEY'  => 'criteria_id',
				),
				/*16 entity achievements rewards */
				$this->achievement_rewards_table    => array(
					'COLUMNS'    => array(
						'rewards_item_id'   => array('UINT', 0),
						'description'       => array('VCHAR_UNI:255', ''),
						'itemlevel'         => array('UINT', 0),
						'quality'           => array('UINT', 0),
						'icon'              => array('VCHAR:255', 0),
					),
					'PRIMARY_KEY'  => 'rewards_item_id',
				),

				/*17 is the relation table between achievement and criterium/rewards */
				$this->bb_relations_table    => array(
					'COLUMNS'    => array(
						'id'              => array('UINT', null, 'auto_increment'),
						'attribute_id'    => array('VCHAR:3', ''),
						'rel_attr_id'     => array('VCHAR:3', ''),
						'att_value'       => array('UINT', 0),
						'rel_value'       => array('UINT', 0),
					),
					'PRIMARY_KEY'  => 'id',
					'KEYS'         => array('UQ01'    => array('UNIQUE', array('attribute_id', 'rel_attr_id', 'att_value', 'rel_value'))),
				),

				/*18 time achievement reached */
				$this->achievement_track_table    => array(
					'COLUMNS'   => array(
						'guild_id'               => array('USINT', 0),
						'player_id'              => array('UINT', 0),
						'achievement_id'         => array('UINT', 0),
						'achievements_completed' => array('BINT', 0),
					),
					'PRIMARY_KEY'   => array('guild_id', 'player_id', 'achievement_id'),
				),

				/*19 time criterium was achieved */
				$this->criteria_track_table    => array(
					'COLUMNS'    => array(
						'guild_id'               => array('USINT', 0),
						'player_id'              => array('UINT', 0),
						'criteria_id'            => array('UINT', 0),
						'criteria_quantity'      => array('BINT', 0),
						'criteria_created'       => array('BINT', 0),
						'criteria_timestamp'     => array('BINT', 0),
					),
					'PRIMARY_KEY'   => array('guild_id', 'player_id', 'criteria_id'),
				),

				/*20 extension plugins - nothing at this time */
				$this->plugins_table    => array(
					'COLUMNS'       => array(
						'name'              => array('VCHAR_UNI:255', ''),
						'value'             => array('BOOL', 0),
						'version'           => array('VCHAR:50', ''),
						'installdate'       => array('BINT', 0),
						'orginal_copyright' => array('VCHAR_UNI:150', ''),
						'bbdkp_copyright'   => array('VCHAR_UNI:150', ''),
					),
					'PRIMARY_KEY'   => array('name'),
				),
			),
		);
	}

	/**
	 * Drop the bbguild table schema from the database
	 *
	 * @return array Array of table schema
	 * @access public
	 */
	public function revert_schema()
	{
		$this->GetTablenames();

		return array(
			'drop_tables'    => array(
				$this->news_table,
				$this->bb_language,
				$this->faction_table,
				$this->class_table,
				$this->race_table,
				$this->guild_table,
				$this->player_ranks_table,
				$this->player_list_table,
				$this->motd_table,
				$this->bbgames_table,
				$this->bb_gamerole_table,
				$this->bbrecruit_table,
				$this->bblogs_table,
				$this->plugins_table,
				$this->achievement_table,
				$this->achievement_track_table,
				$this->achievement_criteria_table,
				$this->achievement_rewards_table,
				$this->bb_relations_table,
				$this->criteria_track_table
			),
		);
	}

	/**
	 * populate table names
	 */
	public function GetTablenames()
	{
		$this->bbgames_table      = $this->table_prefix  . 'bb_games';
		$this->news_table         = $this->table_prefix  . 'bb_news';
		$this->bblogs_table       = $this->table_prefix  . 'bb_logs';
		$this->player_ranks_table = $this->table_prefix  . 'bb_ranks';
		$this->player_list_table  = $this->table_prefix  . 'bb_players';
		$this->class_table        = $this->table_prefix  . 'bb_classes';
		$this->race_table         = $this->table_prefix  . 'bb_races';
		$this->faction_table      = $this->table_prefix  . 'bb_factions';
		$this->guild_table        = $this->table_prefix  . 'bb_guild';
		$this->bb_language        = $this->table_prefix  . 'bb_language';
		$this->motd_table         = $this->table_prefix  . 'bb_motd';
		$this->bbrecruit_table    = $this->table_prefix  . 'bb_recruit';
		$this->bb_gamerole_table  = $this->table_prefix  . 'bb_gameroles';
		$this->plugins_table      = $this->table_prefix  . 'bb_plugins';
		$this->achievement_table  = $this->table_prefix  . 'bb_achievement';
		$this->achievement_track_table = $this->table_prefix  . 'bb_achievement_track';
		$this->achievement_criteria_table = $this->table_prefix  . 'bb_achievement_criteria';
		$this->achievement_rewards_table = $this->table_prefix  . 'bb_achievement_rewards';
		$this->bb_relations_table = $this->table_prefix  . 'bb_relations_table';
		$this->criteria_track_table = $this->table_prefix  . 'bb_criteria_track';

	}

}
