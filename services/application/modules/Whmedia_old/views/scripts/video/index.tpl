<?php if ($this->show_video_slider) {
        $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/slider.js');
        $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/whmedia_core.js');
        $url_world = WHMEDIA_URL_WORLD;
        $script = "   var video_cover = new whmedia.cover_media({video_id : {$this->video->getIdentity()},
                                                                 module: '{$url_world}'   });
                            en4.core.runonce.add(function() {
                                                            var isInitSlider_c = 0;
                                                            var isInitSlider_ch = 0;
                                                            var slider_current_val = window.$('slider_current_val');
                                                            var selected_hour = window.$('selected_hour');
                                                            var selected_minute = window.$('selected_minute');
                                                            var selected_second = window.$('selected_second');
                                                            var slider_gutter_1= window.$('slider_gutter_1');
                                                            var mySlideB = new Slider(slider_gutter_1, $('slider_knob_1'), $('slider_bkg_img_1'),{
                                                                                                                                                        start: 0,
                                                                                                                                                        end: {$this->video_duration},
                                                                                                                                                        offset: 10,
                                                                                                                                                        onChange: function(pos){
                                                                                                                                                            if (isInitSlider_ch == 0) {
                                                                                                                                                                isInitSlider_ch = 1;
                                                                                                                                                            }
                                                                                                                                                            else {
                                                                                                                                                                var h = pos/3600|0;
                                                                                                                                                                var m = (pos - h*3600)/60|0;
                                                                                                                                                                var s = (pos - h*3600 - m*60);
                                                                                                                                                                slider_current_val.setStyle('display', 'block');
                                                                                                                                                                if (selected_hour.get('disabled') != true)
                                                                                                                                                                    selected_hour.set('value', timeFormat(h));
                                                                                                                                                                if (selected_minute.get('disabled') != true)
                                                                                                                                                                    selected_minute.set('value', timeFormat(m));
                                                                                                                                                                selected_second.set('value', timeFormat(s))
                                                                                                                                                            }

                                                                                                                                                        },
                                                                                                                                                        onComplete: function(pos){
                                                                                                                                                            if (isInitSlider_c == 0) {
                                                                                                                                                                isInitSlider_c = 1;
                                                                                                                                                            }
                                                                                                                                                            else {
                                                                                                                                                                video_cover.get_frame(pos);
                                                                                                                                                                
                                                                                                                                                            }

                                                                                                                                                        }
                                                                                                                                                }, null).setMin(0);
                                                                video_cover.slider = mySlideB;
                                                                slider_gutter_1.addEvent('click', function(event){video_cover.slider.clickedElement(event);});
                                                                                                                                                
                         });";
        $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
    }
?>
<div id="all_covers">
    <?php if ($this->show_video_slider): ?>
        <div class="slider_outer" >
            <h3><?php echo $this->translate("Set thumbnail from video still"); ?></h3>
            <div id="slider_current_val" style="display: none;">
                <?php echo $this->translate("Selected time: "); ?>
                <input type="text" size="2" maxlength="3" <?php if ($this->video_duration < 3600): ?>disabled value="00" <?php else:?> onkeypress="javascript:window.$('button_get_frame').setStyle('display', 'block');" <?php endif; ?> id="selected_hour" />:
                <input type="text" size="1" maxlength="2" <?php if ($this->video_duration < 60): ?>disabled value="00" <?php else:?> onkeypress="javascript:window.$('button_get_frame').setStyle('display', 'block');" <?php endif; ?> id="selected_minute" />:
                <input type="text"  size="2" maxlength="2" onkeypress="javascript:window.$('button_get_frame').setStyle('display', 'inline');" id="selected_second" />
                <?php echo $this->translate("hh:mm:ss"); ?>
                <a class="button" href="javascript:void(0)" id="button_get_frame" style="display: none;" onclick="javascript:video_cover.get_time_frame();"><?php echo $this->translate("Get Cover"); ?></a>
            </div>
            <div class="slide_container" id="slide_container_block">
                <div class="slider_gutter" id="slider_1" >
                    <div id="slider_gutter_1" class="slider_gutter_m slider_gutter_item gutter iconsprite_controls">
                     <div class="start_time">00:00</div>
                     <span class="finish_time"><?php echo $this->timeDuration($this->video_duration) ?></span>
                        <img id="slider_bkg_img_1" src="" alt="" />
                        <div id="slider_knob_1" class="knob" onclick="javascript:ignoreDrag(event);"></div>
                    </div>
                </div>
                
                <div class="clearfix"></div>
            </div>

            <div id="frame_img">
                <div class="tip"><span><?php echo $this->translate("Use sliding bar to choose video still."); ?></span></div>
            </div>
            <button type="submit" id="set_cover_slider" name="submit"><?php echo $this->translate("Set a Cover"); ?></button>
            <span> or </span>
            <a onclick="javascript:parent.Smoothbox.close();" href="javascript:void(0);" ><?php echo $this->translate("Cancel"); ?></a>

       </div>

    <?php endif;?>
    <div class="upload_new_video">
        <?php echo $this->form_cover ?>
    </div>
    
</div>