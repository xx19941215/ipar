<?php
namespace Admin\Ui;

class StatisticsController extends AdminControllerBase
{
    public function list()
    {
        $page = $this->request->get('page');
        $statisticsSet = $this->service('reportor')->getStatistics();
        $pageCount = $statisticsSet->getPageCount();
        $statisticsSet = $statisticsSet->setCurrentPage($page)->getItems();
        $count_rqt_all = $this->service('reportor')->getAllRqtCount();
        $count_product_all = $this->service('reportor')->getAllProductCount();
        $count_rqt_unsolved = $this->service('reportor')->getUnsolvedRqtCount();
        $count_product_unasso = $this->service('reportor')->getUnAssoProductCount();
        return $this->page(
            'statistics/list.phtml',
            [
                'statisticsSet' => $statisticsSet,
                'count_rqt_all' => $count_rqt_all,
                'count_product_all' => $count_product_all,
                'count_rqt_unsolved' => $count_rqt_unsolved,
                'count_product_unasso' => $count_product_unasso,
                'pageCount' => $pageCount
            ]
        );
    }

    public function unsolvedProductList()
    {
        $page = $this->request->query->get('page');
        $count_product_unasso = $this->service('reportor')->getUnAssoProductCount();
        $unsolved_rqt_set = $this->service('reportor')->unsolvedProductList($page);
        return $this->page(
            'statistics/unsolved-product',
            [
                'unsolved_rqt_set' => $unsolved_rqt_set,
                'pageCount' => ceil($count_product_unasso / 10)
            ]
        );
    }

    public function unassoRqtList()
    {
        $page = $this->request->query->get('page');
        $count_rqt_unsolved = $this->service('reportor')->getUnsolvedRqtCount();
        $unsolved_rqt_set = $this->service('reportor')->unassoRqtList($page);
        return $this->page(
            'statistics/unsolved-product',
            [
                'unsolved_rqt_set' => $unsolved_rqt_set,
                'pageCount' => ceil($count_rqt_unsolved / 10)
            ]
        );
    }
}
