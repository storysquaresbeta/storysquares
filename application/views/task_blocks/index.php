<div class="panel">
    <div class="tab-title clearfix">
        <h4> <?php echo lang('task_blocks'); ?></h4>
        <div class="title-button-group">
            <?php
            if ($can_create_task_blocks) {
                echo modal_anchor(get_uri("task_blocks/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_task_blocks'), array("class" => "btn btn-default", "title" => lang('add_task_blocks'), "data-post-project_id" => $project_id)); 
            }
            ?>
        </div>
    </div>
    <div class="table-responsive">
        <table id="task-blocks-table"  class="display no-thead b-t b-b-only no-hover" cellspacing="0" width="100%">            
        </table>
    </div>    
</div>


<script type="text/javascript">
    $(document).ready(function () {
        var optionVisibility = false;
        if ("<?php echo ($can_edit_task_blocks || $can_delete_task_blocks); ?>") {
            optionVisibility = true;
        }
        
        $("#task-blocks-table").appTable({
            source: '<?php echo_uri("task_blocks/list_data/".$project_id) ?>',
            order: [[0, "asc"]],
            hideTools: true,
            displayLength: 100,
            columns: [
                {visible: false},
                {title: '<?php echo lang("title"); ?>'},
                {visible: optionVisibility, title: '<i class="fa fa-bars"></i>', "class": "text-center option w100"}
            ],
            onInitComplete: function () {
                //apply sortable
                $("#task-blocks-table").find("tbody").attr("id", "custom-field-table-sortable");
                var $selector = $("#custom-field-table-sortable");

                Sortable.create($selector[0], {
                    animation: 150,
                    chosenClass: "sortable-chosen",
                    ghostClass: "sortable-ghost",
                    onUpdate: function (e) {
                        appLoader.show();
                        //prepare sort indexes 
                        var data = "";
                        $.each($selector.find(".field-row"), function (index, ele) {
                            if (data) {
                                data += ",";
                            }

                            data += $(ele).attr("data-id") + "-" + index;
                        });

                        //update sort indexes
                        $.ajax({
                            url: '<?php echo_uri("task_blocks/update_field_sort_values") ?>',
                            type: "POST",
                            data: {sort_values: data},
                            success: function () {
                                appLoader.hide();
                            }
                        });
                    }
                });

            }

        });
    });
</script>