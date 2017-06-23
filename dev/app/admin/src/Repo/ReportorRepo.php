<?php
namespace Admin\Repo;

class ReportorRepo extends \Mars\Repo\EntityRepo
{

    public function getDrEntityBd($start = '', $end = '')
    {
        $bd = $this->db->select() // SelectSqlBuilder
            ->from('dr_entity');
        if ($start) {
            $bd->andWhere('date', '>=', $start);
        }

        if ($end) {
            $bd->andWhere('date', '<', $end);
        }
        return $bd;
    }

    public function getDrEntitySet($start = null, $end = null)
    {
        if ($start!=null&&!strtotime($start)) {
            return [];
        }
        if ($end!=null&&!strtotime($end)) {
            return [];
        }
        return $this->dataSet($this->getDrEntityBd($start, $end));
    }
    public function getStatistics()
    {
        return $this->dataSet(
            $this->db->select(
                ['a', 'date'],
                ['b','count_user'],
                ['a','count_rqt'],
                ['a','count_product']
            )
                ->from(['entity_daily_stat', 'a'])
                ->leftJoin(
                    ['user_daily_stat', 'b'],
                    ['a', 'date'],
                    '=',
                    ['b', 'date']
                )->orderBy(['a', 'date', 'desc'])
        );
    }

    public function getAllRqtCount()
    {
        return $this->db->select()
            ->from('entity')
            ->where('type_id', '=', '1')
            ->count();
    }

    public function getAllProductCount()
    {
        return $this->db->select()
            ->from('entity')
            ->where('type_id', '=', '3')
            ->count();
    }

    public function getUnsolvedRqtCount()
    {
        $db = adapter_manager()->get('default');
        $res = $db->query('SELECT count(1) as count FROM `entity` where type_id =1 and eid not in (select rqt_eid from solution) ');
        return $res->fetchOne()->count;
    }

    public function getUnAssoProductCount()
    {
        $db = adapter_manager()->get('default');
        $res = $db->query('SELECT count(1) as count  FROM `entity` where type_id =3 and eid
            not in (select product_eid from property)');
        return $res->fetchOne()->count;
    }

    public function unsolvedProductList($page = 1, $limit = 10)
    {
        $start_page = 0;
        $db = adapter_manager()->get('default');
        $start_page = ($page-1)*$limit;
        $start_page < 0 ? $start_page = 0 : '';
        $res = $db->query("SELECT *  FROM `entity` where type_id =3 and eid
            not in (select product_eid from property) limit $start_page,$limit");
        return  $res->fetchAll();
    }

    public function unassoRqtList($page = 1, $limit = 10)
    {
        $start_page = 0;
        $db = adapter_manager()->get('default');
        $start_page = ($page-1)*$limit;
        $start_page < 0 ? $start_page = 0 : '';
        $res = $db->query("SELECT *  FROM `entity` where type_id =1 and eid
            not in (select rqt_eid from solution) limit $start_page,$limit");
        return  $res->fetchAll();
    }
}
