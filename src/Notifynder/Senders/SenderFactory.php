<?php namespace Fenos\Notifynder\Senders;

/**
 * Class NotifynderSenderFactory
 *
 * @package Fenos\Notifynder
 */
use Fenos\Notifynder\Contracts\NotifynderCategory;
use Fenos\Notifynder\Contracts\NotifynderGroup;

/**
 * Class SenderFactory
 *
 * @package Fenos\Notifynder\Senders
 */
class SenderFactory
{

    /**
     * @var NotifynderGroup
     */
    protected $notifynderGroup;

    /**
     * @var NotifynderCategory
     */
    private $notifynderCategory;

    /**
     * @param NotifynderGroup    $notifynderGroup
     * @param NotifynderCategory $notifynderCategory
     */
    public function __construct(NotifynderGroup $notifynderGroup,
                         NotifynderCategory $notifynderCategory)
    {
        $this->notifynderGroup = $notifynderGroup;
        $this->notifynderCategory = $notifynderCategory;
    }

    /**
     * Get the right sender when the data is
     * passed
     *
     * @param  array                $infoNotifications
     * @param                       $category
     * @return SendMultiple|SendOne
     */
    public function getSender(array $infoNotifications, $category = null)
    {
        // if the array is multidimesional
        // it means that we want to send
        // multiple notifications
        if ($this->isMultiArray($infoNotifications)) {
            return $this->sendMultiple($infoNotifications);
        } else {
            return $this->sendSingle($infoNotifications, $category);
        }
    }

    /**
     * Send Single Notification Sender
     *
     * @param  array   $infoNotifications
     * @param          $category
     * @return SendOne
     */
    public function sendSingle(array $infoNotifications, $category)
    {
        return new SendOne($infoNotifications, $category);
    }

    /**
     * Send Multiple Notification Sender
     *
     * @param  array        $infoNotifications
     * @return SendMultiple
     */
    public function sendMultiple(array $infoNotifications)
    {
        return new SendMultiple($infoNotifications);
    }

    /**
     * Get the the send group instance
     *
     * @param  string           $group_name
     * @param  array | \Closure $info
     * @return SendGroup
     */
    public function sendGroup($group_name, array $info)
    {
        return new SendGroup(
            $this->notifynderGroup,
            $this->notifynderCategory,
            $group_name,
            $info
        );
    }

    /**
     * Check if the array passed is
     * multidimensional
     *
     * @param $arr
     * @return bool
     */
    protected function isMultiArray(array $arr)
    {
        $rv = array_filter($arr, 'is_array');
        if (count($rv) > 0) {
            return true;
        }

        return false;
    }
}
