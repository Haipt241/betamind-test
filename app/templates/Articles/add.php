<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Article $article
 * @var \Cake\Collection\CollectionInterface|string[] $users
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
            <?= $this->Form->create($article, ['id' => 'addArticleForm', 'type' => 'post']) ?>
            <fieldset>
                <legend><?= __('Add Article') ?></legend>
                <?php
                    echo $this->Form->control('title');
                    echo $this->Form->control('body');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
            <script>
                $(document).ready(function() {
                    $('#addArticleForm').on('submit', function(e) {
                        e.preventDefault();

                        var formData = $(this).serialize();
                        $.ajax({
                            type: 'POST',
                            url: '/articles.json',
                            data: formData,
                            success: function(response) {
                                alert(response['message']);
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
