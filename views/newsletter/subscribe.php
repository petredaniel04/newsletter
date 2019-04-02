<?php

use yii\widgets\NewsletterWidget;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

?>

<style>
    .newsletter-subscribe {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px;
        flex-direction: column;
    }

    .newsletter-subscribe form {
        background-color: #fff;
        padding: 15px;
        border: 1px solid #ccc;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        width: 340px;
        max-width: 340px;
    }

    .help-block {
        word-break: break-word;
    }

    .grid-view {
        overflow: auto;
        max-width: 100%;
    }


</style>

<section class="content">
    <div class="row">
        <div class="col-xs-12 col-xs-offset-0 col-md-4 col-md-offset-4 newsletter-content">
            <div class="newsletter-subscribe">
                <?php NewsletterWidget::begin(['content' => [
                    'form_id' => 'form-newsletter',
                    'input_name' => 'email_newsletter',
                    'input_id' => 'email-newsletter',
                    'input_type' => 'email',
                    'label_for' => 'email_newsletter',
                    'label_val' => 'E-mail address',
                    'title' => 'Subscribe to our newsletter',
                    'class_list' => 'btn btn-primary',
                    'action' => Url::toRoute('newsletter/save-newsletter'),
                ]]); ?>

                <?php NewsletterWidget::end(); ?>
            </div>
        </div>

        <div class="box-body col-xs-12">
            <div class="row">
                <div class="col-md-12"
                     style="text-align: right; margin-top: -5px; margin-bottom: -15px; height: 55px;">
                    <label class="control-label" for="page-size">Display on page</label>
                    <select style="height: 34px;" name="page_size" id="page-size"
                            onchange="setPageSize($(this).val());">
                        <?php $options = array(10, 50, 100, 150, 200, 250, 300, 350, 400, 450, 500, 550, 600, 650, 700, 750, 800, 850, 900, 950, 1000); ?>
                        <?php foreach ($options as $option) { ?>
                            <option
                                    value="<?php echo $option; ?>" <?php echo !empty($model->pageSize) && $model->pageSize == $option ? 'selected="selected"' : ''; ?>><?php echo $option; ?>
                                records
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" id="myTable">
                    <?php
                    echo GridView::widget([
                        'emptyCell' => '-',
                        'dataProvider' => $model->search(),
                        'columns' => [
                            [
                                'header' => 'Id',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return Html::encode($model->id);
                                },
                                'headerOptions' => [
                                    'style' => 'text-align: center; vertical-align: middle;'
                                ],
                                'contentOptions' => [
                                    'style' => 'text-align: center; vertical-align: middle;'
                                ]
                            ],
                            [
                                'header' => 'E-mail',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return Html::encode($model->email);
                                },
                                'headerOptions' => [
                                    'style' => 'text-align: center; vertical-align: middle;'
                                ],
                                'contentOptions' => [
                                    'style' => 'text-align: center; vertical-align: middle;'
                                ]
                            ],
                            [
                                'header' => 'Name',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return !empty($model->name) ? Html::encode($model->name) : '-';
                                },
                                'headerOptions' => [
                                    'style' => 'text-align: center; vertical-align: middle;'
                                ],
                                'contentOptions' => [
                                    'style' => 'text-align: center; vertical-align: middle;'
                                ]
                            ],
                            [
                                'header' => 'Added',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return date('Y-m-d', strtotime($model->added)) . '<br>' . date('H:i:s', strtotime($model->added));
                                },
                                'headerOptions' => [
                                    'style' => 'text-align: center; vertical-align: middle;'
                                ],
                                'contentOptions' => [
                                    'style' => 'text-align: center; vertical-align: middle;'
                                ]
                            ],
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function () {
        $('#form-newsletter').on('submit', function (e) {
            e.preventDefault();
            var form = $(this);
            var postData = $(this).serialize();
            $('button[type="submit"]').attr('disabled', true).addClass('disabled');
            $.ajax({
                type: "POST",
                url: form.attr("action"),
                dataType: 'json',
                data: postData,
                success: function (response) {
                    form.removeClass('has-error').addClass('has-success');
                    $('.help-block').html(response['message']);
                    $('button[type="submit"]').attr('disabled', false).removeClass('disabled');
                },
                error: function (XMLHttpRequest, textStatus, errorThrown, response) {
                    if (XMLHttpRequest['readyState'] > 0 || XMLHttpRequest['status'] > 0) {
                        form.addClass('has-error').removeClass('has-success');
                        $('.help-block').html(JSON.parse(XMLHttpRequest['responseText'])['message']);
                    } else {
                        alert('Internet connection problem');
                    }
                    $('button[type="submit"]').attr('disabled', false).removeClass('disabled');
                }
            });
        });
    });

    function setPageSize(pageSize) {
        var url = '<?php
            $route = ['newsletter/subscribe'];
            if (!empty($_GET)) {
                foreach ($_GET as $key => $value) {
                    if ($key == 'per-page') {
                        continue;
                    }
                    $route[$key] = $value;
                }
            }
            $route['per-page'] = '';
            echo Url::toRoute($route);
            ?>' + pageSize;
        window.location.href = url;
    }
</script>

