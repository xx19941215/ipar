<?php
namespace Gap\I18n;

class LocaleSet
{
    protected $set = [];
    protected $flipped = [];

    public function __construct($locale_set)
    {
        $this->set = $locale_set;
    }

    public function getFlipped()
    {
        if ($this->flipped) {
            return $this->flipped;
        }

        foreach ($this->set as $key => $opt) {
            $this->flipped[$opt['id']] = $key;
        }

        return $this->flipped;
    }

    public function getId($key)
    {
        if ($opt = prop($this->set, $key)) {
            return prop($opt, 'id', 0);
        }

        return 0;
    }

    public function getKey($id)
    {
        return prop($this->getFlipped(), $id, 'zh-cn');
    }
}
