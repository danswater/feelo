<?php /*
<div id="whmedia_m_<?php echo $this->identity ?>" class="whmedia_m">
    <div class="related-projects">
        <?php foreach($this->favo_projects as $favo_project): ?>
            <div class="whmedia_item">
                <a href="<?php echo $this->url(array('controller' => 'favboxes', 'action' => 'menprojectlist', 'favcircle_id' => $favo_project["favcircle_id"]), 'default', true) ?>">
                    <div>
                        
                        <img src="<?php echo $this->baseUrl(); ?>/whshow_thumb.php?src=<?php echo $favo_project["photos"]["thumb"]; ?>&amp;cz=1&amp;w=120&amp;h=120" alt="Project Thumb">
                    </div>
                </a>               
                 <a href="<?php echo $this->url(array('controller' => 'favboxes', 'action' => 'menprojectlist', 'favcircle_id' => $favo_project["favcircle_id"]), 'default', true) ?>">
                    <?php echo $favo_project["title"]; ?>
                </a>      
            </div>   
        <?php endforeach; ?>
    </div>
</div>
*/ ?>

<div id="whmedia_m_<?php echo $this->identity ?>" class="whmedia_m">
    <div class="related-projects">
        <?php foreach($this->favo_projects as $favo_project): ?>

            <?php 
                $url = $this->url(array('controller' => 'login-pop-up'), 'default', true);
                $smoothbox = "smoothbox";

                if ($this->viewer()->getIdentity()):
                    
                    $url = $this->url(array('controller' => 'favboxes', 'action' => 'menprojectlist', 'favcircle_id' => $favo_project["favcircle_id"]), 'default', true);
                    $smoothbox = "";
                
                endif;
            ?>  


            <div class="whmedia_item">
                <a href="<?php echo  $url; ?>" class="<?php echo $smoothbox; ?>" >
                    <div>
                        
                        <img src="<?php echo $this->baseUrl(); ?>/whshow_thumb.php?src=<?php echo $favo_project["photos"]["thumb"]; ?>&amp;cz=1&amp;w=120&amp;h=120" alt="Project Thumb">
                    </div>
                </a>               
                 <a href="<?php echo  $url; ?>" class="<?php echo $smoothbox; ?>" >
                    <?php echo $favo_project["title"]; ?>
                </a>      
            </div>   
        <?php endforeach; ?>
    </div>
</div>