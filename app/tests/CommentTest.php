<?php
/**
 * Created by PhpStorm.
 * User: anhtd
 * Date: 01/07/2014
 * Time: 15:13
 */
class CommentTest extends ApiTestCase
{
    protected $_session;
    protected $_user_id;
    //test cases for Login by email - password successfully

    public function __construct() {
        parent::__construct();
        $this->_params = array(
            'id' => '2',
            'user_id' => '',
            'rating_id' => '1',
            'content' => 'this is comment 2',
        );
        $this->_method = 'POST';
        $this->_uri = 'api/comment/1';
        $this->_models = array('Comment', 'User', 'Login');
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpData();
        $this->setUpRating();
        $this->setUpProfile();
        $comment = new Comment();
        $comment->id = 1;
        $comment->user_id = $this->_user_id;
        $comment->rating_id = 1;
        $comment->content = "this is comment test";
        $comment->save();
        
    }

    public function testGetListCommentSuccess()
    {
        $response = $this->call('GET', 'api/comment/1');

        $comment_infor = Comment::all();
        if(count($comment_infor) > 0){
            foreach ($comment_infor as $comments) {
                $profile = Profile::where('user_id', $comments->user_id)->first();
                if($profile->image != null) {
                    $comments->avatar_user = URL::asset($profile->image);
                } else {
                    $comments->avatar_user = $profile->image;
                }
                $comments->first_name = $profile->first_name;
                $comments->last_name = $profile->last_name;
            }
            $data = $comment_infor->toArray();
        } else {
            $data = '';
        }
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $comment_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testGetListCommentSuccessNoComment()
    {
        $comment = Comment::destroy(1);
        $response = $this->call('GET', 'api/comment/1');

        $comment_infor = Comment::all();
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $comment_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testGetListCommentErrorNoRating()
    {
        $response = $this->call('GET', 'api/comment/5');

        $this->assertEquals(
            array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))
        , json_decode($response->getContent(), true));
    }

    public function testGetCommentDetailSuccess()
    {
        $response = $this->call('GET', 'api/comment/1/1');
        $comment_infor = Comment::where('id', 1)->first();
        $profile = Profile::where('user_id', $comment_infor->user_id)->first();
        if($profile->image != null) {
            $comment_infor->avatar_user = URL::asset($profile->image);
        } else {
            $comment_infor->avatar_user = $profile->image;
        }
        $comment_infor->first_name = $profile->first_name;
        $comment_infor->last_name = $profile->last_name;
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $comment_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testGetCommentDetailErrorNoComment() 
    {
        $comment = Comment::destroy(1);
        $response = $this->call('GET', 'api/comment/1/1');
        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_COMMENT, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_COMMENT))
        , json_decode($response->getContent(), true));

    }
     public function testGetCommentDetailErrorWrongRating() 
    {
        $response = $this->call('GET', 'api/comment/wrong_rating_id/1');
        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))
        , json_decode($response->getContent(), true));

    }

    public function testCreateCommentSuccess()
    {
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;

        $response = $this->_getAuth($_params);
        //get created login information
        $comment_infor = Comment::get(array('user_id','rating_id', 'content', 'updated_at', 'created_at','id'))->last();
        $profile = Profile::where('user_id', $comment_infor->user_id)->first();
        if($profile->image != null) {
            $comment_infor->avatar_user = URL::asset($profile->image);
        } else {
            $comment_infor->avatar_user = $profile->image;
        }
        $comment_infor->first_name = $profile->first_name;
        $comment_infor->last_name = $profile->last_name;
        $this->assertNotNull($comment_infor);
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $comment_infor->toArray())
        , json_decode($response->getContent(), true));
    }
    public function testCreateCommentErrorWrongRating()
    {
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        $this->_uri = 'api/comment/wrong_rating_id';
        $response = $this->_getAuth($_params);
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))
        , json_decode($response->getContent(), true));
    }
    
    public function testCreateCommentErrorMissingContent()
    {
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        unset($_params['content']);
        $response = $this->_getAuth($_params);
        empty($_params);
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::MISSING_PARAMS, "data" => $_params)
        , json_decode($response->getContent(), true));
    }

    public function testUpdateCommentSuccess()
    {
        $_params = $this->_params;
        $response = $this->action('POST', 'CommentController@update', array('rating_id' => 1, 'id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        //get created login information
        $comment_infor = Comment::where('id', 1)->first();
        $this->assertEquals(
            array("code" => ApiResponse::OK, "data" => $comment_infor->toArray())
        , json_decode($response->getContent(), true));
    }

    public function testUpdateCommentErrorWrongRating()
    {
        $_params = $this->_params;
        $response = $this->action('POST', 'CommentController@update', array('rating_id' => "wrong_rating_id",'id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        //get created login information
       $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))
        , json_decode($response->getContent(), true));
    }

    public function testUpdateCommentErrorNoComment()
    {
        $Comment_destroy = Comment::destroy(1);
        $_params = $this->_params;
        $response = $this->action('POST', 'CommentController@update', array('rating_id' => 1, 'id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        //get created login information
       $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_COMMENT, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_COMMENT))
        , json_decode($response->getContent(), true));
    }

    public function testUpdateCommentErrorMissingContent()
    {
        $_params = $this->_params;
        $_params['user_id'] = $this->_user_id;
        unset($_params['content']);
        $response = $this->action('POST', 'CommentController@update', array('rating_id' => 1, 'id' => 1), array('data' => json_encode($_params), '_method' => 'PUT'));
        //get created login information
        $this->assertEquals(
            array("code" => ApiResponse::MISSING_PARAMS, "data" => $_params)
        , json_decode($response->getContent(), true));
    }

    public function testDeleteCommentSuccess()
    {
        $response = $this->action('delete', 'CommentController@destroy', array('rating_id' => 1, 'id' => 1));
        $this->assertEquals(array("code" => ApiResponse::OK, "data" => "Comment deleted")
         , json_decode($response->getContent(), true));
    }

    public function testDeleteCommentWrongRating()
    {
        $response = $this->action('delete', 'CommentController@destroy', array('rating_id' => "wrong_rating_id", 'id' => 1));
        $this->assertEquals(
            array("code" => ApiResponse::UNAVAILABLE_RATING, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_RATING))
        , json_decode($response->getContent(), true));
    }

    public function testDeleteCommentErrorNoComment()
    {
        $comment = Comment::destroy(1);
        $response = $this->action('delete', 'CommentController@destroy', array('rating_id' => 1, 'id' => 1));
        $this->assertEquals(array("code" => ApiResponse::UNAVAILABLE_COMMENT, "data" => ApiResponse::getErrorContent(ApiResponse::UNAVAILABLE_COMMENT))
         , json_decode($response->getContent(), true));
    }

}