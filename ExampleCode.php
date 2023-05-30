&lt;?php

class Blog {

	public $id;
	public $userId;
	public $title;
	public $body;
	public $views;
	public $comments;
	public $voteCount;
	public $voteAverage;
	public $voteTotal;
	public $voteUserIds;
	public $created;
	public $updated;
	public $autoLoad;

	function __construct($id, $userId, $title, $body=NULL, $views=NULL, $comments=NULL, $voteCount=NULL, $voteAverage=NULL, $voteTotal=NULL, $voteUserIds=NULL, $created=NULL, $updated=NULL, $autoLoad=array()) {
		$this-&gt;id 			= $id;
		$this-&gt;userId		= $userId;
		$this-&gt;title 		= stripslashes($title);
		$this-&gt;body 		= stripslashes($body);
		$this-&gt;views		= $views;
		$this-&gt;comments		= $comments;
		$this-&gt;voteCount 	= $voteCount;
		$this-&gt;voteAverage	= $voteAverage;
		$this-&gt;voteTotal	= $voteTotal;
		$this-&gt;voteUserIds	= $voteUserIds;
		$this-&gt;created 		= $created;
		$this-&gt;updated		= $updated;
		$this-&gt;autoLoad		= $autoLoad;
		$this-&gt;AutoLoad($this-&gt;autoLoad);
	}


	public function AutoLoad($autoLoad) {

		if(is_array($autoLoad)) {
			foreach($autoLoad as $load):
				switch($load):
					case 'comments' :
						$this-&gt;LoadComments();
					break;
					case 'tags' :
						$this-&gt;LoadTags();
					break;
				endswitch;
			endforeach;
		} else if ($autoLoad === true) {
			$this-&gt;LoadComments();
			$this-&gt;LoadTags();
		}
	}


	public static function Load($id, $autoLoad=array()) {

		try {
			$db = Database::GetInstance();
			$result = $db-&gt;query("SELECT * FROM Blog WHERE id = ".$id);

				if($result &amp;&amp; $result-&gt;num_rows &gt; 0) {
					$q = $result-&gt;fetch_object();
						if($Object = new self($q-&gt;id, $q-&gt;user_id, $q-&gt;title, $q-&gt;body, $q-&gt;views, $q-&gt;comments, $q-&gt;vote_count, $q-&gt;vote_average, $q-&gt;vote_total, $q-&gt;vote_user_ids, $q-&gt;created, $q-&gt;updated, $autoLoad)) {
							return $Object;
						}
					} else {
						throw new ApplicationException('Could not load blogId '.$id);
					}

		} catch(DatabaseException $e) {
			throw new ApplicationException($e-&gt;getMessage(), $e-&gt;getCode());
		}
	}


	public function LoadComments() {
		$this-&gt;Comments = Comment_Blog::LoadByBlogId($this-&gt;id);
	}


	public function LoadTags() {
		$this-&gt;Tags = Tag_Blog::LoadTagsByBlogId($this-&gt;id);
	}



	public function AddView() {

		try {
			$db = Database::GetInstance();
			$result = $db-&gt;query("UPDATE Blog SET views = views + 1 WHERE id = ".$this-&gt;id);

				if(!$result) {
					return false;
				} else if ($db-&gt;affected_rows &gt;= 0) {
					return true;
				} else {
					return false;

				}

		} catch(DatabaseException $e) {
			throw new ApplicationException($e-&gt;getMessage(), $e-&gt;getCode());
		}		
	}


	public function HasVoted($userId) {
		$items = explode(',', $this-&gt;voteUserIds);
		return in_array($userId, $items) ? true : false;
	}

	public function TagsToString() {

			if(!empty($this-&gt;Tags)):
				$result = NULL;
					foreach($this-&gt;Tags as $tag):
						$result .= ', '.$tag-&gt;title;
					endforeach;
				return substr($result, 2);
			endif;
		return false;
	}


	public function Vote($rating) {
	global $User;

		try {
			$db = Database::GetInstance();

			if($User-&gt;id == $this-&gt;userId) {//my blog, cant vote
				return false;
			} else if ($this-&gt;HasVoted($User-&gt;id) == true) {//already voted
				return false;
			}

			$this-&gt;voteUserIds = $this-&gt;voteUserIds.",".$User-&gt;id;

			if($this-&gt;voteCount &gt; 1) {
				$this-&gt;voteTotal 	= $this-&gt;voteTotal + $rating;
				$this-&gt;voteCount 	= $this-&gt;voteCount + 1;
				$this-&gt;voteAverage 	=  $this-&gt;voteTotal / $this-&gt;voteCount + 1;
			} else {
				$this-&gt;voteAverage = $rating;
			}

			$result = $db-&gt;query("UPDATE Blog SET vote_count = vote_count + 1, vote_average = '".$this-&gt;voteAverage."', vote_user_ids = '".$this-&gt;voteUserIds."', vote_total = vote_total + $rating WHERE id = ".$this-&gt;id);

				if(!$result) {
					return false;
				} else if ($db-&gt;affected_rows &gt;= 0) {
					return true;
				} else {
					return false;

				}

		} catch(DatabaseException $e) {
			throw new ApplicationException($e-&gt;getMessage(), $e-&gt;getCode());
		}		
	}


