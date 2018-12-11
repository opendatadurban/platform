<?php

/**
 * Ushahidi Lumen Session
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Tools;

use Ushahidi\Core\Session;

class LumenSession implements Session
{
    protected $userRepo;
    protected $overrideUserId = false;
    protected $cachedUser = false;

    public function __construct($userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function setUser($userId)
    {
        // Override user id
        $this->overrideUserId = $userId;
    }

    public function getUser()
    {
        // If user override is set
        if ($this->overrideUserId) {
            // Use that
            $userId = $this->overrideUserId;
        } else {
            // Using the OAuth resource server, get the userid (owner id) for this request
            $genericUser = app('auth')->guard()->user();
            $userId = $genericUser ? $genericUser->id : null;
        }

        // If we have no user id return
        if (!$userId) {
            // return an empty user
            return $this->userRepo->getEntity();
        }

        // If we haven't already loaded the user, or the user has changed
        if (!$this->cachedUser || $this->cachedUser->getId() !== $userId) {
            // Using the user repository, load the user
            $this->cachedUser = $this->userRepo->get($userId);
        }

        return $this->cachedUser;
    }
}
