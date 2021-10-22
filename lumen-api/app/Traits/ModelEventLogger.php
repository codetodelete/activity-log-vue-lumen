<?php

namespace App\Traits;

use Illuminate\Support\Facades\Request as Request;
use App\Models\Log as LogActivityModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Class ModelEventLogger
 * @package App\Traits
 *
 *  Automatically Log Add, Update, Delete events of Model.
 */
trait ModelEventLogger {

    /**
     * Automatically boot with Model, and register Events handler.
     */
    protected static function bootModelEventLogger()
    {   
        foreach (static::getRecordActivityEvents() as $eventName) {
            static::$eventName(function ($model) use ($eventName) {
                try {
                    $reflect = new \ReflectionClass($model);
                    $log = [];
                    $log['subject'] = ucfirst($eventName) . " a " . $reflect->getShortName();
                    $log['url'] = Request::fullUrl();
                    $log['method'] = Request::method();
                    $log['action'] = static::getActionName($eventName);
                    $log['detail'] = json_encode($model->getDirty());
                    $log['ip'] = Request::ip();
                    $log['agent'] = Request::header('user-agent');

                    return LogActivityModel::create($log);

                } catch (\Exception $e) {
                    Log::debug($e->getMessage());
                }
            });
        }
    }

    /**
     * Set the default events to be recorded if the $recordEvents
     * property does not exist on the model.
     *
     * @return array
     */
    protected static function getRecordActivityEvents()
    {
        if (isset(static::$recordEvents)) {
            return static::$recordEvents;
        }

        return [
            'created',
            'updated',
            'deleted'
        ];
    }

    /**
     * Return Suitable action name for Supplied Event
     *
     * @param $event
     * @return string
     */
    protected static function getActionName($event)
    {
        switch (strtolower($event)) {
            case 'created':
                return 'create';
                break;
            case 'updated':
                return 'update';
                break;
            case 'deleted':
                return 'delete';
                break;
            default:
                return 'unknown';
        }
    }
} 