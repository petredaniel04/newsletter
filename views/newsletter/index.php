<?php
/**
 * Created by PhpStorm.
 * User: Daniel Petre
 * Date: 30/03/2019
 * Time: 17:17
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<style>
    body {
        background: #ecf0f5;
    }

    .summary {
        text-align: right;
    }

    .help-block {
        word-break: break-all;
    }

    .newsletter-content {
        border: 1px solid #ccc;
        padding: 15px;
        margin-bottom: 30px;
        background: #fff;
    }

    .newsletter-content button {
        float: right;
    }

    .grid-view {
        overflow: auto;
        max-width: 100%;
    }

</style>

<section class="content-header text-center">
    <h1><?php echo $title; ?></h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-xs-12 col-xs-offset-0 col-md-4 col-md-offset-4 newsletter-content">
            <?php $form = ActiveForm::begin([
                'id' => 'newsletter-form',
                'action' => Url::toRoute(['newsletter/save']),
                'validationUrl' => Url::toRoute(['newsletter/validate']),
                'enableAjaxValidation' => true,
                'validateOnChange' => true,
                'validateOnBlur' => false,
            ]); ?>

            <?php echo $form->field($model, 'email')->textInput(); ?>
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']); ?>
            <?php $form->end(); ?>
        </div>

        <div class="box-body col-xs-12">
            <div class="row">
                <div class="col-md-12" style="text-align: right; margin-top: -5px; margin-bottom: -15px; height: 55px;">
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
        $('#newsletter-form').on('beforeSubmit', function (e) {
            var form = $(this);
            var formData = $(this).serialize();

            $.ajax({
                url: form.attr("action"),
                type: form.attr("method"),
                data: formData,
                beforeSend: function () {
                    form.find('button').addClass('disabled').attr('disabled', true);
                    $('#newsletter-email').attr('readonly', true);
                },
                success: function (data) {
                    if (data['success'] === true) {
                        $('.help-block').html('You subscribed to the newsletter successfully');
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    }
                },
                error: function () {
                    alert("Something went wrong");
                }
            });

        }).on('submit', function (e) {
            e.preventDefault();
        });
    });

    function setPageSize(pageSize) {
        var url = '<?php
            $route = ['newsletter/index'];
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
