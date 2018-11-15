<style>.DTTT_container{display:none;}</style>
<div id="page-content" class="p20 clearfix">
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('team_members'); ?></h1>
            <div class="title-button-group">
                
                <?php
                //Edited by GS - allow classroom admins to add or edit their members
                if ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "can_create_members") == "1") {
                    //Edited by GS - disabled send notification
                    //echo modal_anchor(get_uri("team_members/invitation_modal"), "<i class='fa fa-envelope-o'></i> " . lang('send_invitation'), array("class" => "btn btn-default", "title" => lang('send_invitation')));
                    echo modal_anchor(get_uri("team_members/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_team_member'), array("class" => "btn btn-default", "title" => lang('add_team_member')));
                }
                ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="team_member-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var visibleContact = false;
        if ("<?php echo $show_contact_info; ?>") {
            visibleContact = true;
        }

        var visibleDelete = false;
        if ("<?php echo $this->login_user->is_admin; ?>") {
            visibleDelete = true;
        }

        $("#team_member-table").appTable({
            source: '<?php echo_uri("team_members/list_data/") ?>',
            order: [[1, "asc"]],
            radioButtons: [{text: '<?php echo lang("active_members") ?>', name: "status", value: "active", isChecked: true}, {text: '<?php echo lang("inactive_members") ?>', name: "status", value: "inactive", isChecked: false}],
            columns: [
                {title: '', "class": "w50 text-center"},
                {title: "<?php echo lang("name") ?>"},
                {title: "<?php echo lang("job_title") ?>", "class": "w15p"},
                {visible: visibleContact, title: "<?php echo lang("email") ?>", "class": "w20p"},
                {visible: visibleContact, title: "<?php echo lang("phone") ?>", "class": "w15p"}
                <?php echo $custom_field_headers; ?>,
                {visible: visibleDelete, title: '<i class="fa fa-bars"></i>', "class": "text-center option w100"}
            ]

        });
    });
</script>    
