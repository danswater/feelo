<?php if (count($this->boxes)): ?>
    <div id="div_boxes" style="display: none;" class="add2boxes">
        <h3><?php echo $this->translate("Now you are the user's follower.")?> </h3>
        <div class="add2boxes_img">
            
        </div> 
        <p>
            <?php echo $this->translate("You can add this user to your boxes")?> 
            or <a href="javascript:void(0)" class="gotoadnewbox" onclick="javascript:Smoothbox.close(); var cuid = $(this).get('cuid'); setTimeout(function() { Smoothbox.open('<?php echo ($this->baseurl()=='/'?'':$this->baseurl()); ?>/boxes/create/addnew/'+cuid) }, 500)" style="color:#5f93b4; font-weight:bold"> click here </a> to add box 
        </p>
        <table cellpadding="0" cellspacing="0" width="100%">
            <?php
                $ctr = 0;
                $start = 0;
                $end = count( $this->boxes );
            ?>

            <?php foreach ($this->boxes as $box) :?>

            <?php 
                if( $ctr == 0 )
                    echo '<tr>';
            ?>

            <td>
               <ul>
                    <li>
                        <a id="a_box_<?php echo $box->getIdentity() ?>" href="javascript:void(0);" onclick="javascript:wh_project_follow.toggle_box(this, <?php echo $box->getIdentity() ?>)"><?php echo $box->getTitle() ?></a>
                    </li>
                </ul>
            </td>

            <?php
                $ctr++;
                $start++;
                if( $ctr > 3 || $start >= $end )
                {
                    echo '</tr>';
                    $ctr = 0;
                }
            ?>

            <?php endforeach;?>
        </table>



        <?php /*
        <ul>
            <?php foreach ($this->boxes as $box) :?>
                <li>
                    <a id="a_box_<?php echo $box->getIdentity() ?>" href="javascript:void(0);" onclick="javascript:wh_project_follow.toggle_box(this, <?php echo $box->getIdentity() ?>)"><?php echo $box->getTitle() ?></a>
                </li>
            <?php endforeach;?>
        </ul>
        */ ?>

        <a href='javascript:void(0);' onclick='javascript:Smoothbox.close();' class="add2box_close"><?php echo $this->translate("Close")?></a>
    </div>
<?php endif; ?>