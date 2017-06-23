<?php
namespace Admin\Ui;

class ReportorController extends AdminControllerBase {

    public function index()
    {
        $reportor_set = $this->service('reportor')->getReportors([
            'status' => null
        ]);
        return $this->page('reportor/index', [
            'reportor_set' => $reportor_set,
        ]);
    }


}
