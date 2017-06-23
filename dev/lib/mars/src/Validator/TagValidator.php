<?php
namespace Mars\Validator;

class TagValidator extends ValidatorBase
{
    public function validateTagDst($dst_type, $dst_id, $title)
    {
        if ((int) $dst_type <= 0) {
            return $this->packError('dst_type', 'not-positive');
        }

        if ((int) $dst_id <= 0) {
            return $this->packError('dst_id', 'not-positive');
        }

        return $this->validateTitle($title);
    }

    public function validateTitle($title)
    {
        if (empty($title)) {
            return $this->packEmpty('title');
        }
        return true;
    }
}
