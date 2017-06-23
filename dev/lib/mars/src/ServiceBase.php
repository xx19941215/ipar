<?php
namespace Mars;

class ServiceBase {

    use \Gap\Pack\PackTrait;

    protected $service_manager;
    protected $repo_manager;
    protected $errors;
    protected $name;

    protected $current_repo;

    protected $cache;

    protected $cache_name = 'default';

    protected $page_limit = 10;

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function repo($repo_name)
    {
        $this->current_repo = repo_manager()->get($repo_name);
        return $this->current_repo;
    }

    public function cache()
    {
        if (!$this->cache) {
            $this->cache = cache_manager()->get($this->cache_name);
        }
        return $this->cache;
    }

    public function setPageLimit($page_limit)
    {
        $this->page_limit = $page_limit;
    }

    public function getLimitOffset($page_index = 1)
    {
        $page_index = (int) $page_index;
        $page_index = $page_index ? $page_index : 1;
        return [$this->page_limit, ($page_index - 1) * $this->page_limit];
    }

    public function getPageCount($count = 0)
    {
        $count = (int) $count;
        $count = $count ? $count : 0;
        return ceil($count / $this->page_limit);
    }


    // protected functions
    protected function dataSet($select, $data_callback = null, $ct_callback = null)
    {
        return new \Gap\Database\DataSet($select, $data_callback, $ct_callback);
    }


    /*
    protected function deleteCachedUser($uid)
    {
        $this->cache()->delete("user-$uid");
    }
    protected function setCachedUser($uid, $user)
    {
        $this->cache()->set("user-$uid", dto_encode($user));
    }
    protected function getCachedUser($uid, $callback)
    {
        $key = "user-$uid";
        $cache = $this->cache();
        if ($cached = $cache->get($key)) {
            $user = dto_decode($cached, 'user');
        } else {
            $user = $callback();
            $cache->set($key, dto_encode($user));
        }
        return $user;
    }
    protected function getCachedUsers($uids, $callback)
    {
        $flipped = array_flip($uids);
        $not_cached_uids = [];

        $cache = $this->cache();
        foreach ($flipped as $uid => $index) {
            if ($cached = $cache->get("user-$uid")) {
                $flipped[$uid] = dto_decode($cached, 'user');
            } else {
                $not_cached_uids[] = $uid;
            }
        }

        if ($not_cached_uids) {
            $not_cached_users = $callback($not_cached_uids);
            foreach ($not_cached_users as $uid => $user) {
                $cache->set("user-$uid", dto_encode($user));
                $flipped[$uid] = $user;
            }
        }
        return $flipped;
    }
     */

    protected function deleteCachedEntity($eid)
    {
        $this->cache()->delete("entity-$eid");
    }
    protected function setCachedEntity($eid, $entity)
    {
        $this->cache()->set("entity-$eid", entity_encode($entity));
    }
    protected function getCachedEntity($eid, $callback)
    {
        $key = "entity-$eid";
        $cache = $this->cache();
        if ($cached = $cache->get($key)) {
            $entity = entity_decode($cached);
        } else {
            $entity = $callback();
            $cache->set($key, entity_encode($entity));
        }
        return $entity;
    }
    protected function getCachedEntities($eids, $callback)
    {
        $flipped = array_flip($eids);
        $not_cached_eids = [];

        $cache = $this->cache();

        foreach ($flipped as $eid => $index) {
            if ($cached = $cache->get("entity-$eid")) {
                $flipped[$eid] = entity_decode($cached);
            } else {
                $not_cached_eids[] = $eid;
            }
        }

        if ($not_cached_eids) {
            $not_cached_entities = $callback($not_cached_eids);
            foreach ($not_cached_entities as $eid => $entity) {
                $cache->set("entity-$eid", entity_encode($entity));
                $flipped[$eid] = $entity;
            }
        }
        return $flipped;
    }

    protected function deleteCachedDto($dto_name, $dto_id)
    {
        $this->cache()->delete("$dto_name-$dto_id");
    }
    protected function setCachedDto($dto_name, $dto_id, $dto_obj)
    {
        $this->cache()->set("$dto_name-$dto_id", dto_encode($dto_obj));
    }
    protected function getCachedDto($dto_name, $dto_id, $callback)
    {
        //echo 'getCachedDto start \n <br>';
        $key = "$dto_name-$dto_id";
        $cache = $this->cache();
        if ($cached_data = $cache->get($key)) {
            $dto_obj = dto_decode($cached_data, $dto_name);
            //echo 'getCachedDto from cache \n <br>';
        } else {
            $dto_obj = $callback();
            $cache->set($key, dto_encode($dto_obj));
            //echo 'getCachedDto from repo \n <br>';
        }
        return $dto_obj;
    }
    protected function getCachedDtos($dto_name, $dto_ids, $callback)
    {
        $flipped = array_flip($dto_ids);
        $not_cached_ids = [];

        $cache = $this->cache();

        foreach ($flipped as $id => $index) {
            if ($cached = $cache->get("$dto_name-$id")) {
                $flipped[$id] = dto_decode($cached, $dto_name);
            } else {
                $not_cached_ids[] = $id;
            }
        }

        if ($not_cached_ids) {
            $not_cached_dtos = $callback($not_cached_ids);
            foreach ($not_cached_dtos as $id => $dto) {
                $cache->set("$dto_name-$id", dto_encode($dto));
                $flipped[$id] = $dto;
            }
        }
        return $flipped;
    }
    protected function getCachedData($key, $callback)
    {
        $cache = $this->cache();
        $cached_data = $cache->get($key);
        if (!$cached_data) {
            $cached_data = $callback();
            $cache->set($key, $cached_data);
        }
        return $cached_data;
    }




    /*
    public function lastInsertId()
    {
        return $this->current_repo->lastInsertId();
    }
    public function lastInsertZcode()
    {
        return $this->current_repo->lastInsertZcode();
    }
     */
}
