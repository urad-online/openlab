<p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title: (defaults to "Link to OpenLab Gradebook")'); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_id('message'); ?>"><?php _e('Included Message (optional)'); ?></label> 
    <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('message'); ?>" name="<?php echo $this->get_field_name('message'); ?>"><?php echo $message; ?></textarea>
</p>