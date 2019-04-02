<?php
use yii\widgets\NewsletterWidget;
use yii\helpers\Url;
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
    }


</style>

<div class="newsletter-subscribe">

<?php NewsletterWidget::begin(['content' => [
    'input_name' => 'email_newsletter',
    'input_id' => 'email-newsletter',
    'input_type' => 'email',
    'label_for' => 'email_newsletter',
    'label_val' => 'E-mail address',
    'title' => 'Subscribe to our newsletter',
    'class_list' => 'btn btn-primary',
    'action' => Url::toRoute('newsletter/subscribe'),
]]); ?>
</div>
<?php NewsletterWidget::end(); ?>

<script>

</script>

