# Database Change Log


## 0.1.1

1. delete role, user_role
2. entity add owner_uid
3. user_follow, dist_uid -> dst_uid
4. entity owner_eid -> owner_uid

## 0.1.2

1. add table ed_report( date,rqt_count,profuct_count,idea_count,feature_count,invent_count created )

## 0.1.3

1. "user_tag" (id, uid, tag_id, vote_count, changed)
2. "user_tag_vote" (user_tag_id, vote_uid, created)
3. "e_tag" (id, eid, tag_id, vote_count, changed)
4. "e_tag_vote" (e_tag_id, vote_uid, created)

## 0.1.6

1. table e_follow,e_like  delete status and change changed to created
2. add table user_analysis ( uid,followed_coun, following_count,e_following_count,e_like_count )

## 0.1.7

1. "trans" (add locale_id)
2. "tag" (title -> x, zcode -> x, parent)
3. "user_follow" (status -> x, changed -> created)
4. "group" (gid, owner_uid, zcode, status, zcode)
5. "company" (gid, reg_address_id, reg_date, legal_uid)
6. "group_main" (gid, locale, title, content, changed)
7. "group_tag" (id, gid, tag_id, vote_count, changed)
8. "group_tag_vote" (group_tag_id, vote_uid, created)
9. "group_contact" (id, gid, contact_uid, created, name, email, phone, role)
10. "group_website" (id, gid, locale, url, title, changed)
11. "group_social" (id, gid, type, url, changed)
12. "group_office" (id, gid, office_address_id, changed)
13. "address" (id, country_id, state_id, city_id, area_id, street_id, lat, lng)
14. "country_main" (country_id, locale, title, changed)
15. "country" (id, code)
16. "state" (id, country_id)
17. "state_main" (state_id, locale, title, changed)
18. "city" (id, country_id, state_id)
19. "city_main" (city_id, locale, title, changed)
20. "street" (id, country_id, state_id, city_id)
21. "area" (id, country_id, state_id, city_id, parent)
22. "street_main" (street_id, locale, title, changed)
23. "area_main" (area_id, locale, title, changed)
24. "area_street" (id, area_id, street_id)
25. "address_main" (address_id, locale, title, changed)
26. "tag_main" (tag_id, locale, zcode, title, content)
27. "user_profile" (uid, phone, qq, weixin, facebook, twitter, linkedin)
28. "group_user" (id, gid, uid, roles, start_end)

## 0.1.8

Database Reverse Enginer

## 0.1.9

1. "trans" (locale tinyint)
2. "area_main" (title not null, changed not null)
3. "entity_like" drop

## 0.1.10

1. add table article (id, uid, title, content, created, changed, status, locale, original_id, zcode)
2. add table article_tag (id, article_id, tag_id, status, changed)

## 0.1.11
1. e_tag -> entity_tag
2. e_tag_vote -> entity_tag_vote (e_tag_id -> entity_tag_id)
3. user (avt not null, created DEFAULT CURRENT_TIMESTAMP, changed DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP )

## 0.1.12
1. e_follow ->entity_follow
2. e_analysis -> entity_analysis
3. entity_analysis follow_count -> followed_count

## 0.1.13
1. user_analysis e_following_count->entity_following_count,e_like_count->entity_like_count

## 0.1.14
1. tag_dst_vote primary key (tag_dst_id, vote_uid)

## 0.1.15
1. drop article_tag

## 0.1.16
1. e_comment -> comment

## 0.1.17
1. add tabls area and address

## 0.1.20
1. user_analysis add group_following_count
2. follow add tables group_follow

## 0.1.21
1. add tables entity_like
2. drop entity e_like

## 0.1.22
1. add table js_trans_key