<?php $subjects = $this->db->get_where('subject', array('class_id' => $class_id))->result_array(); ?>
<select class="form-control selectboxit" name="subject_id">
    <?php foreach ($subjects as $subject): ?>
        <option value="<?php echo $subject['subject_id'] ?>"><?php echo $subject['name']; ?></option>
    <?php endforeach; ?>
</select>
<script type="text/javascript">

    // ajax form plugin calls at each modal loading,
    $(document).ready(function() {

        // SelectBoxIt Dropdown replacement
        if($.isFunction($.fn.selectBoxIt))
        {
            $("select.selectboxit").each(function(i, el)
            {
                var $this = $(el),
                    opts = {
                        showFirstOption: attrDefault($this, 'first-option', true),
                        'native': attrDefault($this, 'native', false),
                        defaultText: attrDefault($this, 'text', ''),
                    };

                $this.addClass('visible');
                $this.selectBoxIt(opts);
            });
        }
    });

</script>
