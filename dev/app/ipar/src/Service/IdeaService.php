<?php
namespace Ipar\Service;

use Gap\Validation\ValidationException;

class IdeaService extends \Mars\Service\EntityService
{
    protected $idea_repo;

    public function bootstrap()
    {
        $this->idea_repo = $this->repo('idea');
        parent::bootstrap();
    }

    public function createIdea(
        $uid,
        $src_eid,
        $content
    ) {
        $uid = (int) $uid;
        $src_eid = (int) $src_eid;

        try {
            $this->validateUid($uid);
            $this->validateSrcEid($src_eid);
            $this->validateContent($content);
        } catch (ValidationException $e) {
            return $this->reportException($e);
        }

        $created = $this->repo('story')->createEntity(
            $uid,
            $src_eid,
            'idea',
            '',
            $content,
            ['rqt_eid' => [$src_eid, 'int']]
        );
        if ($created) {
            return true;
        } else {
            return $this->reportDatabaseError();
        }
    }

    public function editIdea($uid, $eid, $content)
    {
        $uid = (int) $uid;
        $eid = (int) $eid;
        $content = $content;

        if (!$uid) {
            return $this->reportError('uid', 'uid-cannot-be-empty');
        }
        if (!$eid) {
            return $this->reportError('eid', 'eid-cannot-be-empty');
        }
        if (!$content) {
            return $this->reportError('content', 'content-cannot-be-empty');
        }
        $edited = $this->repo('story')->editEntity(
            $uid,
            'idea',
            $eid,
            '',
            $content
        );
        if ($edited) {
            return true;
        } else {
            return $this->reportError('database', $this->translate(':unkown-database-error'));
        }
    }

    public function change(
        $eid,
        $uid,
        $rqt_eid,
        $content
    ) {
        $eid = (int) $eid;
        $uid = (int) $uid;
        $rqt_eid = (int) $rqt_eid;

        try {
            $this->validateEid($eid);
            $this->validateUid($uid);
            $this->validateSrcEid($rqt_eid);
            $this->validateContent($content);
        } catch (ValidationException $e) {
            return $this->reportException($e);
        }

        $changed = $this->idea_repo->change(
            $eid,
            $uid,
            $rqt_eid,
            $content
        );
        if ($changed) {
            return true;
        } else {
            return $this->reportDatabaseError();
        }
    }

    public function remove($eid)
    {
        return $this->repo('entity')->remove($eid);
    }

    public function updateIdea($eid,  $content = '')
    {
        $eid = (int) $eid;
        //$this->deleteCachedEntity($eid);
        return $this->idea_repo->updateIdea($eid,  $content);
    }


    public function schIdeaSet($opts = [])
    {
        return $this->idea_repo->schIdeaSet(['type_key' => 'idea']);
    }

    public function getIdeaByZcode($zcode)
    {
        return $this->idea_repo->getIdeaByZcode($zcode);
    }

    public function getRqtByIdeaEid($idea_eid)
    {
        return $this->idea_repo->getRqtByIdeaEid($idea_eid);
    }
    /**
     * protected
     */

    protected function validateSrcEid($src_eid)
    {
        if (!$src_eid) {
            $e = new ValidationException($this->translate(':src_eid-cannot-be-empty'));
            $e->setName('src_eid');
            throw $e;
        }
    }

    public function deleteIdea($eid)
    {
        $eid = (int) $eid;
        if (!$eid) {
            return $this->packError('eid', 'must-be-positive-integer');
        }

        $this->deleteCachedEntity($eid);
        return $this->idea_repo->deleteIdea($eid);
    }

}
