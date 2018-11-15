<div id="kanban-wrapper" class="row custom-kanban">
    <?php
    $columns_data = array();
    
    $column_width = (335 * count($tasks)) + 5;

    foreach ($tasks as $task) {

        //$exising_items = get_array_value($columns_data, $task->status_id);
        $exising_items = get_array_value($columns_data, $task->task_block_id);

        if (!$exising_items) {
            $exising_items = "";
        }

        $task_labels = "";
        if ($task->labels) {
            $labels = explode(",", $task->labels);
            foreach ($labels as $label) {
                $task_labels .= "<span class='label label-info'>" . $label . "</span> ";
            }
        }

        if ($task_labels) {
            $task_labels = "<div class='meta'>$task_labels</div>";
        }

        //Edited by GS
        /* $item = $exising_items .  modal_anchor(get_uri("projects/task_view"), 
          "<span class='avatar'>" .
          "<img src='" . get_avatar($task->assigned_to_avatar) . "'>" .
          "</span>" . $task->id . ". " . $task->title .
          $task_labels,
          array("class"=>"kanban-item", "data-id"=>$task->id, "data-project_id"=>$task->project_id, "data-sort"=>$task->new_sort, "data-post-id" => $task->id, "title" => lang('task_info') . " #$task->id",));
         */
        
        $item = modal_anchor(get_uri("projects/task_view"), (trim($task->description) != ''? limit_characters($task->description):lang('no_story_added'))  .
                        $task_labels, array("class" => "kanban-item", "data-id" => $task->id, "data-project_id" => $task->project_id, "data-post-id" => $task->id, "title" => " "));
        
        
        $item_list = '<a href="javascript:void(0);" class="update-task-link" data-id="' . $task->id . '"  data-act="update-task-data" >' . $task->description . '</a>';


        //Edited by GS
        //$columns_data[$task->status_id] = $item;
      /*  $columns_data[$task->task_block_id] = $item;
        $columns_list_data[$task->task_block_id] = $item_list;
        $columns_data_title[$task->task_block_id] = $task->title;*/
        
        $columns_data[$task->id] = $item;
        $columns_list_data[$task->id] = $item_list;
        $columns_data_title[$task->id] = $task->title;
    }
    ?>
    <div class="tab-content">
        <div  role="tabpanel" class="tab-pane active fade"  id="project-tasks-kanban-grid" aria-labelledby="project-tasks-kanban-tab">
            <div class="task-container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="task-list" id="task-list">
                            <ul class="timeline timeline-horizontal" id="timeline-horizontal-list">
                                <?php foreach ($tasks as $column) { ?>
                                    <li class="timeline-item" id="timeline-item-<?php echo $column->id; ?>" data-status_id="<?php echo $column->id; ?>" data-sort="<?php echo  $column->new_sort; ?>"  data-id="<?php echo $column->id; ?>" data-project_id="<?php echo $column->project_id; ?>" data-post-id="<?php echo $column->id;?>" > 
                                        <div class="timeline-badge warning"><i class="glyphicon glyphicon-check"></i></div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading" >
                                                <h4 class="timeline-title"><?php echo $columns_data_title[$column->id]; ?></h4>
                                            </div>
                                            <div class="timeline-body">
                                                <p>
                                                    <?php echo get_array_value($columns_data, $column->id); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    <?php
                                    $style .= "#timeline-item-{$column->id} .timeline-badge, #timeline-item-{$column->id} .timeline-heading{ background: " . ($column->color ? $column->color : "#2e4053") . "}";
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
        <div  role="tabpanel" class="fade tab-pane" id="project-tasks-kanban-list" aria-labelledby="project-tasks-kanban-list-tab">
            <div class="task-container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="task-list-mesh">
                    <div class="timeline-centered">
                        <?php foreach ($tasks as $column) { ?>
                            <article class="timeline-entry" id="timeline-entry-<?php echo $column->id; ?>">

                                <div class="timeline-entry-inner">

                                    <div class="timeline-icon"  style="-moz-box-shadow: 0 0 0 2px <?php echo $column->color ? $column->color : "#2e4053"; ?>; -webkit-box-shadow: 0 0 0 2px <?php echo $column->color ? $column->color : "#2e4053"; ?>; box-shadow: 0 0 0 2px <?php echo $column->color ? $column->color : "#2e4053"; ?>;background: <?php echo $column->color ? $column->color : "#2e4053"; ?>;">
                                        <i class="entypo-feather"></i>
                                    </div>

                                    <div class="timeline-label">
                                        <?php echo get_array_value($columns_list_data, $column->id); ?>
                                    </div>
                                </div>

                            </article>
                            <?php
                            $style .= "#timeline-entry-{$column->id}:before {background: " . ($column->color ? $column->color : "#2e4053") . "}";
                        }
                        ?>
                    </div>
</div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<img id="move-icon" class="hide" src="<?php echo get_file_uri("assets/images/move.png"); ?>" alt="..." />

<script type="text/javascript">


    adjustViewHeightWidth = function () {

        //set wrapper scroll
        if ($("#kanban-wrapper")[0].offsetWidth < $("#kanban-wrapper")[0].scrollWidth) {
            $("#kanban-wrapper").css("overflow-x", "scroll");
        } else {
            $("#kanban-wrapper").css("overflow-x", "hidden");
        }


        //set column scroll 
        $(".container").height($(window).height() - $(".container").offset().top - 30);

        $(".container").each(function (index) {

            //set scrollbar on column... if requred
            if ($(this)[0].offsetHeight < $(this)[0].scrollHeight) {
                $(this).css("overflow-y", "scroll");
            } else {
                $(this).css("overflow-y", "hidden");
            }

        });
    };


    saveStatusAndSort = function ($item, status) {
        appLoader.show();
        //adjustViewHeightWidth();

        var $prev = $item.prev('li'),
                $next = $item.next('li'),
                $last = $item.next('li').next('li'),
                prevSort = 0, nextSort = 0, newSort = 0, lastSort = 0,
                step = 100000, stepDiff = 500,
                id = $item.attr("data-id"),
                project_id = $item.attr("data-project_id");

        if ($prev && $prev.attr("data-sort")) {
            prevSort = $prev.attr("data-sort") * 1;
        }

        if ($next && $next.attr("data-sort")) {
            nextSort = $next.attr("data-sort") * 1;
        }
        
        if ($last && $last.attr("data-sort")) {
            lastSort = $last.attr("data-sort") * 1;
        }


        if (!prevSort && nextSort) {
            //item moved at the top
            newSort = nextSort - stepDiff;

        } else if (!nextSort && prevSort) {
            //item moved at the bottom
            newSort = prevSort + step;

        }/* else if (!lastSort && prevSort && nextSort) {
            //item moved at the bottom
            newSort = (prevSort + nextSort) / 2;

        }*/ else if (prevSort && nextSort) {
            //item moved inside two items
            newSort = (prevSort + nextSort) / 2;

        } else if (!prevSort && !nextSort) {
            //It's the first item of this column
            newSort = step * 100; //set a big value for 1st item
        }

        //newSort = Math.round(newSort);
        
        $item.attr("data-sort", newSort);


        $.ajax({
            url: '<?php echo_uri("projects/save_task_sort") ?>',
            type: "POST",
            data: {id: id, sort: newSort, status_id: status, project_id: project_id},
            success: function () {
                appLoader.hide();
            }
        });

    };



    $(document).ready(function () {

        var isChrome = !!window.chrome && !!window.chrome.webstore;


            var options = {
                animation: 150,
                group: "timeline-horizontal",
                onAdd: function (e) {
                    //moved to another column. update bothe sort and status
                    saveStatusAndSort($(e.item), $(e.item).closest(".timeline-horizontal").attr("data-status_id"));
                },
                onUpdate: function (e) {
                    //updated sort
                    saveStatusAndSort($(e.item));
                }
            };

            //apply only on chrome because this feature is not working perfectly in other browsers.
            if (isChrome) {
                options.setData = function (dataTransfer, dragEl) {
                    var img = document.createElement("img");
                    img.src = $("#move-icon").attr("src");
                    img.style.opacity = 1;
                    dataTransfer.setDragImage(img, 5, 10);
                };

                options.ghostClass = "kanban-sortable-ghost";
                options.chosenClass = "kanban-sortable-chosen";
            }

            Sortable.create($("#timeline-horizontal-list")[0], options);

    });
    

    $(document).ready(function () {

        var isChrome = !!window.chrome && !!window.chrome.webstore;

        // adjustViewHeightWidth();

    });

    $(window).resize(function () {
        //  adjustViewHeightWidth();
    });


</script>
<script type="text/javascript">
    //turn to inline mode
    $.fn.editable.defaults.mode = 'inline';

    $(document).ready(function () {
        $('#ajaxModal').on('shown.bs.modal', function () {
            $(this).find('.modal-dialog').addClass('modal-lg');
        });
        
        $('#kanban-wrapper').on('focus', 'textarea', function () {
            $(this).height(0).height(this.scrollHeight);
        }).find('textarea').change();

        $('body').on('click', '[data-act=update-task-data]', function (e) {
            e.preventDefault();
            $(this).editable({
                type: "textarea",
                pk: 1,
                name: 'status',
                inputclass: 'squarestext',
                onblur: 'submit',
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                rows: 0,
                // value: $(this).attr('data-value'),
                url: '<?php echo_uri("projects/save_task_data") ?>/' + $(this).attr('data-id'),
                showbuttons: false,
                //source: <?php echo json_encode($status_dropdown) ?>,
                success: function (response, newValue) {
                    if (response.success) {
                        //  $("#task-table").appTable({newData: response.data, dataId: response.id});
                    }
                }
            });
            $(this).editable("show");
        });

    });
</script>
<style>
<?php echo $style; ?>
</style>