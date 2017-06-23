<?php
/**
 * PHP Version 5.6.
 *
 * @category Rest
 *
 * @author Zys <zzyyss@outlook.com>
 * @license http:://www.tecposter.cn/bsd-licence BSD Licence
 *
 * @link http:://www.tecposter.cn/
 **/

namespace Ipar\Rest;

use Gap\Routing\Controller as RoutingController;

class CompanyController extends RoutingController
{
    public function search()
    {

        $company_set = $this->service('company_search')->schCompanySet([
            'query' => $this->request->query->get('query')
        ]);

        $company_set->setCountPerPage(6)->setCurrentPage($this->request->query->get('page'));

        $arr = [];

        foreach ($company_set->getItems() as $company) {
            $item = [];
            $item['gid'] = $company->gid;
            $item['zcode'] = $company->zcode;
            $item['url'] = route_url("ipar-ui-company-show", ['zcode' => $company->zcode]);
            $item['name'] = $company->name;
            $item['avt_url'] = img_src((array)json_decode($company->logo), 'small');
            $item['content'] =  mb_strlen($company->content) > 60 ? mb_substr($company->content, 0, 60) . '...' : $company->content;
        
            $arr[] = $item;

        }


        return $this->packItem('company', $arr);
    }

}
