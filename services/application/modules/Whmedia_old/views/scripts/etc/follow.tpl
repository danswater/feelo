<div id="div_boxes" style="display: none;" class="add2boxes">
    <h3><?php echo $this->translate("Now you are the user's follower.") ?></h3>
    <div class="add2boxes_img">

    </div>
    <p><?php echo $this->translate("You can add this user to your boxes.") ?></p>
    <ul>
        <?php if (count($this->boxes)): ?>
            <?php foreach ($this->boxes as $box) : ?>
                <li>
                    <a id="a_box_<?php echo $box->getIdentity() ?>" href="javascript:void(0);" onclick="javascript:wh_project_follow.toggle_box(this, <?php echo $box->getIdentity() ?>)"><?php echo $box->getTitle() ?></a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        <li>
            <a id="a_box_new" href="javascript:void(0);" onclick="javascript:wh_project_follow.new_box()"><?php echo $this->translate('Create new Box') ?></a>
        </li>
    </ul>
    <a href='javascript:void(0);' onclick='javascript:Smoothbox.close();' class="add2box_close"><?php echo $this->translate("Close") ?></a>
</div>