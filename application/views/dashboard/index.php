<div id="page-content" class="p20 clearfix">
    <?php
    announcements_alert_widget();
    ?>
    <div class="row">
        <?php
        $widget_column = "3"; //default bootstrap column class
        $total_hidden = 0;

        if (!$show_attendance) {
            $total_hidden+=1;
        }

        if (!$show_event) {
            $total_hidden+=1;
        }

        if (!$show_timeline) {
            $total_hidden+=1;
        }

        //set bootstrap class for column
        if ($total_hidden == 1) {
            $widget_column = "4";
        } else if ($total_hidden == 2) {
            $widget_column = "6";
        } else if ($total_hidden == 3) {
            $widget_column = "12";
        }
        ?>

        <?php if ($show_attendance) { ?>
            <div class="col-md-<?php echo $widget_column; ?> col-sm-6 widget-container">
                <?php
                clock_widget();
                ?>
            </div>
        <?php } ?>


        <?php if ($show_event) { ?>
            <div class="col-md-<?php echo $widget_column; ?> col-sm-6  widget-container">
                <?php
                events_today_widget();
                ?> 
            </div>
        <?php } ?>

        <?php if ($show_timeline) { ?>
            <div class="col-md-<?php echo $widget_column; ?> col-sm-6  widget-container">
                <?php
                new_posts_widget();
                ?>  
            </div>
        <?php } ?>

    </div>

    <div class="row">
        
        <div class="col-md-12 widget-container">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-clock-o"></i>&nbsp;  <?php echo lang("project_timeline"); ?>
                </div>
                <div id="project-timeline-container">
                    <div class="panel-body"> 
                        <?php
                        activity_logs_widget(array("log_for" => "project", "limit" => 10));
                        ?>
                    </div>
                </div>
            </div>
        </div>

        
    </div>

</div>
<script type="text/javascript">
    $(document).ready(function () {
        var $stickyNote = $("#sticky-note");

        var saveStickyNote = function () {
            $.ajax({
                url: "<?php echo get_uri("dashboard/save_sticky_note") ?>",
                data: {sticky_note: $stickyNote.val()},
                cache: false,
                type: 'POST'
            });
        };

        $stickyNote.change(function () {
            saveStickyNote();
        });

        //save sticky not on window refresh
        $stickyNote.keydown(function () {
            window.onbeforeunload = saveStickyNote;
        });



        initScrollbar('#project-timeline-container', {
            setHeight: 955
        });

    });
</script>    

