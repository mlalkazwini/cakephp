<?php
	class Comment extends AppModel {
		public $belongsTo = array('Post', 'Author');

		public $validate = array(
			'content' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => 'no comment has been entered'
			),
			'post_id' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => 'Please choose a post'
			)
		);
	}
?>