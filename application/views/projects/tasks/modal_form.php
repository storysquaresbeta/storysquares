<?php echo form_open(get_uri("projects/save_task"), array("id" => "task-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <input type="hidden" name="id" id="task_id" value="<?php echo $model_info->id; ?>" />
    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
    <div class="form-group">
        <label for="title" class=" col-md-2"><?php echo lang('square_title'); ?></label>
        <div class=" col-md-10">
            <?php
            echo form_input(array(
                "id" => "title",
                "name" => "title",
                "value" => $model_info->title,
                "class" => "form-control",
                "placeholder" => lang('square_title'),
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
            ));
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="description" class=" col-md-2"><?php echo lang('square_text'); ?></label>
        <div class=" col-md-10">
            <?php
            echo form_textarea(array(
                "id" => "description",
                "name" => "description",
                "value" => $model_info->description,
                "class" => "form-control resizable",
                "placeholder" => lang('square_text')
            ));
            ?>
        </div>
    </div>
    <?php if (!$project_id) { ?>
        <div class="form-group">
            <label for="project_id" class=" col-md-2"><?php echo lang('project'); ?></label>
            <div class="col-md-10">
                <?php
                echo form_dropdown("project_id", $projects_dropdown, array(), "class='select2 validate-hidden' id='project_id' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                ?>
            </div>
        </div>
    <?php } ?>

   
    <?php //Added by GS ?>
    <?php if($model_info->task_block_id){
        ?>
    <input type="hidden" name="task_block_id" value="<?php echo $model_info->task_block_id; ?>" /><?php }
    ?>
    <?php if($model_info->color){
        ?>
    
    <input type="hidden" name="color" value="<?php echo $model_info->color; ?>" />
    <?php
    }else{ ?>
    <div class="form-group">
        <label for="task_block_id" class=" col-md-2"><?php echo lang('task_block'); ?></label>
        <div class="col-md-10" id="dropdown-apploader-section">
             <?php
            //echo form_dropdown("task_block_id", $task_blocks_dropdown, array($model_info->task_block_id), "class='select2 validate-hidden' id='task_block_id' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        <div class="mt15">
                <?php $this->load->view("includes/color_plate"); ?>
        </div>
        </div>
    </div>   
    <?php } ?>
    <div class="form-group hidden">
        <label for="status_id" class=" col-md-2"><?php echo lang('status'); ?></label>
        <div class="col-md-10">
            <?php
            foreach ($statuses as $status) {
                $task_status[$status->id] = $status->key_name ? lang($status->key_name) : $status->title;
            }

            echo form_dropdown("status_id", $task_status, array($model_info->status_id), "class='select2'");
            ?>
        </div>
    </div>
    <?php $this->load->view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-3", "field_column" => " col-md-9")); ?> 

</div>

<div class="modal-footer">
    <div id="link-of-task-view" class="hide">
        <?php
        echo modal_anchor(get_uri("projects/task_view"), "", array());
        ?>
    </div>
    <?php if($model_info->id){ ?>
    <?php echo js_anchor("<span class=\"fa fa-trash\"></span> ".lang('delete_square'), array('title' => lang('delete_square'), "class" => "delete btn btn-default", "data-id" => $model_info->id, "data-action-url" => get_uri("projects/delete_task"), "data-action" => "delete")); ?>
    <?php } ?>
   <!-- <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button> -->
    <button id="save-and-show-button" type="button" class="btn btn-info"><span class="fa fa-check-circle"></span> <?php echo lang('save_and_show'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>




<script type="text/javascript">
    
    $(document).ready(function () {
       /* $('#ajaxModal').on('shown.bs.modal', function () {
            $(this).find('.modal-dialog').css({width:'auto',
                                       height:'auto', 
                                      'max-height':'100%'});
        });*/
        
        

        //send data to show the task after save
        window.showAddNewModal = false;
        
        $(".delete").click(function (e) {
            window.taskForm.closeModal();
            appLoader.show();
           
            $.ajax({
                    method: "post",
                    url: "<?php echo get_uri("projects/delete_task") ?>",
                    dataType: "json",
                    data: "id="+$(this).attr("data-id"),
                    success: function (result) {  
                       $("#reload-kanban-button").trigger("click");
                       appLoader.hide();
                       appAlert.success(result.message, {duration: 10000});                       
                    }
            });

        });
        
        $("#save-and-show-button").click(function () {
            window.showAddNewModal = true;
            $(this).trigger("submit");

        });
        var taskInfoText = "<?php echo lang('task_info') ?>";

        window.taskForm = $("#task-form").appForm({
            closeModalOnSuccess: false,
            onSuccess: function (result) {
                $("#task-table").appTable({newData: result.data, dataId: result.id});
                $("#reload-kanban-button").trigger("click");

                $("#save_and_show_value").append(result.save_and_show_link);

                if (window.showAddNewModal) {
                    var $taskViewLink = $("#link-of-task-view").find("a");
                    $taskViewLink.attr("data-title", taskInfoText + "#" + result.id);
                    $taskViewLink.attr("data-post-id", result.id);

                    $taskViewLink.trigger("click");
                } else {
                    window.taskForm.closeModal();
                }
            }
        });
        $("#task-form .select2").select2();
        $("#title").focus();

        setDatePicker("#start_date, #end_date, #deadline");

        //load all related data of the selected project
        $("#project_id").select2().on("change", function () {
            var projectId = $(this).val();
            if ($(this).val()) {
                $('#milestone_id').select2("destroy");
                $("#milestone_id").hide();
                $('#assigned_to').select2("destroy");
                $("#assigned_to").hide();
                $('#collaborators').select2("destroy");
                $("#collaborators").hide();
                $('#project_labels').select2("destroy");
                $("#project_labels").hide();
                <?php //Added by GS ?>
                       /*if($("#task_id").val() == '') {
                           $('#task_block_id').select2("destroy");
                           $("#task_block_id").hide();
                       }*/
               
                appLoader.show({container: "#dropdown-apploader-section"});
                $.ajax({
                    url: "<?php echo get_uri("projects/get_all_related_data_of_selected_project") ?>" + "/" + projectId,
                    dataType: "json",
                    success: function (result) {
                        $("#milestone_id").show().val("");
                        $('#milestone_id').select2({data: result.milestones_dropdown});
                        $("#assigned_to").show().val("");
                        $('#assigned_to').select2({data: result.assign_to_dropdown});
                        $("#collaborators").show().val("");
                        $('#collaborators').select2({multiple: true, data: result.collaborators_dropdown});
                        $("#project_labels").show().val("");
                        $('#project_labels').select2({tags: result.label_suggestions});
                        <?php //added by GS ?>
                                 if($("#task_id").val() == '') {
                        /*$("#task_block_id").show().val("");
                       
                        $('#task_block_id').children().remove();
                        $('#task_block_id').select2();
                         $( "#task_block_id" ).attr( "data-rule-required", "true" );
                        $( "#task_block_id" ).attr( "data-msg-required", "This field is required" );
                        
                        $('#task_block_id').append( $('<option></option>').val('').html('-') );
                       $.each(result.task_blocks_dropdown, function(val, text) {
                           if(text != '-')
                            $('#task_block_id').append( $('<option></option>').val(val).html(text) )
                        });*/
                    }
                        appLoader.hide();
                    }
                });
            }
        });

        //intialized select2 dropdown for first time
        $("#project_labels").select2({tags: <?php echo json_encode($label_suggestions); ?>});
        $("#collaborators").select2({multiple: true, data: <?php echo json_encode($collaborators_dropdown); ?>});
        $('#milestone_id').select2({data: <?php echo json_encode($milestones_dropdown); ?>});
        $('#assigned_to').select2({data: <?php echo json_encode($assign_to_dropdown); ?>});
        
        <?php //added by GS ?>
          var current_project_id = '<?php echo $project_id; ?>';
          var task_blocks_arr = <?php echo json_encode($task_blocks_dropdown); ?>;
          var task_blocks_selected = '<?php echo $model_info->task_block_id; ?>';
           /*
           if(!current_project_id &&  $("#task_id").val() == ''){
                $('#task_block_id').children().remove();
                $('#task_block_id').select2();
                $( "#task_block_id" ).attr( "data-rule-required", "true" );
                $( "#task_block_id" ).attr( "data-msg-required", "This field is required" );
                $.each(task_blocks_arr, function(val, text) {
                    $('#task_block_id').append( $('<option></option>').val(val).html(text) )
                });
                if(task_blocks_selected != ''){
                    $('#task_block_id [value='+task_blocks_selected+']').attr('selected', 'true');
                }
            }*/
          
            $('[data-toggle="tooltip"]').tooltip();
        

    });
</script>