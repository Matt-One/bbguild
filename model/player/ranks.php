<?php
/**
 * Ranks class file
 *
 * @package   bbguild v2.0
 * @copyright 2018 avathar.be
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace avathar\bbguild\model\player;

use avathar\bbguild\model\player\guilds;

/**
 * Ranks Class
 *
 * Manages Guildranks, extends the guild class
 *
 *   @package bbguild
 */
class ranks extends guilds
{

	public $bb_ranks_table;
	public $bb_players_table;

	/**
	 * Name of rank
	*
	 * @var string
	 */
	public $RankName;
	/**
	 * id of rank. 0 is highest
	*
	 * @var int
	 */
	public $RankId;
	/**
	 * id of guild
	*
	 * @var int
	 */
	public $RankGuild;
	/**
	 * is rank shown ?
	*
	 * @var int (1 or 0)
	 */
	public $RankHide;
	/**
	 * prefix of rank
	*
	 * @var string
	 */
	public $RankPrefix;
	/**
	 * suffix of rank
	*
	 * @var string
	 */
	public $RankSuffix;

	/**
	 * ranks constructor.
	 * @param string $bb_players_table
	 * @param string $bb_ranks_table
	 * @param int $RankGuild
	 * @param int $RankId
	 */
	public function __construct($bb_players_table, $bb_ranks_table, $RankGuild, $RankId = 0)
	{
		$this->bb_players_table = $bb_players_table;
		$this->bb_ranks_table = $bb_ranks_table;

		if (($RankGuild >= 0 && $RankId = 0) or ($RankGuild == 0 && $RankId = 99) )
		{
			$this->RankGuild=$RankGuild;
			$this->RankId=$RankId;
			$this->Getrank();
		}
		else
		{
			$this->RankGuild=$RankGuild;
			$this->RankId=0;
			$this->RankName='';
			$this->RankHide=0;
			$this->RankPrefix='';
			$this->RankSuffix='';
		}
	}

