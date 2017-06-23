<?php
namespace Mars\Repo;

use Gap\Repo\RepoBase;

class TagEntityRepo extends RepoBase
{
    public function search($query = [])
    {
        $tag_id = prop($query, 'tag_id', '');
        $tag_zcode = prop($query, 'tag_zcode', '');
        $entity_type_id = prop($query, 'entity_type_id', '');

        $ssb = $this->db->select(['e', '*'])
            ->setDto('entity')
            ->from(['entity_tag', 'et'])
            ->leftJoin(
                ['entity', 'e'],
                ['et', 'eid'],
                '=',
                ['e', 'eid']
            );
        if ($tag_id) {
            $ssb->where('tag_id', '=', $tag_id)
                ->andwhere('entity_type_id', '=', $entity_type_id);
        }

        if ($tag_zcode) {
            $ssb->leftJoin(
                ['tag', 't'],
                ['t', 'id'],
                '=',
                ['et', 'tag_id']
            )
                ->where(['t', 'zcode'], '=', $tag_zcode)
                ->andwhere('entity_type_id', '=', $entity_type_id);
        }

        return $this->dataSet($ssb);

    }

}