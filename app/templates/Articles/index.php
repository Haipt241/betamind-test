<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Article> $articles
 */
?>
<div class="articles index content">
    <?= $this->Html->link(__('New Article'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Articles') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('title') ?></th>
                    <th><?= $this->Paginator->sort('body') ?></th>
                    <th><?= $this->Paginator->sort('likes') ?></th>
                    <th><?= $this->Paginator->sort('created_at') ?></th>
                    <th><?= $this->Paginator->sort('updated_at') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                <tr>
                    <td><?= $this->Number->format($article->id) ?></td>
                    <td><?= $article->has('user') ? $this->Html->link($article->user->email, ['controller' => 'Users', 'action' => 'view', $article->user->id]) : '' ?></td>
                    <td><?= h($article->title) ?></td>
                    <td><?= h($article->body) ?></td>
                    <td><?= h($article->likes_count) ?></td>
                    <td><?= h($article->created_at) ?></td>
                    <td><?= h($article->updated_at) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $article->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $article->id]) ?>
                        <?= $this->Html->link(__('Delete'), '#', [
                            'class' => 'delete-article',
                            'data-article-id' => $article->id,
                            'data-confirm-message' => __('Are you sure you want to delete # {0}?', $article->id)
                        ]) ?>
                        <?php if (!$article->not_logged_in): // Nếu người dùng đã đăng nhập ?>
                            <?php if (!$article->liked_by_user): ?>
                                <?= $this->Html->link(__('Like'), '#', [
                                    'class' => 'like-article',
                                    'data-article-id' => $article->id,
                                ]) ?>
                            <?php else: ?>
                                <?= __('Liked') ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <script>
                    $(document).ready(function() {
                        $('.delete-article').on('click', function(event) {
                            event.preventDefault();
                            var articleId = $(this).data('article-id');
                            var confirmMessage = $(this).data('confirm-message');
                            if (!confirm(confirmMessage)) {
                                return;
                            }
                            $.ajax({
                                url: '/articles/' + articleId + '.json',
                                method: 'DELETE',
                                success: function(response) {
                                    alert(response['message']);
                                    console.log(response);
                                    location.reload();
                                },
                                error: function(xhr) {
                                    if (xhr.status === 401) {
                                        window.location.href = "/users/login";
                                    } else {
                                        alert("Error: " + xhr.responseJSON.message);
                                    }
                                }
                            });
                        });

                        $('.like-article').click(function(e) {
                            e.preventDefault();
                            var articleId = $(this).data('article-id');
                            $.ajax({
                                method: 'POST',
                                url: '/articles/like/' + articleId + '.json',
                                success: function(response) {
                                    alert(response['message']);
                                    location.reload();
                                },
                                error: function() {
                                    alert('Error liking the article');
                                }
                            });
                        });
                    });
                </script>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
