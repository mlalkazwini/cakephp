<?php

class PostsController extends AppController {

	// public function beforeScaffold($index) {
	// 	echo $this->element('return');
	// }
	
	public $helpers = array('Html', 'Form');
	public $components = array('Paginator');

	public $paginate = array(
		'limit' => 3,
		'maxLimit' => 3,
		'order' => array(
			'Post.created' => 'DESC'
		)
	);

	public function index() {
		$this->Paginator->settings = $this->paginate;

		$posts = $this->Paginator->paginate('Post');

	    $this->set('posts', $posts);
	}

/**
 * view method
 * 
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		
		if (!$id) {
			throw new NotFoundException(__('Invalid post'));
		}
		$test = $this->Post->find('first', array('conditions' => array('Post.id' => $id), 'recursive' => -1));
		debug($test);
		$post = $this->Post->find('first', array(
			'conditions' => array(
				'Post.id' => $id
			),
			'contain' => array(
				'TaggedPost' => array('Tag'),
				'Comment'
			)
		));
		//debug($post); die;

		if (!$post) {
			throw new NotFoundException(__('Invalid post'));
		}
		$this->set(compact('post'));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$tags = $this->Post->TaggedPost->Tag->find('list', array(
			'fields' => array('tag_name')
		));
		$this->set(compact('tags'));
		if ($this->request->is('post')) {
			$this->Post->create();
			if ($this->Post->save($this->request->data)) {
				foreach ($this->request->data['Tag'] as $tag) {
					$tagData = array(
						'post_id' => $this->Post->id,
						'tag_id' => $tag['tag_id']
					);
					$this->Post->TaggedPost->create();
					$this->Post->TaggedPost->save($tagData);
				}
				$this->Session->setFlash(__('Your post has been saved.'));
				return $this->redirect(array('action' => 'index'));
			}
			$this->Session-setFlash(__('Unable to add your post.'));
		}
	}

/**
 * edit method
 * 
 * @param  integer $id
 * @return void
 */
	public function edit($id = null) {
		if (!$id) {
			throw new NotFoundException(__('Invalid post'));
		}

		$post = $this->Post->findById($id);
		
		if (!$post) {
			throw new NotFoundException(__('Invalid post'));
		}

		$this->Post->id = $id;
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Post->save($this->request->data)) {
				$this->Session->setFlash(__('Your post has been updated.'));
				return $this->redirect(array('action' => 'index'));
			}
			$this->Session->setFlash(__('Unable to update your post.'));
		}

		if (!$this->request->data) {
			$this->request->data = $post;
		}
	}


/**
 * delete method
 * 
 * @param  integer $id
 * @return void
 */
	public function delete($id){
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}

		if ($this->Post->delete($id)) {
			$this->Session->setFlash(__('The post with id: %s has been deleted.', h($id)));
			return $this->redirect(array('action' => 'index'));
		}
	}
}
?>