---
title: ::untagEntity
parentTitle: Mars\Service\TagService


## ::untagEntity

```php
\Mars\Service\TagService::untagEntity(int $eid, int $tag_id)
```

### parameters
- int $eid
- int $tag_id

### return
- \Mars\ParkDto $pack
    - error:
        - eid: must-be-positive-integer
        - tag_id: must-be-positive-integer
        - e_tag: not-found
        - e_tag_vote: not-voted
    - ok

### PHPUnit

```php
```
