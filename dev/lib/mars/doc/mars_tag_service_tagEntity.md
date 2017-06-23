---
title: ::tagEntity
parentTitle: Mars\Service\TagService


## ::tagEntity

```php
\Mars\Service\TagService::tagEntity(int $eid, string $title)
```

### parameters
- int $eid
- string $title

### return
- \Mars\ParkDto $pack
    - error:
        - eid: must-be-positive-integer
        - title: cannot-be-empty
    - ok

### PHPUnit

**danger** $tag_service->clearETags($eid)

```php
public function testTagEntity()
{
    $tag_service = service('tag');

    $entities = service('entity')->schEntitySet()->getItems();
    $entity = $entities[0];

    $eid = $entity->eid;

    $tag_service->clearETags($eid);
    $count = $tag_service->getETagSet($eid)->getItemCount();
    $this->assertEquals(0, $count);

    $tag_title = "    test  ";
    $pack = $tag_service->tagEntity($eid, $tag_title);
    $this->assertInstanceOf('Mars\PackDto', $pack);
    $this->assertTrue($pack->isOk());

    $e_tag_set = $tag_service->getETagSet($eid);
    $e_tag_set->setCountPerPage(0);
    $e_tags = $e_tag_set->getItems();

    $this->assertEquals(1, count($e_tags));
    $tag_service->clearETags($eid);

    $testers = config()->get('tester.available')->all();
    $user_service = service('user');

    $tag_titles = ['test', 'php', '测试', 'Usage', 'Reports'];
    $count = 0;

    foreach ($testers as $nick => $tester) {
        $email = $tester['email'];
        //$password = $tester['password'];
        $user_service->switchUserByEmail($email);

        $index = rand(0, 3);
        $tag_title = $tag_titles[$index];

        $tag_service->tagEntity($eid, $tag_title);
        $count++;
    }

    $e_tag_set = $tag_service->getETagSet($eid);
    $e_tag_set->setCountPerPage(99);
    $e_tags = $e_tag_set->getItems();

    $total_vote_count = 0;
    foreach ($e_tags as $e_tag) {
        $this->assertContains($e_tag->title, $tag_titles);
        $total_vote_count += $e_tag->vote_count;
    }

    $this->assertEquals($count, $total_vote_count);

    $tag_service->clearETags($eid);
}
```
