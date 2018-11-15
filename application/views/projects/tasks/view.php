<div class="modal-body clearfix general-form ">
    <div class="p10 clearfix">
        <div class="media m0 bg-white">
          
            <div class="media-body w100p pt5">
                <p> 
                    <?php echo "<span class='label' style='background:$model_info->color; '>&nbsp;&nbsp;&nbsp;</span>"; ?>
                </p>
            </div>
        </div>
    </div>

    <div class="form-group clearfix">
        <div  class="col-md-12 mb15">
            <strong><?php echo $model_info->title; ?></strong>
        </div>

        <div class="col-md-12 mb15">
            <?php echo $model_info->description ? nl2br(link_it($model_info->description)) : "-"; ?>
        </div>

        <?php
        if (count($custom_fields_list)) {
            foreach ($custom_fields_list as $data) {
                if ($data->value) {
                    ?>
                    <div class="col-md-12 mb15">
                        <strong><?php echo $data->title . ": "; ?> </strong> <?php echo $this->load->view("custom_fields/output_" . $data->field_type, array("value" => $data->value), true); ?>
                    </div>
                    <?php
                }
            }
        }
        ?>

        <div class="col-md-12 mb15">
            <strong><?php echo lang('project') . ": "; ?> </strong> <?php echo anchor(get_uri("projects/view/" . $model_info->project_id), $model_info->project_title); ?>
        </div>

        <?php echo form_close(); ?>

    </div>

    <div class="row clearfix">
        <div class="col-md-12 b-t pt10 list-container">
            <?php if ($can_comment_on_tasks) { ?>
                <?php $this->load->view("projects/comments/comment_form"); ?>
            <?php } ?>
            <?php $this->load->view("projects/comments/comment_list"); ?>
        </div>
    </div>
<?php //Edited by GS ?>
    <?php if ($this->login_user->role_type === 'classadmin' || $this->login_user->is_admin){  ?>
    <?php //if ($this->login_user->user_type === "staff") { ?>
        <div class="box-title"><span ><?php echo lang("activity"); ?></span></div>
        <div class="pl15 pr15 mt15 list-container">
            <?php echo activity_logs_widget(array("limit" => 20, "offset" => 0, "log_type" => "task", "log_type_id" => $model_info->id)); ?>
        </div>
    <?php } ?>
</div>

<div class="modal-footer">
    <?php
    if ($can_edit_tasks) {
        echo modal_anchor(get_uri("projects/task_modal_form/"), "<i class='fa fa-pencil'></i> " . lang('edit_task'), array("class" => "btn btn-default", "data-post-id" => $model_info->id, "title" => lang('edit_task')));
    }
    ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>

<?php
$task_link = anchor(get_uri("projects/view/$model_info->project_id/tasks?task=" . $model_info->id), '<i class="fa fa-external-link"></i>', array("target" => "_blank", "class" => "p15"));
?>

<script type="text/javascript">
    $(document).ready(function () {
        //add a clickable link in task title.
        //$("#ajaxModalTitle").append('<?php echo $task_link ?>');

        //show the items in checklist
        $(".checklist-items").html(<?php echo $checklist_items; ?>);

        //show save & cancel button when the checklist-add-item-form is focused
        $("#checklist-add-item").focus(function () {
            $("#checklist-options-panel").removeClass("hide");
            $("#checklist-add-item-error").removeClass("hide");
        });

        $("#checklist-options-panel-close").click(function () {
            $("#checklist-options-panel").addClass("hide");
            $("#checklist-add-item-error").addClass("hide");
            $("#checklist-add-item").val("");
        });

        $("#checklist_form").appForm({
            isModal: false,
            onSuccess: function (response) {
                $("#checklist-add-item").val("");
                $("#checklist-add-item").focus();
                $(".checklist-items").append(response.data);
            }
        });

        $('body').on('click', '[data-act=update-checklist-item-status-checkbox]', function () {
            var status_checkbox = $(this).find("span");
            status_checkbox.addClass("inline-loader");
            $.ajax({
                url: '<?php echo_uri("projects/save_checklist_item_status") ?>/' + $(this).attr('data-id'),
                type: 'POST',
                dataType: 'json',
                data: {value: $(this).attr('data-value')},
                success: function (response) {
                    if (response.success) {
                        status_checkbox.closest("div").html(response.data);
                    }
                }
            });
        });
    });
</script>