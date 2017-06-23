<?php
namespace Gap\I18n;

class Translator
{
    protected $locale_id = 0;
    protected $locale_set;
    //protected $locale_set = [];
    //protected $locale_flipped_set = [];
    protected $cache = null;
    protected $adapter = null;
    protected $db_table;

    /*
        $locale_set = [
            'zh-cn' => ['id' => 1, 'title' => '简体中文 - 大陆'],
            'en-us' => ['id' => 2, 'title' => 'English - US'],
            'de-de' => ['id' => 3, 'title' => 'Deutschland - Deutsch'],
            'fr-fr' => ['id' => 4, 'title' => 'France - Français'],
            'zh-tw' => ['id' => 5, 'title' => '繁體中文 - 台湾'],
            'zh-hk' => ['id' => 6, 'title' => '繁體中文 - 香港'],
        ]
     */

    public function __construct($config_db, $config_cache, $locale_set, $db_table = 'trans', $locale_key = '')
    {
        $this->adapter = new \Gap\Database\Pdo\PdoAdapter($config_db);
        $this->cache = new \Gap\Cache\Redis\Cache($config_cache);
        $this->db_table = $db_table ? $db_table : 'trans';

        $this->locale_set = $locale_set;
        $this->setLocaleKey($locale_key);
    }

    public function setLocaleKey($locale_key)
    {
        $this->locale_id = $this->locale_set->getId($locale_key);
        return $this;
    }

    public function getLocaleSet()
    {
        return $this->locale_set->getSet();
    }

    public function getLocaleId()
    {
        return $this->locale_id;
    }

    public function getLocaleKey()
    {
        return $this->locale_set->getKey($this->locale_id);
    }

    public function translate($str)
    {
        if (!$str) {
            return '';
        }
        $locale_id = $this->locale_id;

        if ($trans = $this->cache->hGet($locale_id, $str)) {
            return $trans;
        }
        if ($trans = $this->getTransFromDb($locale_id, $str)) {
            $this->cache->hSet($locale_id, $str, $trans);
            return $trans;
        }
        $trans = ':' . $str;
        $this->set($locale_id, $str, $trans);
        return $trans;
    }

    public function set($locale_id, $str, $trans)
    {
        if (!$locale_id) {
            debug_print_backtrace();
            _debug('locale cannot be empty');
        }

        $this->cache->hSet($locale_id, $str, $trans);

        if ($this->getTransFromDb($locale_id, $str)) {
            $updated = $this->adapter->update($this->db_table)
                ->where('locale_id', '=', $locale_id, 'int')
                ->andWhere('str', '=', $str)
                ->set('trans', $trans)
                ->execute();
            if (!$updated) {
                _debug('trans update failed');
            }

            return;
        }

        $created = $this->adapter->insert($this->db_table)
            ->value('locale_id', $locale_id, 'int')
            ->value('str', $str)
            ->value('trans', $trans)
            ->execute();
        if (!$created) {
            _debug('trans created failed');
        }

    }

    public function getTransByIdFromDb($id)
    {
        $trans_row = $this->adapter->select()
            ->from($this->db_table)
            ->where('id', '=', $id)
            ->limit(1)
            ->fetchOne();
        if ($trans_row) {
            return $trans_row;
        }

        return false;
    }

    public function getTransFromDb($locale_id, $str)
    {
        $trans_row = $this->adapter->select('trans')
            ->from($this->db_table)
            ->where('locale_id', '=', $locale_id)
            ->andWhere('str', '=', $str)
            ->limit(1)
            ->fetchOne();

        if ($trans_row) {
            return $trans_row->trans;
        }

        return false;
    }
}
