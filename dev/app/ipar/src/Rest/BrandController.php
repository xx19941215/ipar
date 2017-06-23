<?php
namespace Ipar\Rest;

class BrandController extends \Gap\Routing\Controller
{
    public function savePost()
    {
        $post = $this->request->request;
        $title = $post->get('title');
        $product_eid = $post->get('product_eid');

        $pack = $this->service('brand')->saveBrandProduct([
            'product_eid' => $product_eid,
            'title' => $title
        ]);
        if ($pack->isOk()) {
            $pack->addItem('brand_product', [
                'brand_product_id' => $pack->getItem('brand_product_id'),
                'brand_title' => $title,
                'vote_count' => 1,
                'has_voted' => true,
                'op_class' => 'unvote',
                'vote_users' => [$this->view('module/user-avt', ['user' => current_user()])]
            ]);
        }
        return $pack;
    }

    public function deletePost()
    {
        $brand_product_id = $this->request->request->get('brand_product_id');
        return $this->service('brand')->deleteBrandProduct(['brand_product_id' => $brand_product_id]);
    }

    public function votePost()
    {
        $brand_service = $this->service('brand');
        $brand_product_id = $this->request->request->get('brand_product_id');
        $pack = $brand_service->voteBrandProduct(
            $brand_product_id
        );
        if ($brand_product = $brand_product->findBrandProduct(['brand_product_id' => $brand_product_id])) {
            $pack->addItem('vote_count', $brand_product->countVote());
            $pack->addItem('vote_users', $this->fetchVoteUsers($brand_product_id));
        }
        return $pack;
    }

    public function listPost()
    {

        $post = $this->request->request;
        $product_eid = $post->get('product_eid');
        $page = $post->get('page', 1);
        $count_per_page = $post->get('cpp', 35);

        $brand_service = $this->service('brand');
        $brand_product_set = $brand_service->schBrandProductSet(['product_eid' => $product_eid]);
        $brand_product_set->setCurrentPage($page);
        $brand_product_set->setCountPerPage($count_per_page);

        $brand_products = [];

        foreach ($brand_product_set->getItems() as $brand_product) {
            $has_voted = $brand_product->hasVoted();
            $brand_main = $brand_product->getBrandMain();
            $brand_products[] = [
                'brand_product_id' => $brand_product->id,
                'brand_title' => $brand_main->title,
                'brand_link' => route_url('ipar-product-brand', ['zcode' => $brand_main->zcode]),
                'vote_count' => $brand_product->countVote(),
                'has_voted' => $has_voted,
                'op_class' => $has_voted ? 'unvote' : 'vote',
            ];
        }
        return $this->packItem('list', $brand_products);
    }

    public function unvotePost()
    {
        $brand_service = $this->service('brand');
        $brand_product_id = $this->request->request->get('brand_product_id');
        $pack = $brand_service->unvoteBrandProduct(
            $brand_product_id
        );
        if ($brand_product = $brand_service->findBrandProduct(['brand_product_id' => $brand_product_id])) {
            $pack->addItem('vote_count', $brand_product->countVote());
            $pack->addItem('vote_users', $this->fetchVoteUsers($brand_product_id));
        }

        return $pack;
    }

    public function voteUsersPost()
    {
        $brand_product_id = $this->request->request->get('brand_product_id');
        $brand_product = $this->service('brand')->findBrandProduct([
            'brand_product_id' => $brand_product_id
        ]);

        return $this->packItems([
            'vote_count' => $brand_product->countVote(),
            'vote_users' => $this->fetchVoteUsers($brand_product_id)
        ]);
    }


    protected function fetchVoteUsers($brand_product_id)
    {
        $vote_set = $this->service('brand')->schBrandProductVoteSet(['brand_product_id' => $brand_product_id]);
        $vote_users = [];
        foreach ($vote_set->getItems() as $vote) {
            $vote_users[] = $this->view('module/user-avt', ['user' => $vote->getVoteUser()]);
        }
        return $vote_users;
    }
}
