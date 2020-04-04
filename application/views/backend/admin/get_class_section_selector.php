<?php $sections = $this->db->get_where('section', array('class_id' => $class_id))->result_array(); ?>
<select class="form-control selectboxit" name="section_id">
    <?php foreach ($sections as $section): ?>
        <option value="<?php echo $section['section_id'] ?>"><?php echo $section['name']; ?></option>
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
