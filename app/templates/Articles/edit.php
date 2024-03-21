<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Article $article
 * @var string[]|\Cake\Collection\CollectionInterface $users
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Articles'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="articles form content">
            <?= $this->Form->create($article, ['data-article-id' => $article->id, 'id' => 'editArticleForm', 'type' => 'put']) ?>
            <fieldset>
                <legend><?= __('Edit Article') ?></legend>
                <?php
                    echo $this->Form->control('title');
                    echo $this->Form->control('body');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
            <script>
                $(document).ready(function() {
                    $('#editArticleForm').on('submit', function(e) {
                        e.preventDefault();

                        var formData = $(this).serialize();
                        var articleId = $(this).data('article-id');
                        $.ajax({
                            type: 'PUT',
                            url: '/articles/' + articleId + '.json',
                            data: formData,
                            success: function(response) {
                                alert('Article updated successfully');
                                console.log(response);
                            },
                            error: function(error) {
                                alert('An error occurred. Please try again.');
                                console.log(error);
                            }
                        });
                    });
                });
            </script>
        </div>
    </div>
</div>

