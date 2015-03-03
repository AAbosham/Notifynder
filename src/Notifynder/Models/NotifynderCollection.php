<?php namespace Fenos\Notifynder\Models;

use Fenos\Notifynder\Contracts\NotifynderTranslator;
use Fenos\Notifynder\Exceptions\NotificationTranslationNotFoundException;
use Fenos\Notifynder\Parsers\NotifynderParser;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class NotifynderCollection
 *
 * @package Fenos\Notifynder\Models
 */
class NotifynderCollection extends Collection
{

    /**
     * @var NotifynderTranslator
     */
    protected $notifynderTranslator;

    /**
     * @param array $models
     */
    public function __construct($models)
    {
        parent::__construct($models);
    }

    /**
     * Translator instance
     *
     * @return NotifynderTranslator
     */
    public function translator()
    {
        return app('notifynder.translator');
    }

    /**
     * This method translate the body text from
     * another language. It used by collection
     *
     * @param $language (String)
     * @return Collection
     */
    public function translate($language)
    {
        // Loop throught the notifications
        foreach ($this->items as $key => $item) {
            try {
                $translation = $this->translator()
                    ->translate($language, $this->items[$key]['body']['name']);

                $this->items[$key]['body']['text'] = $translation;
            } catch (NotificationTranslationNotFoundException $e) {
                $this->items[$key]['body']['text'];
            }
        }

        $this->parse();

        return $this;
    }

    /**
     * Parse the body of the notification
     *
     * @return $this
     */
    public function parse()
    {
        $parse = new NotifynderParser();

        $notifications = [];

        foreach ($this->items as $key => $item) {
            $notifications[$key] = $item;
            $notifications[$key]['body']['text'] = $parse->parse($item);
        }

        $this->items = $notifications;

        return $this;
    }
}
