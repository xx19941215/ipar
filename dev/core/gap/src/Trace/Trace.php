<?php
namespace Gap\Trace;

class Trace {
    protected $level = 0;
    protected $index = 2;
    protected $db;
    //protected $prefix;

    protected $start_time = null;
    protected $current_uid = 0;

    public function __construct($db_config, $level = 0, $index = 3)
    {
        $this->db = new \Gap\Database\Pdo\PdoAdapter($db_config);
        $this->level = $level;
        $this->index = $index;
        //$this->prefix = $config->get('prefix', '');
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function setIndex($index)
    {
        $this->index = $index;
    }

    public function start()
    {
        if ($this->level) {
            $this->start_time = microtime();
        }
    }

    public function end($errors = null)
    {
        if ($this->level <= 0) {
            return;
        }

        $status = $errors ? 0 : 1;

        $backtrace = debug_backtrace();
        $caller = $backtrace[$this->index];

        $uid = (int) $this->getCurrentUid();

        $this->db->beginTransaction();

        if (!$this->db->insert('trace')
            ->value('uid', $uid, 'int')
            ->value('class', $caller['class'])
            ->value('function', $caller['function'])
            ->value('status', $status, 'int')
            //->value('start', $this->getStartTime())
            ->value('elapsed', $this->getElapsed())
            ->execute()
        ) {
            _debug('trace trace insert failed');
            return;
        }

        $trace_id = $this->db->lastInsertId();

        if ($errors) {
            if (!$this->db->insert('trace_errors')
                ->value('trace_id', $trace_id, 'int')
                ->value('errors', json_encode($errors))
                ->execute()
            ) {
                _debug('trace trace_errors insert failed');
                return;
            }
        }
        if ($this->level >= 2) {

            $request = http_request();
            if (!$this->db->insert('trace_http_request')
                ->value('trace_id', $trace_id, 'int')
                ->value('http_request', json_encode([
                    'ips' => json_encode($request->getClientIps()),
                    'referer' => $request->headers->get('referer'),
                    'user_agent' => $request->headers->get('user-agent'),
                    'session' => session()->getId()
                ]))
                ->execute()
            ) {
                _debug('trace trace_http_request insert failed');
                return false;
            }

            if ($this->level >= 3) {
                if ($args = $caller['args']) {
                    if (!$this->db->insert('trace_args')
                        ->value('trace_id', $trace_id, 'int')
                        ->value('args', json_encode($args))
                        ->execute()
                    ) {
                        _debug('trace trace_args insert failed');
                        return false;
                    }
                }
            }
        }
        if (!$this->db->commit()) {
            _debug('trace db commit failed');
        }
    }

    public function setCurrentUid($uid)
    {
        $this->current_uid = $uid;
        return $this;
    }

    protected function getCurrentUid()
    {
        if ($this->current_uid) {
            return $this->current_uid;
        }

        if ($this->current_uid = service('user')->getCurrentUid()) {
            return $this->current_uid;
        }

        if ($this->current_uid = current_uid()) {
            return $this->current_uid;
        }

        return 0;
    }
    protected function getStartTime()
    {
        list($usec, $sec) = explode(' ', $this->start_time);
        $usec = substr(str_replace('0.', '.', $usec), 0, 7);
        return date('Y-m-d H:i:s', $sec) . $usec;
    }

    protected function getElapsed()
    {
        $end = microtime();
        $start = $this->start_time;
        list($end_usec, $end_sec) = explode(' ', $end);
        list($start_usec, $start_sec) = explode(' ', $this->start_time);
        //$end_usec = (float) str_replace('0.', '.', $end_usec);
        //$start_usec = (float) str_replace('0.', '.', $start_usec);
        $elapsed = 1000 * (((float) $end_sec - (float) $start_sec) + ((float) $end_usec - (float) $start_usec));
        return $elapsed;
    }
}
