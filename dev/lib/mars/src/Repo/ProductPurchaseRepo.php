<?php

namespace Mars\Repo;

class ProductPurchaseRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'product_purchase';
    protected $dto = 'product_purchase';
    protected $fields = [
        'id' => 'int',
        'eid' => 'int',
        'purchase_url' => 'str',
        'website' => 'str',
        'currency' => 'int',
        'price' => 'str',
        'commission' => 'str',
        'started' => 'str',
        'expired' => 'str'
    ];

    public function search($query = [])
    {
        $ssb = $this->db->select()
            ->from($this->table_name)
            ->setDto($this->dto);

        if ($eid = prop($query, 'eid', '')) {
            $ssb->where('eid', '=', "$eid");
        }

        $ssb->orderBy('id', 'desc');
        return $this->dataSet($ssb);
    }

    public function create($data)
    {
        $this->validate($data);
        if ($this->errors) {
            return $this->packErrors($this->errors);
        }

        $data = $this->preprocess($data);
        $isb = $this->db->insert($this->table_name);
        $hit = 0;

        foreach ($this->fields as $field => $type) {
            if ($value = prop($data, $field)) {
                $isb->value($field, $value, $type);
                $hit++;
            }
        }

        if ($hit <= 0) {
            return $this->packError('data', 'error-data');
        }

        if (!$isb->execute()) {
            return $this->packError($this->table_name, 'insert-failed');
        }

        return $this->packItem('id', $this->db->lastInsertId());
    }

    public function update($query, $data)
    {
        $this->validate($data);
        if ($this->errors) {
            return $this->packErrors($this->errors);
        }

        $data = $this->preprocess($data);
        $usb = $this->db->update($this->table_name);

        if ($this->buildWheres($usb, $query) <= 0) {
            return $this->packError('query', 'error-query');
        }
        $hit = 0;
        foreach ($this->fields as $field => $type) {
            if ($value = prop($data, $field)) {
                $usb->set($field, $value, $type);
                $hit++;
            }
        }
        if ($hit <= 0) {
            return $this->packError('data', 'error-data');
        }

        if (!$usb->execute()) {
            return $this->packError($this->table_name, 'update-failed');
        }

        return $this->packOk();
    }

    public function preprocess($data)
    {
        $url = parse_url($data['purchase_url'])['host'];
        $url_ext = substr($url, strrpos($url, ".") + 1);
        $patt = "/\.$url_ext/";
        $url = preg_replace($patt, "", $url);
        $url_name = substr($url, strrpos($url, ".") + 1);
        $data['website'] = $url_name . "." . $url_ext;
        $data['price'] = money_format('%.2n', $data['price']);
        $data['commission'] = money_format('%.2n', $data['commission']);

        return $data;
    }

    protected function validate($data)
    {
        $purchase_url = prop($data, 'purchase_url');
        if (empty($purchase_url)) {
            $this->addError('purchase-url', 'empty');
            return false;
        }

        $url_length = mb_strlen($purchase_url);
        if ($url_length > 512) {
            $this->addError('purchase_url', ['out-range-%d', 512]);
            return false;
        }

        if (!filter_var($purchase_url, FILTER_VALIDATE_URL)) {
            $this->addError('purchase_url', 'error');
            return false;
        }

        $started = date_create(prop($data, 'started'));
        $expired = date_create(prop($data, 'expired'));
        if (date_diff($started, $expired)->invert > 0) {
            $this->addError('date', 'expired cannot be early than started');
            return false;
        }

        $price = prop($data, 'price');
        if (!is_numeric($price)) {
            $this->addError('price', 'is not a number');
            return false;
        }

        $commission = prop($data, 'commission');
        if (!is_numeric($commission)) {
            $this->addError('commission', 'is not a number');
            return false;
        }
    }
}