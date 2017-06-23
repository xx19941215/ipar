<?php
namespace Ipar\Ui;

class CompanyServiceController extends IparControllerBase
{
    public function show()
    {
        return $this->page("company_service/show", [
            'count_solved' => service('property_counter')->countSolved(),
            'count_product' => service('product_counter')->countProduct(),
            'count_improving' => service('property_counter')->countImproving()
        ]);
    }
}

?>