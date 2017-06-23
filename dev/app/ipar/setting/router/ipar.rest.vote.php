<?php
$this
    ->setCurrentSite('api')
    ->setCurrentAccess('login')

    ->postRest('/vote/solution', ['as' => 'ipar-rest-vote-solution', 'action' => 'Ipar\Rest\VoteController@voteSolutionPost'])
    ->postRest('/unvote/solution', ['as' => 'ipar-rest-unvote-solution', 'action' => 'Ipar\Rest\VoteController@unvoteSolutionPost'])
    ->postRest('/solution/users', ['as' => 'ipar-rest-solution-users', 'action' => 'Ipar\Rest\VoteController@getSolutionVoteUsers'])

    ->postRest('/vote/property', ['as' => 'ipar-rest-vote-property', 'action' => 'Ipar\Rest\VoteController@votePropertyPost'])
    ->postRest('/unvote/property', ['as' => 'ipar-rest-unvote-property', 'action' => 'Ipar\Rest\VoteController@unvotePropertyPost'])
    ->postRest('/property/users', ['as' => 'ipar-rest-property-users', 'action' => 'Ipar\Rest\VoteController@getPropertyVoteUsers'])
;
