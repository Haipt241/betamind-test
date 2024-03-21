<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\View\JsonView;

/**
 * Articles Controller
 *
 * @property \App\Model\Table\ArticlesTable $Articles
 * @method \App\Model\Entity\Article[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ArticlesController extends AppController
{
    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Likes');
    }

    /**
     * @param \Cake\Event\EventInterface $event
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Configure the login action to not require authentication, preventing
        // the infinite redirect loop issue
        $this->Authentication->addUnauthenticatedActions(['delete']);
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->Authorization->skipAuthorization();
        $this->loadComponent('Paginator');

        $user = $this->Authentication->getIdentity();
        $userId = $user ? $user->getIdentifier() : null;

        $articles = $this->Articles->find()
            ->contain(['Users', 'Likes']);

        $articles = $this->paginate($articles)->map(function ($article) use ($userId) {
            $article->likes_count = count($article->likes);
            $article->liked_by_user = $userId && in_array($userId, collection($article->likes)->extract('user_id')->toList());
            $article->not_logged_in = !$userId;
            unset($article->likes);
            return $article;
        });


        $this->set(compact('articles'));
        $this->viewBuilder()->setOption('serialize', ['articles']);
    }

    /**
     * View method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->Authorization->skipAuthorization();
        $article = $this->Articles->get($id, [
            'contain' => ['Users', 'Likes'],
        ]);
        $article->likes_count = count($article->likes);
        $this->set('article', $article);
        $this->viewBuilder()->setOption('serialize', ['article']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $article = $this->Articles->newEmptyEntity();
        $this->Authorization->authorize($article);
        $response = ['message' => '', 'article' => null];

        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());
            $userId = $this->Authentication->getIdentity()->getIdentifier();
            $article->user_id = $userId;
            if ($this->Articles->save($article)) {
                $response['message'] = __('The article has been saved.');
                $response['article'] = $article;
            } else {
                $response['message'] = __('The article could not be saved. Please, try again.');
                $this->response = $this->response->withStatus(400);
            }

        }
        // Set the view vars that have to be serialized.
        $this->set($response);

        // Specify which view vars JsonView should serialize.
        $this->viewBuilder()->setOption('serialize', array_keys($response));
    }

    /**
     * Edit method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $article = $this->Articles->get($id, [
            'contain' => [],
        ]);
        $this->Authorization->authorize($article);
        $response = ['message' => '', 'article' => null];
        if ($this->request->is(['put'])) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());
            if ($this->Articles->save($article)) {
                $response['message'] = __('The article has been saved.');
                $response['article'] = $article;
            } else {
                $response['message'] = __('The article could not be saved. Please, try again.');
                $this->response = $this->response->withStatus(400);
            }
        }
        // Set the view vars that have to be serialized.
        $this->set($response);

        // Specify which view vars JsonView should serialize.
        $this->viewBuilder()->setOption('serialize', array_keys($response));
        $users = $this->Articles->Users->find('list', ['limit' => 200])->all();
        $this->set(compact('article', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $user = $this->Authentication->getIdentity();
        if (!$user) {
            $this->Authorization->skipAuthorization();
            $response = ['message' => __('You must be logged in to perform this action.')];
            $this->response = $this->response->withStatus(401);
        } else {
            $article = $this->Articles->get($id);
            $this->Authorization->authorize($article);
            $response = ['message' => '', 'article' => null];
            if ($this->Articles->delete($article)) {
                $response['message'] = __('The article has been deleted.');
                $response['article'] = $article;
            } else {
                $response['message'] = __('The article could not be deleted. Please, try again.');
                $this->response = $this->response->withStatus(400);
            }

        }

        // Set the view vars that have to be serialized.
        $this->set($response);

        // Specify which view vars JsonView should serialize.
        $this->viewBuilder()->setOption('serialize', array_keys($response));
    }

    /**
     * @param $articleId
     * @return \Cake\Http\Response|null
     */
    public function like($articleId)
    {
        $this->request->allowMethod(['post']);
        $userId = $this->Authentication->getIdentity()->getIdentifier();
        $this->Authorization->skipAuthorization();
        $existingLike = $this->Likes->find()
            ->where(['article_id' => $articleId, 'user_id' => $userId])
            ->first();
        $response = ['message' => '', 'article' => null];
        if ($existingLike) {
            $response['message'] = __('You have already liked this article.');
            $this->response = $this->response->withStatus(400);
        } else {
            $like = $this->Likes->newEntity(['article_id' => $articleId, 'user_id' => $userId]);
            if ($this->Likes->save($like)) {
                $response['message'] = __('Article liked successfully.');
                $this->response = $this->response->withStatus(200);
            } else {
                $response['message'] = __('Could not like the article. Please, try again.');
                $this->response = $this->response->withStatus(400);
            }
        }
        // Set the view vars that have to be serialized.
        $this->set($response);

        // Specify which view vars JsonView should serialize.
        $this->viewBuilder()->setOption('serialize', array_keys($response));
    }
}