	public function AddComment($blogId) {

		try {
			$db = Database::GetInstance();
			$result = $db-&gt;query("UPDATE Blog SET comments = comments + 1 WHERE id = ".$blogId);

				if(!$result) {
					return false;
				} else if ($db-&gt;affected_rows &gt;= 0) {
					return true;
				} else {
					return false;

				}

		} catch(DatabaseException $e) {
			throw new ApplicationException($e-&gt;getMessage(), $e-&gt;getCode());
		}		
	}


	public function RemoveComment($blogId) {

		try {
			$db = Database::GetInstance();
			$result = $db-&gt;query("UPDATE Blog SET comments = comments - 1 WHERE id = ".$blogId);

				if(!$result) {
					return false;
				} else if ($db-&gt;affected_rows &gt;= 0) {
					return true;
				} else {
					return false;

				}

		} catch(DatabaseException $e) {
			throw new ApplicationException($e-&gt;getMessage(), $e-&gt;getCode());
		}		
	}


	public function Save() {

		try {
			$db = Database::GetInstance();
			$result = $db-&gt;query("UPDATE Blog SET title = ".$db-&gt;safe($this-&gt;title).", body = ".$db-&gt;safe($this-&gt;body).", updated = ".time()." WHERE id = ".$this-&gt;id);

				if(!$result) {
					return false;
				} else if ($db-&gt;affected_rows &gt;= 0) {
					return true;
				} else {
					return false;
				}

		} catch(DatabaseException $e) {
			throw new ApplicationException($e-&gt;getMessage(), $e-&gt;getCode());
		}		
	}


	public static function LoadAllByType($from, $to, $type=NULL, $extra=NULL, $count=0, $autoLoad=array()) {
	global $User;

		try {
			$db = Database::GetInstance();

			if($type == 'by_user') {
				$arg = " AND b.user_id = $extra ";
				$join = NULL;
			} else if ($type == 'by_tag') {
				$arg = ' AND t.title = '.$db-&gt;safe($extra);
				$join = "LEFT JOIN Tag_Blog tb ON (tb.blog_id = b.id) LEFT JOIN Tag t ON (tb.tag_id = t.id) ";
			} else if ($type == 'by_friends') {
				$arg = " AND b.user_id IN($extra) ";
				$join = NULL;
			} else {
				$arg = NULL;
				$join = NULL;
			}

			$sql = "SELECT b.*, u.username as username, ud.id as ud_id, ud.key, ud.value 
			FROM Blog b 
			JOIN User u ON (b.user_id = u.id) 
			LEFT OUTER JOIN User_Data ud ON (u.id = ud.user_id AND ud.key = 'Avatar') 
			$join 
			WHERE b.id &gt; 0 
			$arg 
			ORDER BY b.created DESC
			LIMIT $from, $to";
			$query = $db-&gt;query($sql) or die($db-&gt;error." $sql");

				if($count == 1) {
					return $query-&gt;num_rows;
				}

				if($query &amp;&amp; $query-&gt;num_rows &gt; 0):
					$blogs = array();
						while ($q = $query-&gt;fetch_object()):
							$Object = new self($q-&gt;id, $q-&gt;user_id, $q-&gt;title, $q-&gt;body, $q-&gt;views, $q-&gt;comments, $q-&gt;vote_count, $q-&gt;vote_average, $q-&gt;vote_total, $q-&gt;vote_user_ids, $q-&gt;created, $q-&gt;updated, $autoLoad);
							$Object-&gt;User = new User($q-&gt;user_id, $q-&gt;username);
							if(!empty($q-&gt;key)):
								$Object-&gt;User-&gt;Avatar = new User_Data($q-&gt;ud_id, $q-&gt;user_id, $q-&gt;key, $q-&gt;value);
							endif;
							$blogs[] = $Object;
						endwhile;
					return $blogs;
				endif;
			return false;

		} catch(DatabaseException $e) {
			throw new ApplicationException($e-&gt;getMessage(), $e-&gt;getCode());
		}		
	}


	public static function Create($userId, $title, $body) {

		try {
			$db = Database::GetInstance();
			$time = time();
			$db-&gt;query("INSERT INTO Blog (user_id, title, body, created, updated) VALUES ($userId, ".$db-&gt;safe($title).", ".$db-&gt;safe($body).", $time, $time) ");
			$id = $db-&gt;insert_id;

				if($db-&gt;affected_rows &gt; 0) {
					return self::Load($id);
				} else { 
					return false;
				}

		} catch(DatabaseException $e) {
			throw new ApplicationException($e-&gt;getMessage(), $e-&gt;getCode());
		}		
	}


	public function Delete() {

		try {
			Comment_Blog::DeleteByBlogId($this-&gt;id);
			$db = Database::GetInstance();
			return $db-&gt;query("DELETE FROM Blog WHERE id = ".$this-&gt;id);

		} catch(DatabaseException $e) {
			throw new ApplicationException($e-&gt;getMessage(), $e-&gt;getCode());
		}		
	}


}

?&gt;