	/**
	 * gets all info on one rank
	 */
	public function Getrank()
	{
		global $db;
		$sql = 'SELECT rank_name, rank_hide, rank_prefix, rank_suffix
    			FROM ' . $this->bb_ranks_table . '
    			WHERE rank_id = ' . (int) $this->RankId . ' and guild_id = ' . (int) $this->RankGuild;
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$this->RankName = $row['rank_name'];
			$this->RankHide    = $row['rank_hide'];
			$this->RankPrefix = $row['rank_prefix'];
			$this->RankSuffix = $row['rank_suffix'];
		}
		$db->sql_freeresult($result);

	}

	/**
	 * adds a rank
	 */
	public function Makerank()
	{
		global $user, $db, $cache;

		$cache->destroy('sql', $this->bb_ranks_table);

		if ($this->RankName == '')
		{
			trigger_error($user->lang('ERROR_RANK_NAME_EMPTY'), E_USER_WARNING);
		}

		//check if guildid is valid
		if ($this->RankGuild == 0)
		{
			trigger_error($user->lang('ERROR_INVALID_GUILDID'), E_USER_WARNING);
		}

		$sql = 'DELETE FROM ' . $this->bb_ranks_table . '
                   	WHERE rank_id = ' . (int) $this->RankId . '
                   	AND guild_id = ' . (int) $this->RankGuild;
		$db->sql_query($sql);

		// build insert array
		$query = $db->sql_build_array(
			'INSERT', array(
			'rank_id' => (int) $this->RankId ,
			'rank_name' => $this->RankName ,
			'rank_hide' => $this->RankHide ,
			'rank_prefix' => $this->RankPrefix ,
			'rank_suffix' => $this->RankSuffix ,
			'guild_id' => (int) $this->RankGuild)
		);
		// insert new rank
		$db->sql_query('INSERT INTO ' . $this->bb_ranks_table . $query);

		// log the action
		$log_action = array(
		'header' => 'L_ACTION_RANK_ADDED',
		'id' => (int) $this->RankId,
		'L_NAME' => $this->RankName,
		'L_USERCOLOUR' => $user->data['user_colour'],
		'L_ADDED_BY' => $user->data['username']);

		$this->log_insert(
			array(
			'log_type' => $log_action['header'] ,
			'log_action' => $log_action)
		);

		unset($bbguild);
	}

	/**
	 * deletes a rank
	 *
	 * @param  bool $override
	 * @return bool
	 */
	public function Rankdelete($override = false)
	{
		global $user, $db;

		if ($this->countplayers() > 0 )
		{
			return false;
		}

		if (! $override)
		{
			// check if rank is used
			$sql = 'SELECT count(*) as rankcount FROM ' . $this->bb_players_table . ' WHERE
            		 player_rank_id   = ' . (int) $this->RankId . ' and
            		 player_guild_id =  ' . (int) $this->RankGuild;
			$result = $db->sql_query($sql);
			if ((int) $db->sql_fetchfield('rankcount') >= 1)
			{
				trigger_error('Cannot delete rank ' . $this->RankId . '. There are players with this rank in guild . ' . $this->RankGuild, E_USER_WARNING);
			}
		}

		// hardcoded exclusion of ranks 90/99
		$sql = 'DELETE FROM ' . $this->bb_ranks_table . ' WHERE rank_id != 90 and rank_id != 99 and rank_id= ' .
		$this->RankId . ' and guild_id = ' . $this->RankGuild;
		$db->sql_query($sql);

		// log the action

		$log_action = array(
		'header' => 'L_ACTION_RANK_DELETED' ,
		'id' => (int) $this->RankId  ,
		'L_NAME' => $this->RankName ,
		'L_ADDED_BY' => $user->data['username']);

		$this->log_insert(
			array(
			'log_type' => $log_action['header'] ,
			'log_action' => $log_action)
		);

		unset($bbguild);

		return true;

	}

	/**
	 * updates a rank
	 *
	 * @param  ranks $old_rank
	 * @return boolean
	 */
	public function Rankupdate(ranks $old_rank)
	{

		global $user, $db;

		$sql_ary = array(
		'rank_id' => $this->RankId ,
		'guild_id' => $this->RankGuild ,
		'rank_name' => $this->RankName ,
		'rank_hide' => $this->RankHide ,
		'rank_prefix' => $this->RankPrefix ,
		'rank_suffix' => $this->RankSuffix
		);

		$sql = 'UPDATE ' . $this->bb_ranks_table . '
			SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE rank_id=' . (int) $old_rank->RankId . '
			AND guild_id = ' . (int) $old_rank->RankGuild;
		$db->sql_query($sql);

		$log_action = array(
		'header'         => 'L_ACTION_RANK_UPDATED' ,
		'L_NAME_BEFORE' => $old_rank->RankName ,
		'L_HIDE_BEFORE' => $old_rank->RankHide ,
		'L_PREFIX_BEFORE' => $old_rank->RankPrefix ,
		'L_SUFFIX_BEFORE' => $old_rank->RankSuffix ,
		'L_NAME_AFTER' => $this->RankName,
		'L_HIDE_AFTER' => $this->RankHide ,
		'L_PREFIX_AFTER' => $this->RankPrefix ,
		'L_SUFFIX_AFTER' => $this->RankSuffix ,
		'L_UPDATED_BY' => $user->data['username']);

		$this->log_insert(
			array(
			'log_type' => $log_action['header'] ,
			'log_action' => $log_action)
		);

		unset($bbguild);
		return true;

	}

	/**
	 * returns rank array
	*
	 * @return array
	 */
	public function listranks()
	{
		global $db;
		// rank 99 is the out-rank
		$sql = 'SELECT rank_id, rank_name, rank_hide, rank_prefix, rank_suffix, guild_id FROM ' . $this->bb_ranks_table . '
	        		WHERE guild_id = ' . $this->RankGuild . '
	        		ORDER BY rank_id, rank_hide  ASC ';

		return $db->sql_query($sql);

	}

	/**
	 * counts players in guild with a given rank
	 */
	public function countplayers()
	{
		global $db;

		$sql = 'SELECT count(*) as countm FROM ' . $this->bb_players_table . '
			WHERE player_rank_id = ' . $this->RankId . ' and player_guild_id = ' . $this->RankGuild;
		$result = $db->sql_query($sql);
		$countm = (int) $db->sql_fetchfield('countm');
		$db->sql_freeresult($result);

		return $countm;
	}


	/**
	 * updates a wow guild rank list from Battle.NET API -- except guildless
	 *
	 * @param array $playerdata
	 * @param int   $guild_id
	 */
	public function WoWRankFix($playerdata, $guild_id)
	{
		global  $db;

		$newranks = array();

		//init the rank counts per rank
		foreach ($playerdata as $new)
		{
			$newranks[$new['rank']] = 0;
		}

		//count the number of players per rank
		foreach ($playerdata as $new)
		{
			$newranks[$new['rank']] += 1;
		}

		ksort($newranks);

		/* GET OLD RANKS */
		$sql = ' select rank_id from ' . $this->bb_ranks_table . ' WHERE
				 guild_id =  ' . (int) $guild_id . ' and rank_id < 90 order by rank_id ASC';
		$result = $db->sql_query($sql);
		$oldranks = array ();

		while ($row = $db->sql_fetchrow($result))
		{
			$oldranks[(int) $row['rank_id']] = 0;
		}
		$db->sql_freeresult($result);
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$oldranks[(int) $row['rank_id']] += 1;
		}
		$db->sql_freeresult($result);

		// get the new ranks not yet created
		$diff = array_diff_key($newranks, $oldranks);

		foreach ($diff as $key => $count)
		{
			$newrank = new ranks($guild_id);
			$newrank->RankName = 'Rank'.$key;
			$newrank->RankId = $key;
			$newrank->RankGuild = $guild_id;
			$newrank->RankHide = 0;
			$newrank->RankPrefix = '';
			$newrank->RankSuffix = '';
			$newrank->Makerank();
			unset($newrank);
		}

	}

}
