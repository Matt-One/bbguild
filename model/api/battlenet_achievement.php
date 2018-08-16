<?php
/**
 * Battle.net WoW API PHP SDK
 * provides data about an individual achievement.
 * @package   bbguild v2.0
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 * @author    Andreas Vandenberghe <sajaki9@gmail.com>
 * @link      https://dev.battle.net/
 * @link      https://github.com/bbDKP
 */

namespace avathar\bbguild\model\api;

use avathar\bbguild\model\api\battlenet_resource;

/**
 * Realm resource.
 *
 *   @package bbguild
 */
class battlenet_achievement extends battlenet_resource
{
	/**
	 * allowed = none in this resource (asterisk)
	 *
	 * @var array
	 */
	protected $methods_allowed = array('*');
	protected $endpoint='achievement';

	/**
	 * @param int $id
	 * @return array
	 */
	public function getAchievementDetail($id)
	{
		global $user;
		https://us.api.battle.net/wow/achievement/2860?locale=en_US&apikey=<key>
		$data = $this->consume(
			$id, array('*')
		);
		return $data;
	}
}
