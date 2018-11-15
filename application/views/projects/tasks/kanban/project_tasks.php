<div class="panel mb0 bg-none" >
    <!--<div class="tab-title clearfix">
        <h4><?php echo lang('view_story'); ?></h4>
        <div class="title-button-group">
            <?php
            if ($can_create_tasks) {
                echo modal_anchor(get_uri("projects/task_modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_task'), array("class" => "btn btn-default", "title" => lang('add_task'), "data-post-project_id" => $project_id));
            }
            ?>
        </div>
    </div>-->
    
</div>
<div id="load-kanban"></div>
<div class="panel mb0 bg-none" >
    <div class="p15">
        <div class="row">
            <div class="col-md-12 text-center">
                <a class="nav-item nav-link btn btn-default task-btn" href="#project-tasks-kanban-grid" id="project-tasks-kanban-grid-tab" data-toggle="tab" role="tab" aria-controls="project-tasks-kanban-grid" aria-selected="true"><i class="fa fa-minus fa-custom-3x"></i></a>
                       <a class="nav-item nav-link btn btn-default task-btn" data-toggle="modal" href="#downloadModal" id="project-tasks-kanban-export" ><i class="fa fa-chevron-up fa-custom-3x"></i></a>
                
<a class="nav-item nav-link btn btn-default task-btn"  href="#project-tasks-kanban-list" id="project-tasks-kanban-list-tab" data-toggle="tab" role="tab" aria-controls="project-tasks-kanban-list" aria-selected="true"><i class="fa fa-vertical-bar-medium fa-custom-3x"></i></a>
                <button class="btn btn-default" id="reload-kanban-button" style="display: none;"><i class="fa fa-refresh"></i></button> 
               
 <?php
            if ($can_create_tasks) {
                echo modal_anchor(get_uri("projects/task_modal_form"), "<i class='fa fa-plus-circle fa-custom-3x'></i> ", array("class" => "btn btn-default task-btn  add-square-btn", "title" => lang('add_task'), "data-post-project_id" => $project_id));
            }
            ?>
            </div>
            
            <div id="kanban-filters" class="col-md-6"></div>
        </div>
    </div>
</div>
<div id="downloadModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Please select download file type.</h4>
      </div>
      <div class="modal-body">
         <div class="col-md-2 col-md-offset-3">
            <p>
                 <a class="btn btn-default" id="word-download" href="#">WORD</a>
                
            </p>
        </div>
          <div class="col-md-2 col-md-offset-2">
              <p>
                 <a class="btn btn-default" id="pdf-download" href="#">PDF</a>
             
            </p>
          </div>
          <div class=" col-md-4 col-md-offset-4"></div>
      </div>
      <div class="modal-footer" style="border-top:none;">
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">

    $(document).ready(function () {
       var filterDropdown =[];
       
       $( "#word-download" ).click(function(e) {
           e.preventDefault();
           location.href = '<?php echo_uri("projects/project_tasks_kanban_data_export/" . $project_id) ?>';
           $('#downloadModal').modal('hide');
        });
        
        $( "#pdf-download" ).click(function(e) {
           e.preventDefault();
           location.href = '<?php echo_uri("projects/project_tasks_kanban_data_export_pdf/" . $project_id) ?>';
           $('#downloadModal').modal('hide');
        });
       
        /*if("<?php echo $this->login_user->user_type?>"=="staff"){
             filterDropdown = [
                {name: "specific_user_id", class: "w200", options: <?php echo $assigned_to_dropdown; ?>},
                {name: "milestone_id", class: "w200", options: <?php echo $milestone_dropdown; ?>}
            ];
        }else{
             filterDropdown = [
                {name: "milestone_id", class: "w200", options: <?php echo $milestone_dropdown; ?>}
            ];
        }*/
       

        var scrollLeft = 0;
        $("#kanban-filters").appFilters({
            source: '<?php echo_uri("projects/project_tasks_kanban_data/".$project_id) ?>',
            targetSelector: '#load-kanban',
            reloadSelector: "#reload-kanban-button",
            /*search: {name: "search"},*/
            filterDropdown: filterDropdown,
            /*singleDatepicker: [{name: "deadline", defaultText: "<?php echo lang('deadline') ?>",
                    options: [
                        {value: "expired", text: "<?php echo lang('expired') ?>"},
                        {value: moment().format("YYYY-MM-DD"), text: "<?php echo lang('today') ?>"},
                        {value: moment().add(1, 'days').format("YYYY-MM-DD"), text: "<?php echo lang('tomorrow') ?>"},
                        {value: moment().add(7, 'days').format("YYYY-MM-DD"), text: "<?php echo sprintf(lang('in_number_of_days'), 7); ?>"},
                        {value: moment().add(15, 'days').format("YYYY-MM-DD"), text: "<?php echo sprintf(lang('in_number_of_days'), 15); ?>"}
                    ]}],*/
            beforeRelaodCallback: function () {
                scrollLeft = $("#kanban-wrapper").scrollLeft();
            },
            afterRelaodCallback: function () {
                setTimeout(function () {
                    $("#kanban-wrapper").animate({scrollLeft: scrollLeft}, 'slow');
                }, 500);

            }
        });

    });

</script>