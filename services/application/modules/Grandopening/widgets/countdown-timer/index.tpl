<script language="JavaScript">
    var server_time = <?php echo time() ?> *1000;
    var cur_tmp_time = new Date();
    var time_difference = server_time - cur_tmp_time.getTime();
    var dest_time = <?php echo $this->endtime * 1000 ?> - time_difference;
</script>
<script language="JavaScript" src="<?php echo $this->baseUrl().'/application/modules/Grandopening/externals/scripts/go.js' ?>" type="text/JavaScript"></script>
<script language="JavaScript">
window.addEvent('domready', function() {
    startclock();
});
</script>
<?php echo $this->oldPathInfo;?>
<div>
    <table class="go_clock" border=0 cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <img name="days_1" src="<?php echo $this->baseUrl().'/application/modules/Grandopening/externals/images/counter/00.png' ?>"><img name="days_2" src="<?php echo $this->baseUrl().'/application/modules/Grandopening/externals/images/counter/00.png' ?>"><img name="days_3" src="<?php echo $this->baseUrl().'/application/modules/Grandopening/externals/images/counter/00.png' ?>">
            </td>
            <td align="center">
                <img src="<?php echo $this->baseUrl().'/application/modules/Grandopening/externals/images/counter/colon.png' ?>" />
            </td>
            <td align="center">
                <img name="hours_1" src="<?php echo $this->baseUrl().'/application/modules/Grandopening/externals/images/counter/00.png' ?>"><img name="hours_2" src="<?php echo $this->baseUrl().'/application/modules/Grandopening/externals/images/counter/00.png' ?>">
            </td>
            <td align="center">
                <img src="<?php echo $this->baseUrl().'/application/modules/Grandopening/externals/images/counter/colon.png' ?>" />
            </td>
            <td align="center">
                <img name="minutes_1" src="<?php echo $this->baseUrl().'/application/modules/Grandopening/externals/images/counter/00.png' ?>"><img name="minutes_2" src="<?php echo $this->baseUrl().'/application/modules/Grandopening/externals/images/counter/00.png' ?>">
            </td>
            <td align="center">
                <img src="<?php echo $this->baseUrl().'/application/modules/Grandopening/externals/images/counter/colon.png' ?>" />
            </td>
            <td>
                <img name="seconds_1" src="<?php echo $this->baseUrl().'/application/modules/Grandopening/externals/images/counter/00.png' ?>"><img name="seconds_2" src="<?php echo $this->baseUrl().'/application/modules/Grandopening/externals/images/counter/00.png' ?>">
            </td>
        </tr>
        <tr>
            <td align="center">
                <?php echo $this->translate('Days');?>
            </td>
            <td>
            <td align="center">
                <?php echo $this->translate('Hours');?>
            </td>
            <td>
            <td align="center">
                <?php echo $this->translate('Minutes');?>
            </td>
            <td>
            <td align="center">
                <?php echo $this->translate('Seconds');?>
            </td>
        </tr>

    </table>
</div>