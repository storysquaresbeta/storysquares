<style>.DTTT_container{display:none;}</style>
<div id="page-content" class="p20 clearfix">
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('projects'); ?></h1>
            <div class="title-button-group">
                <?php
                if ($can_create_projects) {
                    echo modal_anchor(get_uri("projects/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_project'), array("class" => "btn btn-default", "title" => lang('add_project')));
                }
                ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="project-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var optionVisibility = false;
        if ("<?php echo ($can_edit_projects || $can_delete_projects); ?>") {
            optionVisibility = true;
        }

        $("#project-table").appTable({
            source: '<?php echo_uri("projects/list_data") ?>',
            columns: [
                
                {title: '<?php echo lang("title") ?>'},
                {title: '<?php echo lang("client") ?>', "class": "w25p"}
                <?php echo $custom_field_headers; ?>,
                {visible: optionVisibility, title: '<i class="fa fa-bars"></i>', "class": "text-center option w100"}
            ],
            order: [[1, "desc"]]
        });
    });
</script>