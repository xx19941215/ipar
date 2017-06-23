<?php
namespace Admin\Service;

//use Gap\Validation\ValidationException;

class ReportorService extends \Mars\Service\EntityService
{

    protected $reportor_repo;

    public function bootstrap()
    {
        $this->reportor_repo = $this->repo('reportor');
    }

    public function getDrEntitySet($start = null, $end = null)
    {
        return $this->reportor_repo->getDrEntitySet($start, $end)->setCountPerPage(100);
    }

    public function getStatistics()
    {
        return $this->reportor_repo->getStatistics();
    }

    public function getAllRqtCount()
    {
        return $this->reportor_repo->getAllRqtCount();
    }

    public function getAllProductCount()
    {
        return $this->reportor_repo->getAllProductCount();
    }

    public function getUnsolvedRqtCount()
    {
        return $this->reportor_repo->getUnsolvedRqtCount();
    }

    public function getUnAssoProductCount()
    {
        return $this->reportor_repo->getUnAssoProductCount();
    }

    public function unassoRqtList($page)
    {
        return $this->reportor_repo->unassoRqtList($page);
    }

    public function unsolvedProductList($page)
    {
        return $this->reportor_repo->unsolvedProductList($page);
    }

    public function getUnsolvedProductCount()
    {
        return $this->reportor_repo->getUnsolvedProductCount();
    }
}
