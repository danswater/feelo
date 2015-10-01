<script language="JavaScript">
    function sendmail(email_form) {
        new Form.Request(email_form, 'grand_opening_email_div').send();
    }
</script>
<div id="grand_opening_email_div">
    <?php echo $this->form->render($this); ?>
</div>